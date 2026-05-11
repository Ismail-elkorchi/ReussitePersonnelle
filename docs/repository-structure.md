# Repository Structure

```text
themes/
  reussitepersonnelle/        New block theme.

plugins/
  reussitepersonnelle-core/   Site-specific functionality plugin.

tools/
  sync/                       Read-only production pull scripts.
  local/                      Local import, lint, and smoke-test scripts.
  deploy/                     Future production deploy tooling.

local/
  docker-compose.yml          Local WordPress runtime.
  .env.example                Safe defaults for local environment variables.

docs/
  local-development.md
  repository-structure.md
```

The old Genesis child theme has been preserved as the Git branch `legacy/genesis-theme`.

The `master` branch is now intended to track the new block theme, site plugin, and development tooling only.
