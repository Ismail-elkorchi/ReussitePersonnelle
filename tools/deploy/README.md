# Deployment

Deployment is intentionally not automated yet.

The future deploy path should run the local test suite first, then sync only:

- `themes/reussitepersonnelle/` to production `wp-content/themes/reussitepersonnelle/`
- `plugins/reussitepersonnelle-core/` to production `wp-content/plugins/reussitepersonnelle-core/`

Production database, uploads, WordPress core, server control-panel config, cache directories, and third-party plugins should not be deployed from this repository.
