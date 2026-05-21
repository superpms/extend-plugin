# Reference: Package Surface

This page lists the public and integration-facing surface of `superpms/extend-plugins`.

## Composer Package

Package name:

```text
superpms/extend-plugins
```

Autoload:

- `bin/autoload.php`
- PSR-4 `pms\\` to `src/pms/`

Runtime requirement:

- PHP `>=8.1`

Development requirements:

- `superpms/basic`
- `superpms/interpreter-terminal`

## Files

```text
bin/autoload.php
bin/autorun.php
src/pms/extend/plugins/Setup.php
src/pms/extend/plugins/PluginInstallCommand.php
src/pms/app/PluginsApp.php
ide-helper/pms/program/path/Driver.php
```

## Classes and Trait

### pms\extend\plugins\Setup

Boot lifecycle module.

Method:

```php
public static function entry(string $rootPath): void
```

Side effects:

- Mounts `pluginsRoot`.
- Creates `installed.php` when missing.
- Includes enabled plugin `autoload.php` files.

### pms\extend\plugins\PluginInstallCommand

Terminal command module.

Command:

```text
plugin-install
```

Argument:

```text
name
```

Side effects:

- Reads `<pluginsRoot>/<name>/plugin.json`.
- Appends `<name>` to `<pluginsRoot>/installed.php` when validation passes and `autoload.php` exists.

### pms\app\PluginsApp

Trait for plugin classes.

Methods:

```php
final protected static function config(?string $name = null, $default = null)
final protected static function path(?string $suffix = null): string
```

Side effects:

- Caches plugin config by derived plugin name.

## Dynamic Path Method

The package's IDE-helper file declares:

```php
Path::getPluginsRoot(string|array $suffix1 = "", ...$suffix2)
```

This method is provided at runtime by `pms\program\path\Driver::__call(...)` after `Setup` mounts `pluginsRoot`.

## Hook Dependencies

`autorun.php` uses:

- `pms\hook\LifecycleHook`
- `pms\hook\TerminalCommandHook`

Both are optional at registration time because `autorun.php` checks `class_exists(...)`.

## Global Helper Dependencies

The implementation relies on framework helpers and constants provided by other PMSPHP packages:

- `path_join(...)`
- `file_create(...)`
- `array_chain(...)`
- `LIFECYCLE_BOOT`
- `COMMAND_ARGUMENT_TYPE`
- Terminal color constants used by command output.

This package is therefore not a standalone generic plugin loader; it is a PMSPHP framework extension package.
