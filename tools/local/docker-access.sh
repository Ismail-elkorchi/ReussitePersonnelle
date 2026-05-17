#!/usr/bin/env bash

rp_user_is_in_docker_group() {
	getent group docker 2>/dev/null \
		| cut -d: -f4 \
		| tr ',' '\n' \
		| grep -Fxq "$(id -un)"
}

rp_reexec_with_docker_group_if_needed() {
	if [ "${RP_DOCKER_GROUP_SESSION:-0}" = "1" ]; then
		return
	fi

	if ! command -v docker >/dev/null 2>&1; then
		return
	fi

	if docker info >/dev/null 2>&1; then
		return
	fi

	if ! rp_user_is_in_docker_group || ! command -v sg >/dev/null 2>&1; then
		return
	fi

	local script_path="$1"
	shift

	local quoted_workdir quoted_script raw_arg quoted_arg command_line
	printf -v quoted_workdir '%q' "$PWD"
	printf -v quoted_script '%q' "$script_path"
	command_line="cd $quoted_workdir && $quoted_script"

	for raw_arg in "$@"; do
		printf -v quoted_arg '%q' "$raw_arg"
		command_line+=" $quoted_arg"
	done

	export RP_DOCKER_GROUP_SESSION=1
	exec sg docker -c "$command_line"
}
