# superpms/extend-plugins

`superpms/extend-plugins` is the plugin loader package for PMSPHP. It mounts the project plugin root during framework boot, loads enabled plugin `autoload.php` files from `plugins/installed.php`, registers the `plugin-install` terminal command, and provides the `PluginsApp` trait used by plugin classes to read their own config and paths.

## Runtime Position

In the current server project, this package is required by `server/composer.json` and participates in boot through Composer `autoload.files`:

1. `bin/autoload.php` requires `bin/autorun.php`.
2. `bin/autorun.php` mounts `pms\extend\plugins\Setup` on `LIFECYCLE_BOOT` when `LifecycleHook` is available.
3. `bin/autorun.php` mounts `pms\extend\plugins\PluginInstallCommand` when `TerminalCommandHook` is available.
4. `Setup` reads `boot.json` `extend.plugins`, mounts `pluginsRoot`, then loads enabled plugin autoload files.

The package does not scan every plugin directory automatically. Runtime loading is controlled by `plugins/installed.php`.

## Main Capabilities

- Mount the plugin root path with `Path::mount('pluginsRoot', ...)`.
- Create a default `plugins/installed.php` if it is missing.
- Include each enabled plugin's `autoload.php`.
- Validate and install a plugin name through the `plugin-install` terminal command.
- Expose plugin-local `config()` and `path()` helpers through the `PluginsApp` trait.
- Add IDE-helper metadata for `Path::getPluginsRoot(...)`.

## Documentation

Detailed developer documentation lives under [docs/README.md](docs/README.md).

- [Architecture](docs/architecture/runtime-loading.md)
- [Modules](docs/modules/setup.md)
- [How To](docs/how-to/create-and-install-plugin.md)
- [Reference](docs/reference/plugin-files.md)

## Install

```bash
composer require superpms/extend-plugins
```

## Requirements

- PHP `>=8.1`
- Runtime integration with `superpms/basic`
- Terminal command integration requires `superpms/interpreter-terminal`

The current `server` project requires PHP `>=8.2`, but this package's own Composer constraint is PHP `>=8.1`.

## Development Notes

- Keep plugin runtime behavior in this package separate from plugin business implementation.
- Treat `installed.php` as the loading whitelist.
- Treat `plugin.json` as install-time metadata and validation input.
- Do not add business-specific behavior to this package; concrete plugins live under the consuming project's `plugins/` directory.

## License

Apache-2.0. See [LICENSE.txt](LICENSE.txt).
