#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="$ROOT_DIR/local/.env"
ENV_EXAMPLE="$ROOT_DIR/local/.env.example"
COMPOSE_FILE="$ROOT_DIR/local/docker-compose.yml"
DUMP_PATH="${1:-$ROOT_DIR/.local/backups/latest.sql}"

if [ ! -f "$ENV_FILE" ]; then
	cp "$ENV_EXAMPLE" "$ENV_FILE"
	printf 'Created %s from example defaults.\n' "$ENV_FILE"
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
	cat <<'MSG'
Docker and Docker Compose are required before importing the production clone.
Run `npm run local:check` for the exact Ubuntu package command.
MSG
	exit 1
fi

if [ ! -f "$DUMP_PATH" ]; then
	printf 'Database dump not found: %s\n' "$DUMP_PATH" >&2
	printf 'Run `npm run local:pull` first, or pass a dump path to this script.\n' >&2
	exit 1
fi

LOCAL_TABLE_PREFIX="${LOCAL_TABLE_PREFIX:-}"
if [ -z "$LOCAL_TABLE_PREFIX" ]; then
	LOCAL_TABLE_PREFIX="$(
		grep -m1 -E 'CREATE TABLE `[^`]+_options`' "$DUMP_PATH" \
			| sed -E 's/.*CREATE TABLE `([^`]+)_options`.*/\1_/'
	)"
fi

if [ -z "$LOCAL_TABLE_PREFIX" ]; then
	printf 'Could not determine WordPress table prefix from %s\n' "$DUMP_PATH" >&2
	printf 'Set LOCAL_TABLE_PREFIX in local/.env and retry.\n' >&2
	exit 1
fi

export LOCAL_TABLE_PREFIX

COMPOSE=(docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE")

printf 'Starting local WordPress services...\n'
"${COMPOSE[@]}" up -d --force-recreate db wordpress

printf 'Waiting for MariaDB...\n'
db_ready=0
for _ in $(seq 1 60); do
	if "${COMPOSE[@]}" exec -T db mariadb-admin ping -h127.0.0.1 -uroot "-p${LOCAL_DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; then
		db_ready=1
		break
	fi
	sleep 2
done

if [ "$db_ready" -ne 1 ]; then
	printf 'MariaDB was not ready in time.\n' >&2
	exit 1
fi

printf 'Waiting for WordPress core files...\n'
wp_ready=0
for _ in $(seq 1 60); do
	if "${COMPOSE[@]}" run --rm cli core version >/dev/null 2>&1; then
		wp_ready=1
		break
	fi
	sleep 2
done

if [ "$wp_ready" -ne 1 ]; then
	printf 'WordPress core files were not ready in time.\n' >&2
	exit 1
fi

printf 'Setting local WordPress table prefix to %s...\n' "$LOCAL_TABLE_PREFIX"
"${COMPOSE[@]}" run --rm --user root cli config set table_prefix "$LOCAL_TABLE_PREFIX" --type=variable --quiet

printf 'Resetting local database %s...\n' "$LOCAL_DB_NAME"
"${COMPOSE[@]}" exec -T db mariadb -uroot "-p${LOCAL_DB_ROOT_PASSWORD}" <<SQL
DROP DATABASE IF EXISTS \`${LOCAL_DB_NAME}\`;
CREATE DATABASE \`${LOCAL_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${LOCAL_DB_NAME}\`.* TO '${LOCAL_DB_USER}'@'%';
FLUSH PRIVILEGES;
SQL

printf 'Importing %s...\n' "$DUMP_PATH"
"${COMPOSE[@]}" exec -T db mariadb -u"$LOCAL_DB_USER" "-p${LOCAL_DB_PASSWORD}" "$LOCAL_DB_NAME" < "$DUMP_PATH"

printf 'Refreshing local Yoast SEO package...\n'
if ! "${COMPOSE[@]}" run --rm --user root cli --skip-plugins=wordpress-seo plugin install wordpress-seo --version=27.5 --force --quiet; then
	printf 'Warning: could not refresh Yoast SEO locally; continuing with copied production files.\n' >&2
fi

printf 'Rewriting production URLs to %s...\n' "$LOCAL_URL"
"${COMPOSE[@]}" run --rm cli search-replace "$PRODUCTION_URL" "$LOCAL_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only
"${COMPOSE[@]}" run --rm cli search-replace "https://www.reussitepersonnelle.com" "$LOCAL_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only
"${COMPOSE[@]}" run --rm cli option update home "$LOCAL_URL"
"${COMPOSE[@]}" run --rm cli option update siteurl "$LOCAL_URL"

printf 'Adjusting local-only plugin state...\n'
"${COMPOSE[@]}" run --rm cli plugin deactivate wp-super-cache --quiet || true
"${COMPOSE[@]}" run --rm cli plugin deactivate ga4-analytics --quiet || true
"${COMPOSE[@]}" run --rm cli eval 'if ( ! file_exists( WP_PLUGIN_DIR . "/reussitepersonnelle-core/reussitepersonnelle-core.php" ) ) { fwrite( STDERR, "Tracked plugin file is not mounted at wp-content/plugins/reussitepersonnelle-core/reussitepersonnelle-core.php" . PHP_EOL ); exit( 1 ); }'
"${COMPOSE[@]}" run --rm cli plugin activate reussitepersonnelle-core --quiet
"${COMPOSE[@]}" run --rm cli theme activate reussitepersonnelle --quiet
"${COMPOSE[@]}" run --rm cli cache flush || true

printf 'Local clone is ready at %s\n' "$LOCAL_URL"
