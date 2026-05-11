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

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
	cat <<'MSG'
Docker and Docker Compose are required for smoke tests.
Run `npm run local:check` for the exact Ubuntu package command.
MSG
	exit 1
fi

COMPOSE=(docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/local/docker-compose.yml")

"${COMPOSE[@]}" up -d db wordpress
curl -fsSI "$LOCAL_URL" >/dev/null
"${COMPOSE[@]}" run --rm cli core version
"${COMPOSE[@]}" run --rm cli plugin is-active reussitepersonnelle-core
"${COMPOSE[@]}" run --rm cli theme list --format=table

printf 'Smoke tests passed for %s\n' "$LOCAL_URL"
