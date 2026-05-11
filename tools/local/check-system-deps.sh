#!/usr/bin/env bash
set -euo pipefail

missing=0
missing_packages=0

check() {
	local command_name="$1"
	local package_hint="$2"

	if ! command -v "$command_name" >/dev/null 2>&1; then
		printf 'missing: %s (%s)\n' "$command_name" "$package_hint"
		missing=1
		missing_packages=1
	else
		printf 'ok: %s -> %s\n' "$command_name" "$(command -v "$command_name")"
	fi
}

check docker docker.io
check node nodejs
check npm npm
check rsync rsync
check ssh openssh-client

if ! docker compose version >/dev/null 2>&1; then
	printf 'missing: docker compose (docker-compose-plugin)\n'
	missing=1
	missing_packages=1
else
	printf 'ok: docker compose -> %s\n' "$(docker compose version --short)"
fi

if command -v docker >/dev/null 2>&1; then
	if docker info >/dev/null 2>&1; then
		printf 'ok: docker daemon access\n'
	elif id -nG | grep -qw docker; then
		printf 'error: docker CLI is installed, but the daemon is not reachable from this shell.\n'
		printf 'Check that the Docker service is running: sudo systemctl status docker\n'
		missing=1
	elif getent group docker | grep -q "$(id -un)"; then
		printf 'error: your user is in the docker group, but this shell has not refreshed its groups.\n'
		printf 'Run `newgrp docker`, or log out and back in, then retry.\n'
		missing=1
	else
		printf 'error: docker CLI is installed, but this user cannot access the Docker daemon.\n'
		printf 'Run: sudo usermod -aG docker "$USER"\n'
		missing=1
	fi
fi

if [ "$missing" -ne 0 ]; then
	if [ "$missing_packages" -ne 0 ]; then
	cat <<'MSG'

Install the missing system packages on Ubuntu with:

sudo apt-get update
sudo apt-get install -y docker.io docker-compose-plugin rsync openssh-client
sudo systemctl enable --now docker
sudo usermod -aG docker "$USER"

After adding your user to the docker group, log out and back in, or run `newgrp docker`.
MSG
	fi
	exit 1
fi
