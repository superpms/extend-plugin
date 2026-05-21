# Server Integration Facts

This page records how the current `server` project actually uses `superpms/extend-plugins`.

## Composer Requirement

`server/composer.json` requires:

```json
{
  "require": {
    "superpms/extend-plugins": "^1.0"
  }
}
```

The same file maps project plugin source with:

```json
{
  "autoload": {
    "psr-4": {
      "plugins\\": "plugins"
    }
  }
}
```

The Composer namespace mapping makes plugin classes autoloadable as `plugins\...`. The `extend-plugins` runtime loader separately loads each enabled plugin's `autoload.php`, which is where plugins can define constants, helper values, and other startup symbols.

## Boot Configuration

`server/boot.json` declares:

```json
{
  "extend": {
    "plugins": "/plugins"
  }
}
```

`Setup` reads that value through `BootOptions::get_extend('plugins', '/plugins')`.

## Installed Plugins

The current server loading whitelist is `server/plugins/installed.php`:

```php
return [
    'superpms/http',
    'superpms/apidoc',
    'superpms/login'
];
```

These names map to directories under `server/plugins`.

## Existing Plugin Examples

The current server has examples of the package conventions:

- `server/plugins/superpms/http/plugin.json`
- `server/plugins/superpms/http/autoload.php`
- `server/plugins/superpms/http/config.php`
- `server/plugins/superpms/login/plugin.json`
- `server/plugins/superpms/login/autoload.php`
- `server/plugins/superpms/login/config.php`
- `server/plugins/superpms/apidoc/plugin.json`
- `server/plugins/superpms/apidoc/config.php`

`superpms/http` and `superpms/login` provide `autoload.php` files and are loaded by `Setup`. `superpms/apidoc` appears in `installed.php`, but currently has no `autoload.php`; `Setup` therefore skips runtime inclusion for that plugin while its classes remain available through the project's Composer namespace mapping.

## PluginsApp Usage

Existing plugin classes use `pms\app\PluginsApp`, for example:

- `plugins\superpms\login\business\LoginBusiness`
- `plugins\superpms\login\middleware\LoginAuthMiddleware`
- `plugins\superpms\http\action\RequestAction`
- `plugins\superpms\http\middleware\RequestAuthMiddleware`
- `plugins\superpms\apidoc\ApidocBuilder`

The trait derives the plugin name from the class namespace. For `plugins\superpms\login\business\LoginBusiness`, the derived plugin name is `superpms/login`, so `self::config(...)` reads `server/plugins/superpms/login/config.php`.

## Related Runtime Consumers

Application code imports plugin classes and constants directly. Examples visible in the server include:

- HTTP access classes importing `plugins\superpms\http\annotate\RequestTerminalOnly`.
- HTTP access classes importing `plugins\superpms\login\contract\EnumAccessInterface`.
- Exception handlers importing plugin exception classes.
- Auth code calling `plugins\superpms\login\business\LoginBusiness`.

These are consumers of concrete plugins, not responsibilities of the `extend-plugins` package.
