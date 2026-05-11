#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

if command -v php >/dev/null 2>&1; then
	find "$ROOT_DIR/themes" "$ROOT_DIR/plugins" -name '*.php' -print0 | xargs -0 -n1 php -l
	exit 0
fi

if command -v docker >/dev/null 2>&1 && docker compose version >/dev/null 2>&1; then
	if [ ! -f "$ROOT_DIR/local/.env" ]; then
		cp "$ROOT_DIR/local/.env.example" "$ROOT_DIR/local/.env"
	fi

	docker compose --env-file "$ROOT_DIR/local/.env" -f "$ROOT_DIR/local/docker-compose.yml" run --rm --entrypoint sh cli -lc \
		'find wp-content/themes/reussitepersonnelle wp-content/plugins/reussitepersonnelle-core -name "*.php" -print0 | xargs -0 -n1 php -l'
	exit 0
fi

cat <<'MSG' >&2
Cannot lint PHP: neither local php nor Docker Compose is available.
Run `npm run local:check` for the required system packages.
MSG
exit 1
