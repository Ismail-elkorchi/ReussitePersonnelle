#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="$ROOT_DIR/local/.env"

source "$ROOT_DIR/tools/local/docker-access.sh"
rp_reexec_with_docker_group_if_needed "$ROOT_DIR/tools/local/start.sh" "$@"

if [ ! -f "$ENV_FILE" ]; then
	cp "$ROOT_DIR/local/.env.example" "$ENV_FILE"
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

COMPOSE=(docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/local/docker-compose.yml")
source "$ROOT_DIR/tools/local/wordpress-core.sh"

"${COMPOSE[@]}" up -d --force-recreate db wordpress

rp_ensure_wordpress_core_version

"${COMPOSE[@]}" run --rm cli eval 'if ( ! file_exists( WP_PLUGIN_DIR . "/reussitepersonnelle-core/reussitepersonnelle-core.php" ) ) { fwrite( STDERR, "Tracked plugin file is not mounted at wp-content/plugins/reussitepersonnelle-core/reussitepersonnelle-core.php" . PHP_EOL ); exit( 1 ); }'
"${COMPOSE[@]}" run --rm cli plugin activate reussitepersonnelle-core --quiet
"${COMPOSE[@]}" run --rm cli theme activate reussitepersonnelle --quiet
"${COMPOSE[@]}" run --rm cli cache flush >/dev/null 2>&1 || true

printf 'Local site is ready at %s\n' "$LOCAL_URL"
