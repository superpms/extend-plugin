# extend-plugins Developer Documentation

This directory documents the `superpms/extend-plugins` Composer package for framework and plugin developers. It is organized by concern rather than as a flat set of notes.

## Reading Order

1. [Architecture: runtime loading](architecture/runtime-loading.md)
2. [Architecture: server integration](architecture/server-integration.md)
3. [Modules: Setup](modules/setup.md)
4. [Modules: PluginInstallCommand](modules/plugin-install-command.md)
5. [Modules: PluginsApp](modules/plugins-app.md)
6. [How to create and install a plugin](how-to/create-and-install-plugin.md)
7. [Reference: plugin files](reference/plugin-files.md)
8. [Reference: package surface](reference/package-surface.md)

## Scope

These docs describe the Composer package itself: boot hooks, plugin root mounting, loading rules, command behavior, helper trait behavior, and development conventions. They do not describe the business behavior of individual plugins.

## Current Server Facts Used

- `server/composer.json` requires `superpms/extend-plugins`.
- `server/boot.json` declares `extend.plugins` as `/plugins`.
- `server/plugins/installed.php` is the runtime loading whitelist.
- Existing server plugins use namespaces like `plugins\superpms\login` and plugin names like `superpms/login`.
- Existing server plugin classes use `pms\app\PluginsApp` to read plugin-local config.
