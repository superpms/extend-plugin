# How To Create and Install a Plugin

This guide describes the package-level plugin contract. It does not define any business behavior for a plugin.

## 1. Choose the Plugin Name

Plugin names use two slash-separated parts:

```text
<vendor>/<plugin>
```

Example:

```text
superpms/login
```

The matching PHP namespace should use:

```text
plugins\<vendor>\<plugin>\...
```

Example:

```php
namespace plugins\superpms\login\business;
```

This namespace shape is required for `PluginsApp` to derive the correct plugin name.

## 2. Create the Directory

Create the plugin directory under the project plugin root:

```text
server/plugins/<vendor>/<plugin>/
```

The current server plugin root is configured by `server/boot.json`:

```json
{
  "extend": {
    "plugins": "/plugins"
  }
}
```

## 3. Add plugin.json

Create:

```text
server/plugins/<vendor>/<plugin>/plugin.json
```

Minimum installable shape:

```json
{
  "name": "vendor/plugin"
}
```

The install command currently enforces only:

- The file exists.
- The JSON can be decoded.
- `name` exists.
- `name` matches the directory name passed to `plugin-install`.

Existing server plugins also use metadata fields such as `description`, `type`, `version`, `config`, `require`, `useProperty`, `useAppFile`, `useAppFolder`, `useRequestHeader`, and `useConst`. Those fields are conventions visible in current plugins, not hard validation rules inside `extend-plugins`.

## 4. Add autoload.php When Startup Symbols Are Needed

Create:

```text
server/plugins/<vendor>/<plugin>/autoload.php
```

Use this file for startup symbols that must exist after plugin loading, such as constants or small helper definitions.

`Setup` only includes plugin `autoload.php` files listed through `installed.php`. `PluginInstallCommand` only appends a plugin to `installed.php` if this file exists.

## 5. Add config.php When Plugin Classes Need Config

Create:

```text
server/plugins/<vendor>/<plugin>/config.php
```

Return a PHP array:

```php
<?php
return [
    'key' => 'value',
];
```

Plugin classes that use `PluginsApp` can read this file with:

```php
self::config('key', 'default');
```

## 6. Use PluginsApp in Plugin Classes

For classes under the plugin namespace:

```php
namespace plugins\vendor\plugin\service;

use pms\app\PluginsApp;

class ExampleService
{
    use PluginsApp;

    public static function value(): string
    {
        return self::config('key', 'default');
    }
}
```

## 7. Install the Plugin

Run the terminal command from the server project:

```bash
php pms plugin-install vendor/plugin
```

The command validates `plugin.json`, checks for `autoload.php`, appends the plugin name to `plugins/installed.php`, and prints the plugin path.

## 8. Verify Loading

After boot, verify:

- `server/plugins/installed.php` contains the plugin name.
- `server/plugins/<vendor>/<plugin>/autoload.php` exists.
- Any constants or helper definitions from `autoload.php` are available.
- Any plugin class using `PluginsApp` can read `config.php`.

## Common Failure Points

- `plugin-install` says the name is incorrect: the name is not exactly two slash-separated parts.
- `plugin-install` says the project is unsupported: `plugin.json` is missing.
- `plugin-install` says plugin info is incorrect: JSON failed to decode or has no `name`.
- `plugin-install` says name and directory do not match: `plugin.json.name` differs from the command argument.
- Plugin directory exists but startup symbols are missing: the plugin is not in `installed.php` or has no `autoload.php`.
