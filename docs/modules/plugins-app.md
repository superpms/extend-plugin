# Module: PluginsApp

`pms\app\PluginsApp` is a trait for classes inside project plugins. It provides plugin-local config and path helpers.

## Namespace-Based Plugin Name

The trait derives a plugin name from the called class:

```php
$class = get_called_class();
$name = explode("\\", $class);
$name = array_slice($name, 1, 2);
return join('/', $name);
```

For a class named:

```php
plugins\superpms\login\business\LoginBusiness
```

the derived plugin name is:

```text
superpms/login
```

This is why project plugin namespaces must keep the shape:

```text
plugins\<vendor>\<plugin>\...
```

## config()

```php
final protected static function config(?string $name = null, $default = null)
```

`config()` loads:

```text
<pluginsRoot>/<vendor>/<plugin>/config.php
```

The config is cached in a static array keyed by plugin name.

Call patterns:

```php
self::config();                 // full config array
self::config('header-token');   // one nested value
self::config('missing', 'x');   // fallback value
```

The method uses `array_chain($config, $name)` to read nested values. If the requested value is `null`, the supplied default is returned.

If the plugin has no `config.php`, the cached config is an empty array.

## path()

```php
final protected static function path(?string $suffix = null): string
```

`path()` returns a path under the plugin root.

It derives the plugin directory from the called class namespace, then calls:

```php
Path::getPluginsRoot($name, $suffix)
```

The method normalizes a non-empty suffix so it begins with `/`.

## Existing Server Usage

Current server plugin classes use this trait to avoid hard-coding their plugin directory:

- Login token signing reads `superpms/login/config.php`.
- Login middleware reads header and auth field names from `superpms/login/config.php`.
- HTTP request signing reads `superpms/http/config.php`.
- API doc generation reads `superpms/apidoc/config.php`.

## Boundaries

`PluginsApp` is not a base class and does not participate in lifecycle registration. It only provides helper methods to plugin classes that opt into the trait.
