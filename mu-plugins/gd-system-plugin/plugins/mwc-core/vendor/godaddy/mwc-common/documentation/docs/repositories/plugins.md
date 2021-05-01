---
id: plugins
title: Plugins
---

The `Plugins` repository provides an abstraction layer for common interactions with the plugins within a project.  To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository;
```

## Activate Plugin

Active a given plugin by its name.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository;

PluginsRepository::activatePlugin();
```

## Check if Plugin Active

Check by its name if the given plugin is currently active.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository

PluginsRepository::isPluginActive('name');
```

## Check if Plugin Installed

Check by its name if the given plugin is currently installed.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository

PluginsRepository::isPluginInstalled('name');
```

## Plugin Basename

On some rare occasions plugin filenames do not match their slugs.  To combat this problem, we operate off the plugins basename, which converts the given plugin to its expected filename when it does not match its sluggable name.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository

PluginsRepository::getPluginBasename();
```

## Plugin Path

Returns the given plugin's path.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PluginsRepository

PluginsRepository::getPluginPath();
```