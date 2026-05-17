#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="$ROOT_DIR/local/.env"

if [ ! -f "$ENV_FILE" ]; then
	cp "$ROOT_DIR/local/.env.example" "$ENV_FILE"
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

COMPOSE=(docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/local/docker-compose.yml")

"${COMPOSE[@]}" up -d --force-recreate db wordpress

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

"${COMPOSE[@]}" run --rm cli eval 'if ( ! file_exists( WP_PLUGIN_DIR . "/reussitepersonnelle-core/reussitepersonnelle-core.php" ) ) { fwrite( STDERR, "Tracked plugin file is not mounted at wp-content/plugins/reussitepersonnelle-core/reussitepersonnelle-core.php" . PHP_EOL ); exit( 1 ); }'
"${COMPOSE[@]}" run --rm cli plugin activate reussitepersonnelle-core --quiet
"${COMPOSE[@]}" run --rm cli theme activate reussitepersonnelle --quiet
"${COMPOSE[@]}" run --rm cli cache flush >/dev/null 2>&1 || true

printf 'Local site is ready at %s\n' "$LOCAL_URL"
