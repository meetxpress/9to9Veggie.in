---
id: configuration
title: Configuration
---

The `Configuration` class provides a common set of functionality for interacting with configurations and settings at the application level in a standardized manner.  Currently configurations are defined in many different veins within PHP and even more within our own given project sets:

- `.ini` files
- `.env` files
- `.yaml` files
- external configuration files
- database stored persistent settings
- PHP constant declaration which cannot be changed once the PHP instance has been set making them extremely hard to test
- options within the options tables when dealing with WordPress or WooCommerce

The above is a list given from a current single live project.  While the above is not an exhaustive list for all projects, it presents the real challenge of knowing where each configuration lives, what its current state may be at different points in its life cycle, testing functionality dependent upon it, and defining the impacts of modifying a setting due to an unclear chain of custody.  The previous challenges are in addition to the varying performance issues related to each with no guarantee or standard handling of caching such settings.

The `Configuration` provides a standard shared feature set for dealing with configurations, regardless of their origin, and allows for simplified modification after the fact in a testable manner.

To use the `Configuration` class import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
```

## Configurations

### Default Folder
The `Configuration` operates under the assumption that all configurations are contained within the `configurations` directory within the project root. The `Configurations` allows for infinite additional **nested directories**. To add a configuration directory provide the desired directory to the `initialize` method:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::initialize('/var/www/project/some/directory');
```

Initialization can be confirmed by:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::isInitialized();
```

:::note When to Initialize
Initialization of the `Configuration` class should only occur once within a project.  It is encouraged that this happen when you bootstrap a given project. When building a WordPress or WooCommerce plugin, this would typically occur in the plugin declaration file itself and is generally already done in the offered [base plugins](/wordpress/plugin).
:::

You may retrieve the full paths for all configuration folders at anytime by:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::directories();
```

### Configuration File
All configuration files **MUST** return a php associative array with the given settings and must be located within a configuration directory.  Configurations will be stored by their file name as their root key, followed by the give associated array.  A typical configuration file may look something like the following:

```php title="configurations/godaddy.php"
return [
  /*
   *--------------------------------------------------------------------------
   * Site General Information
   *--------------------------------------------------------------------------
   *
   * The following settings are related to the production GoDaddy site.  They
   * are automatically generated upon site creation and provide general
   * information around site interaction generally set by PHP constants.
   *
   */
	'site' => [
		'created'  => defined('GD_SITE_CREATED') ? GD_SITE_CREATED : null,
		'token'    => defined('GD_SITE_TOKEN') ? GD_SITE_TOKEN : null,
	],

  /*
   *--------------------------------------------------------------------------
   * Site Types
   *--------------------------------------------------------------------------
   *
   * The following settings declare type attributes about the site itself like
   * is it a production site or operating off a temporary domain.
   *
   */
	'temporary_domain' => defined('GD_TEMP_DOMAIN') ? GD_TEMP_DOMAIN : null,
	'staging'          => defined('GD_STAGING_SITE') ? GD_STAGING_SITE : false,

];
```

Since the configuration files are a standard PHP file returning an associative array, the full power of PHP is available to you in these files to determine the appropriate setting state/value.  These files are also fully testable via the `Configuration` to ensure settings are not changed resulting in an unknown impact.

## Configuration Values

### Get Configuration Value
You may easily access any of your configuration values using the `Configuration` from anywhere within the codebase for any declared configuration within the broader project.  The configuration values may be accessed using dot notation which will always begin with the filename the configuration is located within.  

In addition, a default value may be provided if the configuration is not found or is not currently set.

:::note Files as key names
The first key in your dot notated string is always the filename.  It is important to note that the filename will be converted to all lowercase and snake case.  Eg. `mwc-dashboard` and `MwcDashboard` will become `mwc_dashboard`.
:::

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::get('godaddy.site.token');

// Returns current site token

Configuration::get('godaddy.temporary_domain', 'https://mydomain.com');

// https://mydomain.com
```

### Set Configuration Value
You may also set a configuration value using `Configuration` from anywhere within the codebase for any declared configuration within the broader project.  These settings will currently be lost upon configuration reload -- until further work is done to persist these.  Currently this is most valuable for testing or updating a configuration temporarily for something like a preview of a change.

If the team does decide to allow for persisting of setting changes via this helper, the following functionality should have a persist boolean added to initiate a private method called `setPersisted` after updating the cache which updates whatever the given origin was.

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::set('godaddy.temporary_domain', 'https://newdomain.com');

// https://newdomain.com
```

## Configuration Caching
To give all projects a speed boost and to ensure less performant configuration stores are not indirectly impacting user experiences all configurations are cached by default use the [Cache](/components/cache) class and [CacheConfigurations](/components/cache#cache-configurations) type.  

### Check if Cached
You can check if the current configuration is cached at anytime by:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

Configuration::isCached();
```

### Clearing the Cache
If you need to bust the cache you may do so easily and force it to be reloaded:

```php
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

// Will be reloaded the next time a value is requested
Configuration::clear();

// Force an immediate clear and reload of the cache
Configuration::reload();
```
