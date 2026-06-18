# Local Development

This repository uses Docker Compose for the local production clone. The goal is to mirror the important production versions: WordPress 7.0, PHP 8.4, and MariaDB 10.11.

## System Dependencies

Install the system packages with your local sudo password:

```bash
sudo apt-get update
sudo apt-get install -y docker.io docker-compose-plugin rsync openssh-client
sudo systemctl enable --now docker
sudo usermod -aG docker "$USER"
```

After adding your user to the `docker` group, log out and back in, or run:

```bash
newgrp docker
```

The repository npm scripts also detect this stale-session state and re-run themselves through the `docker` group when possible. A plain command such as `docker ps` still needs a refreshed shell.

Then verify:

```bash
npm run local:check
```

## First Clone

Create `local/.env` from the example and fill in the private production sync values:

```bash
cp local/.env.example local/.env
```

The real SSH target, server path, and remote database dump command belong only in ignored `local/.env`. The dump command must write SQL to stdout.

Pull a read-only copy of production files and database:

```bash
npm run local:pull
```

Import that copy into the local Docker environment:

```bash
npm run local:import
```

The local site will be available at:

```text
http://localhost:8080
```

phpMyAdmin is available only when the `tools` profile is enabled:

```bash
sg docker -c 'docker compose --env-file local/.env -f local/docker-compose.yml --profile tools up -d phpmyadmin'
```

Then open:

```text
http://localhost:8081
```

## Local Files

Production copies are stored under `.local/` and ignored by Git:

```text
.local/prod/wp-content/
.local/backups/
```

The repository-owned theme and plugin are mounted over the production copy:

```text
themes/reussitepersonnelle/
plugins/reussitepersonnelle-core/
```

## WordPress Core Version

The local stack keeps WordPress core files in the `wp-core` Docker volume. When `WORDPRESS_VERSION` changes, the local scripts check the running core version and reset only that core volume if it is stale. The local database, uploads, production plugin copies, and repository-owned theme/plugin mounts are preserved.

## Tests

Run PHP linting:

```bash
npm run lint:php
```

Run the local smoke test after importing production:

```bash
npm run test:smoke
```
