# ReussitePersonnelle

Development workspace for `reussitepersonnelle.com`.

This repository now tracks:

- `themes/reussitepersonnelle/`: the new WordPress block theme.
- `plugins/reussitepersonnelle-core/`: site-specific functionality.
- `tools/`: local clone, test, and future deployment scripts.
- `local/`: Docker Compose configuration for a production-like local WordPress runtime.

The previous Genesis child theme is preserved in the Git branch:

```text
legacy/genesis-theme
```

Start with:

```bash
npm install
npm run local:check
```

See [docs/local-development.md](docs/local-development.md) for the full local clone workflow.
