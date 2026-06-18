#!/usr/bin/env bash

rp_expected_wordpress_version() {
	printf '%s\n' "${WORDPRESS_VERSION:-7.0}"
}

rp_wait_for_wordpress_core() {
	local wp_ready=0

	for _ in $(seq 1 60); do
		if "${COMPOSE[@]}" run --rm cli core version >/dev/null 2>&1; then
			wp_ready=1
			break
		fi
		sleep 2
	done

	if [ "$wp_ready" -ne 1 ]; then
		printf 'WordPress core files were not ready in time.\n' >&2
		return 1
	fi
}

rp_wordpress_core_version() {
	"${COMPOSE[@]}" run --rm cli core version
}

rp_wordpress_core_volume_name() {
	local wordpress_container

	wordpress_container="$("${COMPOSE[@]}" ps -q wordpress)"
	if [ -z "$wordpress_container" ]; then
		return 1
	fi

	docker inspect "$wordpress_container" --format '{{range .Mounts}}{{if eq .Destination "/var/www/html"}}{{.Name}}{{end}}{{end}}'
}

rp_reset_wordpress_core_volume() {
	local volume_name="$1"

	"${COMPOSE[@]}" stop wordpress >/dev/null
	"${COMPOSE[@]}" rm --force --stop wordpress >/dev/null
	docker volume rm "$volume_name" >/dev/null
	"${COMPOSE[@]}" up -d --force-recreate wordpress
}

rp_ensure_wordpress_core_version() {
	local expected_version current_version volume_name

	expected_version="$(rp_expected_wordpress_version)"
	rp_wait_for_wordpress_core

	current_version="$(rp_wordpress_core_version)"
	case "$current_version" in
		"$expected_version"*)
			printf 'Local WordPress core is %s.\n' "$current_version"
			return 0
			;;
	esac

	volume_name="$(rp_wordpress_core_volume_name)"
	if [ -z "$volume_name" ]; then
		printf 'Could not determine the local WordPress core Docker volume name.\n' >&2
		return 1
	fi

	printf 'Local WordPress core is %s; resetting core volume for %s.\n' "$current_version" "$expected_version"
	printf 'Only the wp-core volume is reset. The local database and production content copy are preserved.\n'

	rp_reset_wordpress_core_volume "$volume_name"
	rp_wait_for_wordpress_core

	current_version="$(rp_wordpress_core_version)"
	case "$current_version" in
		"$expected_version"*)
			printf 'Local WordPress core is now %s.\n' "$current_version"
			;;
		*)
			printf 'Expected local WordPress %s, found %s after resetting wp-core.\n' "$expected_version" "$current_version" >&2
			return 1
			;;
	esac
}
