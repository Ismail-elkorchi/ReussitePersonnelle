#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="$ROOT_DIR/local/.env"

source "$ROOT_DIR/tools/local/docker-access.sh"
rp_reexec_with_docker_group_if_needed "$ROOT_DIR/tools/local/stop.sh" "$@"

if [ ! -f "$ENV_FILE" ]; then
	cp "$ROOT_DIR/local/.env.example" "$ENV_FILE"
fi

docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/local/docker-compose.yml" down
