---
id: plugin
title: Plugin
---

Plugins serve to extend the functionality of WordPress.  The generally offer new feature sets, functionality, and optimizations along with UI updates.  This is as opposed to Themes which tend to be very style and UI heavy with minimal functionality changes.  The following is a starting point offered by this library for implementing plugins:

## BasePlatformPlugin
The `BasePlatformPlugin` offers WordPress plugin integration out the box with minimal setup.  To create a new plugin simply extend the Base and call the parent constructor:

```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();
	}
}
```

### Plugin Name
By default any plugin extending `BasePlatformPlugin` will inherit the name of the projects folder name.  If you wish to explicitly set the name of the plugin -- encouraged -- then you may set the `name` property on the inheriting plugin class.


```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
	/** @var string The name of the plugin */
	protected $name = 'MyAwesomePlugin';
}
```

### Plugin Configurations
Plugins may declare configuration values at runtime to be set broadly.  Configuration values will be **pre-prended** with the plugins name.  To set configuration values you may include the key value pairs in the `configurationValues` class property or define a custom `getConfigurationValues()` method for more advanced handling.


```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
	/** @var array Key value array of plugin configurations */
	protected $configurationValues = [
      'VERSION' => '2.0.0',
    ];
    
    /**
     * Overwrite the configuration value getter for advanced control.
     *
     * @return array
     */
    protected function getConfigurationValues() : array
    {
        return [
            'PLUGIN_DIR' => __DIR__,
            'VERSION'    => '2.0.0',
        ];
    }
}
```

### Additional Configuration Directories

Plugins will always read configurations values from the MWC Common `configurations` folder. 

In addition to that, it will load values from `configurations` folder under the plugin's root directory.

In some cases, a plugin may want to add extra configuration files with its own specific values. In order to do that, the plugin should override the `configurationDirectories` property to providing the directory name(s) to its configuration directory or directories.

:::note Directory Paths
All directories assume a path from the root of the given project implementing the plugin.  Eg myConfigs will look in `path/to/project/myConfigs`.

The root project folder is always the folder contain the `src` folder.
:::

```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
    /**
     * Configuration directories.
     *
     * @var array
     */
    protected $configurationDirectories = ['configurations', 'external_configurations'];
}
```

Note that if there are two configuration files with the same name and keys, the values from the last added file will overwrite the previous ones.

### Set the Plugin Prefix
In rare circumstances you may need a custom plugin prefix for variables given the plugin name is too large or uncommon to make sense as a prefix.  In those rare instances you may explicitly declare the plugin prefix.

Note that plugin prefixes will be automatically appended with a deliminator.

:::danger Dangerous Action
Plugin folder names or plugin names themselves offer an easy way to reduce the risk of naming conflicts which could result in configurations getting unexpectedly clobbered.  When choosing a new prefix, be sure to keep in mind the uniqueness requirement of configurations.  Consider instead changing the name of the plugin itself rather than a custom prefix!
:::

```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
    /**
     * Overwrite the plugin prefix for more advanced handling.
     *
     * @return string
     */
    protected function getPluginPrefix() : string
    {
        return 'MyShortenedPrefix';
    }
}
```

### Classes to Instantiate
In some instances you may wish to force the constructors of some classes to execute upon instantiation of the plugin itself.  This could be to register various items with the underlying system at runtime like a menu item.  To accomplish this you may provide the classes and when to instantiate in the `classesToInstantiate` class property or define a custom `getClassesToInstantiate()` method for more advanced handling.

Key value pairs should declare the class itself and under what environments to load that classes constructor during plugin instantiation.


```php
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;

final class Plugin extends BasePlatformPlugin {
	/** @var array Key value array of classes to instantiate and when */
	protected $classesToInstantiate = [
      AnotherClass::class => 'web',
    ];
    
    /**
     * Overwrite the classes to instantiate method for more advanced handling
     *
     * @return array
     */
    protected function getClassesToInstantiate() : array
    {
        return [
            MyClass::class      => 'api',
            AnotherClass::class => 'web',
            Always::class       => true,
            Conditional::class  => Configuration::get('test.debug') ? 'api' : 'web',
        ];
    }
}
```
