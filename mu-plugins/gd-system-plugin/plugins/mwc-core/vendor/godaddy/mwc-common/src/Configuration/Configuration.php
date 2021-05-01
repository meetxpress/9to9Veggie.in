<?php

namespace GoDaddy\WordPress\MWC\Common\Configuration;

use Exception;
use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

/**
 * Main configuration handler.
 *
 * @since 1.0.0
 */
class Configuration
{
    /** @var string[] configuration directories where configuration files can be found */
    protected static $configurationDirectories = [];

    /**
     * Initializes the configuration for the product.
     *
     * @since 1.0.0
     *
     * @param ?string[]|string $additionalConfigurationDirectories
     */
    public static function initialize($additionalConfigurationDirectories = [])
    {
        self::addBaseConfigurationDirectory();

        $additionalConfigurationDirectories = ArrayHelper::wrap($additionalConfigurationDirectories);

        if ($additionalConfigurationDirectories) {
            foreach ($additionalConfigurationDirectories as $configurationDirectoryPath) {
                self::addConfigurationDirectory($configurationDirectoryPath);
            }
        }
    }

    /**
     * Clears the current configuration cache.
     *
     * @since 1.0.0
     */
    public static function clear()
    {
        Cache::configurations()->clear();
    }

    /**
     * Gets the Configurations directories.
     *
     * @since 1.0.0
     *
     * @return string[]
     */
    public static function directories() : array
    {
        return self::$configurationDirectories;
    }

    /**
     * Gets the configuration value.
     *
     * @since 1.0.0
     *
     * @param string $key dot notated array key for the configuration
     * @param mixed|null $default default value to return
     * @return mixed|null
     * @throws Exception
     */
    public static function get(string $key, $default = null)
    {
        $values = Cache::configurations()->get();

        if (! ArrayHelper::accessible($values)) {
            $values = self::load(self::directories());
        }

        return ArrayHelper::get(ArrayHelper::wrap($values), $key, $default);
    }

    /**
     * Loads the configurations of a given directory recursively.
     *
     * @since 1.0.0
     *
     * @param string $directory
     * @return array
     * @throws Exception
     */
    private static function getDirectoryContents(string $directory) : array
    {
        $result = [];
        $excluded = ['.', '..'];

        if (is_dir($directory)) {
            foreach (scandir($directory) as $name) {
                if (ArrayHelper::contains($excluded, $name)) {
                    continue;
                }

                $keyName = StringHelper::snakeCase(StringHelper::beforeLast($name, '.'));
                $filepath = StringHelper::trailingSlash($directory).$name;

                if (is_file($filepath)) {
                    $contents = include $filepath;

                    if (ArrayHelper::accessible($contents)) {
                        $result[$keyName] = $contents;
                    }

                    continue;
                }

                // @NOTE: Loop through sub directories if they exist
                $result[$keyName] = self::load($filepath);
            }
        }

        return $result;
    }

    /**
     * Determines if the Configurations are currently cached.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function isCached() : bool
    {
        return ! empty(Cache::configurations()->get());
    }

    /**
     * Determines if the configuration has been initialized.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function isInitialized() : bool
    {
        return (bool) self::$configurationDirectories;
    }

    /**
     * Loads the Configurations from the given folders.
     *
     * @NOTE: This should load and store the Configurations in the static cache.
     *
     * @since 1.0.0
     *
     * @param string[]|string $directories
     * @return array|void
     * @throws Exception
     */
    private static function load($directories) : array
    {
        $directories = ArrayHelper::wrap($directories);

        if (! self::isInitialized()) {
            self::initialize();
        }

        $results = [];

        foreach ($directories as $directory) {
            $results = ArrayHelper::combineRecursive($results, self::getDirectoryContents($directory));
        }

        Cache::configurations()->set($results);

        return $results;
    }

    /**
     * Reloads the current configuration cache.
     *
     * @NOTE: This is preferred over clearing the cache outright.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public static function reload()
    {
        self::load(self::directories());
    }

    /**
     * Overwrites a configuration value.
     *
     * @NOTE: Careful this change will currently be lost when the class static variable resets on a new instance of the application.
     *
     * This is not meant for permanent persistence at this time beyond the application instance and will need to be modified to support that functionality.
     *
     * Currently this is heavily convenient for testing.
     *
     * @since 1.0.0
     *
     * @param string $key dot notated array key for the configuration
     * @param mixed|null $value
     * @throws Exception
     */
    public static function set(string $key, $value = null)
    {
        $values = Cache::configurations()->get();

        if (! ArrayHelper::accessible($values)) {
            $values = self::load(self::directories());
        }

        ArrayHelper::set($values, $key, $value);

        Cache::configurations()->set($values);
    }

    /**
     * Adds the base configuration directory for the project.
     *
     * @since 1.0.0
     */
    private static function addBaseConfigurationDirectory()
    {
        $baseConfigurationDirectoryPath = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'src').'configurations');

        if (! ArrayHelper::contains(self::$configurationDirectories, $baseConfigurationDirectoryPath)) {
            self::$configurationDirectories[] = $baseConfigurationDirectoryPath;
        }
    }

    /**
     * Adds a configuration directory.
     *
     * @since 1.0.0
     *
     * @param string $directoryPath full path to the configuration directory
     */
    private static function addConfigurationDirectory(string $directoryPath)
    {
        if (! ArrayHelper::contains(self::$configurationDirectories, $directoryPath)) {
            self::$configurationDirectories[] = $directoryPath;
        }
    }

    /**
     * Checks if the key exists in the current configurations.
     *
     * @since 1.0.0
     *
     * @param string $key dot notated array key for the configuration
     * @return bool
     * @throws \Exception
     */
    public static function hasKey(string $key) : bool
    {
        $values = Cache::configurations()->get();

        if (! ArrayHelper::accessible($values)) {
            $values = self::load(self::directories());
        }

        return ArrayHelper::has(ArrayHelper::wrap($values), $key);
    }
}
