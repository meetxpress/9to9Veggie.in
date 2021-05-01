---
id: managed-extensions
title: ManagedExtensions
---

The `ManagedExtensions` repository provides an abstraction layer for common interactions with managed Extensions within a project. To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
```

## Configuration

The repository communicates with the GoDaddy and SkyVerge extension APIs to retrieve the latest premium extension data. In order to use it, you'll need to set a few configuration values using site constants:

```php
define( 'MWC_EXTENSIONS_API_URL', 'url/to/your/local/extensions/api' );

define( 'GD_ACCOUNT_UID', '' ); // your GoDaddy account ID
define( 'GD_SITE_TOKEN', '' ); // your GoDaddy site token

define( 'GD_PLAN_NAME', 'eCommerce Managed WordPress' ); // this is the value currently required by the plugin
```
You can find the GoDaddy account values in your TLA site's `gd-config.php`

You can point the `MWC_EXTENSIONS_API_URL` constant to your local instance of the [SkyVerge extensions API](https://github.com/gdcorp-partners/skyverge-extensions-api)

## Methods

## getManagedExtensions

Returns an array of all managed extensions. Extensions can include multiple extension types like [Plugins](/components/extension#plugin) and [Themes](/components/extension#theme).

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

ManagedExtensionsRepository::getManagedExtensions();
```

## getManagedPlugins

Returns an array of only the managed plugins.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

ManagedExtensionsRepository::getManagedPlugins();
```

## getInstalledManagedPlugins

Returns an array of all the installed managed plugins.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

ManagedExtensionsRepository::getInstalledManagedPlugins();
```

## getManagedThemes

Returns an array of only the managed themes.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

ManagedExtensionsRepository::getManagedThemes();
```

## getInstalledManagedThemes

Returns an array of all the installed managed themes.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

ManagedExtensionsRepository::getInstalledManagedThemes();
```

## getManagedExtensionVersions

Returns an array of all the available versions for the given extension.

Each version is represented by a `PluginExtension` or `ThemeExtension` object with data for that version.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;

$plugin = ManagedExtensionsRepository::getManagedExtensions()[5];

ManagedExtensionsRepository::getManagedExtensionVersions($plugin);
```
