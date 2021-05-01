---
id: extension
title: Extension
---

The `Extension` component provides a standardized, performant interface for interacting with extensions.  The Extension type denotes a **data class** meaning that each instance type will share a common core functionality interface, but may have very instance specific functionality and state.  

As a result the base functionality will be provided in a base class and handling of children will always be done through the given [extension type](/components/extension#available-types).

## Base Class
The base class is abstract and meant to provide a shared interface, but you should check the individual [extension type](/components/extension#available-types) for specific usage of the **data class** instance.

### Class properties
| Parameter                	| Type      | Description                                                       |
|--------------------------	|--------	|-------------------------------------------------------------------|
| id                        | int       | The ID, if any                                                    |
| brand                     | string    | The brand, if any                                                 |
| category                  | string    | The slug of an assigned category, if any                          |
| documentationUrl          | string    | URL for the product’s documentation                               |
| homepageUrl               | string    | URL for the product’s homepage, e.g. sales page                   |
| lastUpdated               | int       | The UNIX timestamp for the extension's last update                |
| minimumPhpVersion         | int       | The minimum version of PHP required to run the extension          |
| minimumWooCommerceVersion | int       | The minimum version of WooCommerce required to run the extension  |
| minimumWordPressVersion   | int       | The minimum version of WordPress required to run the extension    |
| name                      | string    | The name, e.g. WooCommerce Memberships                            |
| packageUrl                | string    | URL of the product’s zip, for download                            |
| shortDescription          | string    | The short description                                             |
| slug                      | string    | The slug, e.g. woocommerce-memberships                            |
| type                      | string    | The extension type, e.g. plugin or theme                          |
| version                   | string    | The version number                                                |

### Getters and Setters

In addition to the given **data class** properties, there is a setter and getter provided for each.  Those are denoted as follows:

```php
public function setProperty($value) : AbstractExtension

public function getProperty();
```

## Available Types
| Type   	|
|--------	|
| Plugin 	|
| Theme  	|

### Creating a Type
To create an addition extension type you should extend [AbstractExtension](/components/extension#base-class).  There is no Contract requiring additional fields for this class type.

```php
use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;

final class MyExtensionType extends AbstractExtension
{
  // ...
}
```

### Plugin
The `Plugin` extension type provides a **data class** to normalize plugin data into a commonly shared interface as opposed to its typical raw array format.  The class can further extend the data states by allowing common functionality like downloading within the interface.

#### Basename

You may get or set the plugin basename.  Getting the plugin's basename returns the same as provided more broadly by [getPluginBasename()](/repositories/plugins#plugin-basename).  However, given a specific plugin instance you may get or set the basename without needing to provide its name.

```php
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;

$plugin = new PluginExtension();

$plugin->setBasename('my-plugin');
$plugin->getBasename();

// my-plugin
```

### Theme
The `Theme` extension type provides a **data class** to normalize plugin data into a commonly shared interface as opposed to its typical raw array format.  This extension type currently provides no additional data state or functionality beyond the [AbstractExtension base](/components/extension#base-class).


## Installation and Activation

Extensions can be installed and activated using the respective `install` and `activate` methods. If there's an error performing these tasks, an exception will be thrown and logged to Sentry.

```php
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;

$plugin = new PluginExtension();
$plugin->install();
$plugin->activate();

$theme = new ThemeExtension();
$theme->install();
// do something in between...
$theme->activate();
```

To verify the state of an extension, whether installed or active, use the methods `isInstalled` and `isActive` respectively.

```php
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;

$plugin = new PluginExtension();

if ($plugin->isActive()) {
    // ...
} elseif (! $plugin->isInstalled()) {
    //...
}
```

## Uninstall and Deactivate

Extensions can be uninstalled and deactivated using the respective `uninstall` and `deactivate` methods. If there's an error performing these tasks, an exception will be thrown and logged to Sentry.

:::note What happens if I uninstall a plugin that's not installed?
If you attempt to uninstall a plugin that is not current installed, it will do nothing and act as though the uninstall request was successful since you have ended in the requested state -- a plugin not installed.
:::

```php
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;

$plugin = new PluginExtension();

$plugin->deactivate();

$plugin->uninstall();
```

## Getting the Current Version

You may often want to determine the currently installed plugin or theme before deciding what to do with the current theme instance.  To help with this you may get the current installed version via `getInstalledVersion`.  It will return `null` if no version is installed or the version number of the currently installed instance.


```php
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;

$plugin = new PluginExtension();

$plugin->getInstalledVersion();

// 1.2.3
```