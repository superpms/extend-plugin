# Reference: Plugin Files

This reference describes files under the consuming project's plugin root.

## Plugin Root

The plugin root is resolved from:

```php
path_join($rootPath, BootOptions::get_extend('plugins', '/plugins'))
```

In the current server project this is:

```text
server/plugins
```

## installed.php

Path:

```text
server/plugins/installed.php
```

Purpose:

- Runtime loading whitelist.
- Read by `Setup` during `LIFECYCLE_BOOT`.
- Written by `PluginInstallCommand` when a plugin with `autoload.php` is installed.

Shape:

```php
<?php
return [
    'vendor/plugin'
];
```

Each array item is resolved to:

```text
server/plugins/vendor/plugin/autoload.php
```

## plugin.json

Path:

```text
server/plugins/vendor/plugin/plugin.json
```

Purpose:

- Install-time metadata.
- Minimal validation input for `plugin-install`.

Required by `PluginInstallCommand`:

```json
{
  "name": "vendor/plugin"
}
```

Fields visible in current server plugin examples:

- `name`
- `description`
- `type`
- `version`
- `config`
- `require`
- `useProperty`
- `useAppFile`
- `useAppFolder`
- `useRequestHeader`
- `useConst`

Only `name` is currently enforced by the package.

## autoload.php

Path:

```text
server/plugins/vendor/plugin/autoload.php
```

Purpose:

- Runtime startup file included by `Setup`.
- Suitable for constants and startup-only definitions.

Rules:

- Loaded only when the plugin name appears in `installed.php`.
- Loaded with `include_once`.
- Missing files are skipped by `Setup`.
- Missing files prevent `PluginInstallCommand` from writing the plugin into `installed.php`.

## config.php

Path:

```text
server/plugins/vendor/plugin/config.php
```

Purpose:

- Plugin-local configuration loaded by `PluginsApp::config()`.

Shape:

```php
<?php
return [
    'key' => 'value',
];
```

Rules:

- `PluginsApp` caches this file per plugin name.
- Missing files produce an empty config array.
- Nested values are read with `array_chain(...)`.

## PHP Classes

Project Composer config maps:

```json
{
  "psr-4": {
    "plugins\\": "plugins"
  }
}
```

Plugin classes therefore use namespaces such as:

```php
namespace plugins\vendor\plugin\module;
```

This namespace shape also lets `PluginsApp` derive `vendor/plugin`.
