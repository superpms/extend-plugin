# Runtime Loading Architecture

`extend-plugins` is loaded by Composer before the PMSPHP `Boot` object reaches the framework boot lifecycle.

## Composer Entry

The package declares this autoload file in `composer.json`:

```json
{
  "autoload": {
    "files": [
      "bin/autoload.php"
    ],
    "psr-4": {
      "pms\\": "src/pms/"
    }
  }
}
```

`bin/autoload.php` only requires `bin/autorun.php`. The actual integration lives in `autorun.php`.

## Hook Registration

`bin/autorun.php` conditionally registers two integrations:

- `LifecycleHook::mount(LIFECYCLE_BOOT, Setup::class)`
- `TerminalCommandHook::mount(PluginInstallCommand::class)`

Both registrations are guarded with `class_exists(...)`. This lets the package be present before the corresponding framework hook classes are available, but in the server project both are available through the required framework packages.

## Boot-Time Sequence

The server boot chain is:

1. `server/public/index.php` or `server/pms` loads `vendor/autoload.php`.
2. Composer loads `extend-plugins/bin/autoload.php`.
3. `autorun.php` mounts `Setup` to `LIFECYCLE_BOOT`.
4. `pms\Boot` reads `server/boot.json` and initializes `BootOptions`.
5. `pms\Boot` runs `LifecycleHook::run(LIFECYCLE_BOOT, $rootPath)`.
6. `Setup::entry($rootPath)` mounts the plugin root and loads installed plugin autoload files.

## Plugin Root Resolution

`Setup` calculates the plugin root with:

```php
$pluginDir = path_join($rootPath, BootOptions::get_extend('plugins', '/plugins'));
Path::mount('pluginsRoot', $pluginDir);
```

In the current server project, `server/boot.json` contains:

```json
{
  "extend": {
    "plugins": "/plugins",
    "kits": "/kits"
  }
}
```

That makes the plugin root `server/plugins`.

`Path::mount(...)` stores the path under an uppercase key and creates the directory when missing. After mounting, callers can use `Path::getPluginsRoot(...)`.

## Loading Rule

Runtime loading is whitelist-based:

1. `Setup` reads `Path::getPluginsRoot('installed.php')`.
2. If the file does not exist, `Setup` creates a default file returning an empty array.
3. If the file exists, it must return an array of plugin names.
4. For each plugin name, `Setup` checks `<pluginsRoot>/<plugin>/autoload.php`.
5. Existing autoload files are loaded with `include_once`.

Directory presence alone does not enable a plugin. A plugin directory is loaded only when its name appears in `installed.php` and its `autoload.php` exists.

## Boundaries

This package does not:

- Recursively scan all plugin directories.
- Validate every field in `plugin.json`.
- Register HTTP middleware itself.
- Interpret plugin business features.
- Copy plugin files into a project during Composer install.

Those behaviors belong to the consuming project, individual plugins, or other framework packages.
