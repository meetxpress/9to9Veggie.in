<?php

namespace GoDaddy\WordPress\MWC\Common\Plugin;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\SentryRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

/**
 * Base platform plugin.
 *
 * @since 1.0.0
 */
class BasePlatformPlugin
{
    /** @var string[] classes to instantiate */
    protected $classesToInstantiate;

    /** @var array configuration values */
    protected $configurationValues;

    /** @var string[] configuration directories */
    protected $configurationDirectories = ['configurations'];

    /** @var string plugin name */
    protected $name;

    /**
     * Base platform plugin constructor.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function __construct()
    {
        // @NOTE: Load configurations so that they are cached - Should always be called first
        $this->initializeConfiguration();

        WordPressRepository::requireWordPressInstance();

        // @NOTE: Make sure all PHP constants are set
        $this->instantiateConfigurationValues();
        Configuration::reload();

        // @NOTE: Initialize error reporting -- Must be called after configurations are loaded
        $this->initializeErrorReporting();

        // @NOTE: Instantiate required classes
        $this->instantiatePluginClasses();
    }

    /**
     * Initializes the Configuration class and loads the configuration values.
     *
     * @since 1.0.0
     *
     * @return void
     * @since 1.0.0
     */
    protected function initializeConfiguration()
    {
        Configuration::initialize($this->getConfigurationDirectories());
    }

    /**
     * Initializes Error Reporting and Tracking.
     *
     * @since x.y.z
     *
     * @return void
     * @throws Exception
     */
    protected function initializeErrorReporting()
    {
        SentryRepository::initialize();
    }

    /**
     * Gets the classes that should be instantiated when initializing the inheriting plugin.
     *
     * @NOTE: This is here so it can be overridden if needed before setting values
     *
     * @since 1.0.0
     *
     * @return array
     */
    protected function getClassesToInstantiate() : array
    {
        return ArrayHelper::wrap($this->classesToInstantiate);
    }

    /**
     * Gets configuration values.
     *
     * @NOTE: This is here so it can be overridden if needed before setting values.
     *
     * @since 1.0.0
     *
     * @return array
     */
    protected function getConfigurationValues() : array
    {
        return ArrayHelper::wrap($this->configurationValues);
    }

    /**
     * Gets configuration directories.
     *
     * @NOTE: This is here so it can be overridden if needed before setting values.
     *
     * @since 1.0.0
     *
     * @return array
     */
    protected function getConfigurationDirectories() : array
    {
        $directories = [];
        $sourceDirectory = StringHelper::before(__DIR__, 'src');

        foreach (ArrayHelper::wrap($this->configurationDirectories) as $directory) {
            $directories[] = StringHelper::trailingSlash($sourceDirectory.$directory);
        }

        return $directories;
    }

    /**
     * Gets plugin prefix.
     *
     * @since 1.0.0
     *
     * @return string
     * @throws Exception
     */
    protected function getPluginPrefix() : string
    {
        $pluginName = $this->name ?: StringHelper::afterLast(Configuration::get('wordpress.absolute_path'), '/');

        return strtoupper($pluginName);
    }

    /**
     * Instantiates the plugin constants and configuration values.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    protected function instantiateConfigurationValues()
    {
        foreach ($this->getConfigurationValues() as $key => $value) {
            $this->defineConfigurationConstant($key, $value);
        }
    }

    /**
     * Safely converts the platform's configuration into global constant.
     *
     * @since 1.0.0
     *
     * @param string $configurationName
     * @param string $configurationValue
     *
     * @throws Exception
     */
    protected function defineConfigurationConstant(string $configurationName, string $configurationValue)
    {
        $pluginPrefix = $this->getPluginPrefix();
        $constantName = strtoupper(StringHelper::snakeCase(strtolower("{$pluginPrefix} {$configurationName}")));

        if (! defined($constantName)) {
            define($constantName, $configurationValue);
        }
    }

    /**
     * Instantiates the plugin specific classes.
     *
     * @since 1.0.0
     */
    protected function instantiatePluginClasses()
    {
        foreach ($this->getClassesToInstantiate() as $class => $mode) {
            if (is_bool($mode) && $mode) {
                new $class();
            }

            if ($mode === 'cli' && WordPressRepository::isCliMode()) {
                new $class();
            }

            if ($mode === 'web' && ! WordPressRepository::isCliMode()) {
                new $class();
            }
        }
    }
}
