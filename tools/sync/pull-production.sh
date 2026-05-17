#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

ENV_FILE="$ROOT_DIR/local/.env"

if [ -f "$ENV_FILE" ]; then
	set -a
	# shellcheck disable=SC1090
	source "$ENV_FILE"
	set +a
fi

SSH_TARGET="${RP_SSH_TARGET:-}"
DOMAIN="${RP_DOMAIN:-reussitepersonnelle.com}"
REMOTE_DOCROOT="${RP_REMOTE_DOCROOT:-}"
REMOTE_DB_DUMP_COMMAND="${RP_REMOTE_DB_DUMP_COMMAND:-}"

missing=0
for var_name in SSH_TARGET REMOTE_DOCROOT REMOTE_DB_DUMP_COMMAND; do
	if [ -z "${!var_name}" ]; then
		printf 'Missing required private setting: %s\n' "$var_name" >&2
		missing=1
	fi
done

if [ "$missing" -ne 0 ]; then
	cat <<'MSG' >&2

Set the private production sync values in ignored local/.env before running local:pull.
Use local/.env.example as the template.
MSG
	exit 1
fi

LOCAL_CONTENT_DIR="$ROOT_DIR/.local/prod/wp-content"
BACKUP_DIR="$ROOT_DIR/.local/backups"
STAMP="$(date +%Y%m%d-%H%M%S)"
DB_DUMP="$BACKUP_DIR/reussitepersonnelle-production-$STAMP.sql"

mkdir -p \
	"$LOCAL_CONTENT_DIR/uploads" \
	"$LOCAL_CONTENT_DIR/plugins" \
	"$LOCAL_CONTENT_DIR/themes" \
	"$LOCAL_CONTENT_DIR/mu-plugins" \
	"$LOCAL_CONTENT_DIR/languages" \
	"$BACKUP_DIR"

printf 'Pulling production wp-content for %s from %s\n' "$DOMAIN" "$SSH_TARGET"

rsync -az --delete \
	--exclude='cache/' \
	--exclude='upgrade/' \
	--exclude='upgrade-temp-backup/' \
	"$SSH_TARGET:$REMOTE_DOCROOT/wp-content/uploads/" \
	"$LOCAL_CONTENT_DIR/uploads/"

if [ -w "$LOCAL_CONTENT_DIR/plugins" ]; then
	rsync -az --delete \
		--exclude='cache/' \
		"$SSH_TARGET:$REMOTE_DOCROOT/wp-content/plugins/" \
		"$LOCAL_CONTENT_DIR/plugins/"
else
	printf 'Warning: skipping production plugin mirror because %s is not writable.\n' "$LOCAL_CONTENT_DIR/plugins" >&2
fi

if [ -w "$LOCAL_CONTENT_DIR/themes" ]; then
	rsync -az --delete \
		"$SSH_TARGET:$REMOTE_DOCROOT/wp-content/themes/" \
		"$LOCAL_CONTENT_DIR/themes/"
else
	printf 'Warning: skipping production theme mirror because %s is not writable.\n' "$LOCAL_CONTENT_DIR/themes" >&2
fi

rsync -az --delete \
	"$SSH_TARGET:$REMOTE_DOCROOT/wp-content/mu-plugins/" \
	"$LOCAL_CONTENT_DIR/mu-plugins/"

rsync -az --delete \
	"$SSH_TARGET:$REMOTE_DOCROOT/wp-content/languages/" \
	"$LOCAL_CONTENT_DIR/languages/"

printf 'Dumping production database with configured remote command\n'
ssh "$SSH_TARGET" "$REMOTE_DB_DUMP_COMMAND" > "$DB_DUMP"

ln -sfn "$(basename "$DB_DUMP")" "$BACKUP_DIR/latest.sql"

printf 'Production pull complete.\n'
printf 'Database dump: %s\n' "$DB_DUMP"
printf 'Latest symlink: %s\n' "$BACKUP_DIR/latest.sql"
