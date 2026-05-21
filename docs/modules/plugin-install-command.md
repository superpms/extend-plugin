# Module: PluginInstallCommand

`pms\extend\plugins\PluginInstallCommand` is the terminal command registered by `bin/autorun.php`.

## Command Name

```text
plugin-install
```

The command extends `pms\app\TerminalCommandApp`.

## Argument

The command declares one required argument:

```text
name
```

The name must be a two-part plugin name such as:

```text
superpms/login
```

Internally, the command checks that `explode('/', $name)` returns exactly two segments.

## Validation Flow

The command validates the target plugin in this order:

1. `name` argument is present.
2. `name` has exactly two slash-separated parts.
3. `<pluginsRoot>/<name>/plugin.json` exists.
4. `plugin.json` can be decoded as JSON.
5. `plugin.json` has a `name` field.
6. `plugin.json.name` matches the command argument.

When any check fails, the command writes a message and ends terminal output.

## Write Behavior

After validation, the command checks:

```text
<pluginsRoot>/<name>/autoload.php
```

Only when this file exists does the command update:

```text
<pluginsRoot>/installed.php
```

The update process is:

1. Include the current `installed.php`.
2. Append the plugin name.
3. Run `array_unique(...)`.
4. Write a PHP file returning the updated list.

Repeated installs therefore do not duplicate the same plugin name.

## Output

On success, the command prints:

- Install success title.
- Plugin name.
- Plugin directory path.

## Development Notes

- The command does not create a plugin directory.
- The command does not create `plugin.json`.
- The command does not create `autoload.php`.
- The command does not inspect plugin business code.
- A plugin with no `autoload.php` can pass metadata validation but will not be added to `installed.php` by this command.
- The command assumes `pluginsRoot` has already been mounted by boot-time setup.
