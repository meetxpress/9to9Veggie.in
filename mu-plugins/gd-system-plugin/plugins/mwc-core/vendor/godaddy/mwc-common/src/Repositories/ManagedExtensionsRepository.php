<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\ExtensionAdapterContract;
use GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter;
use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter;
use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest;

/**
 * Managed extensions repository class.
 *
 * Provides methods for getting Woo and SkyVerge managed extensions.
 *
 * @since 1.0.0
 */
class ManagedExtensionsRepository
{
    /**
     * Gets all managed extensions.
     *
     * @since 1.0.0
     *
     * @return AbstractExtension[]
     * @throws Exception
     */
    public static function getManagedExtensions() : array
    {
        return array_map(static function ($data) {
            return self::buildManagedExtension(new ExtensionAdapter($data));
        }, static::getManagedExtensionsFromCache('extensions', [static::class, 'getManagedExtensionsData']));
    }

    /**
     * Gets the managed plugins.
     *
     * @since 1.0.0
     *
     * @return PluginExtension[]
     * @throws Exception
     */
    public static function getManagedPlugins() : array
    {
        return ArrayHelper::where(static::getManagedExtensions(), static function (AbstractExtension $extension) {
            return $extension->getType() === PluginExtension::TYPE;
        }, false);
    }

    /**
     * Get only the installed managed plugins.
     *
     * @since 1.0.0
     *
     * @return PluginExtension[]
     * @throws Exception
     */
    public static function getInstalledManagedPlugins() : array
    {
        WordPressRepository::requireWordPressFilesystem();

        $availablePlugins = get_plugins();

        return ArrayHelper::where(static::getManagedPlugins(), function (PluginExtension $plugin) use ($availablePlugins) {
            return ArrayHelper::exists($availablePlugins, $plugin->getBasename());
        });
    }

    /**
     * Get only the installed managed plugins.
     *
     * @since 1.0.0
     *
     * @return PluginExtension[]
     * @throws Exception
     */
    public static function getInstalledManagedThemes() : array
    {
        WordPressRepository::requireWordPressFilesystem();

        $availableThemes = wp_get_themes();

        return ArrayHelper::where(static::getManagedThemes(), function (ThemeExtension $theme) use ($availableThemes) {
            return ArrayHelper::exists($availableThemes, $theme->getSlug());
        });
    }

    /**
     * Gets managed extensions from cache.
     *
     * If the cache has no value, it attempts to get the extensions invoking the given $loader.
     *
     * @since 1.0.0
     *
     * @param string $key cache key
     * @param callable $loader function to call
     *
     * @return array
     * @throws Exception
     */
    protected static function getManagedExtensionsFromCache(string $key, callable $loader) : array
    {
        $cache = Cache::extensions()->get([]);

        if (! empty($currentValue = ArrayHelper::get($cache, $key))) {
            return $currentValue;
        }

        $extensions = $loader();

        ArrayHelper::set($cache, $key, $extensions);

        Cache::extensions()->set($cache);

        return $extensions;
    }

    /**
     * Gets data for managed SkyVerge extensions.
     *
     * @since x.y.z
     *
     * @return array
     * @throws Exception
     */
    protected static function getManagedExtensionsData() : array
    {
        $response = (new GoDaddyRequest())
            ->query(['method' => 'GET'])
            ->url(static::getManagedExtensionsApiUrl())
            ->send();

        return ArrayHelper::get($response->getBody(), 'data', []);
    }

    /**
     * Gets the URL for the Managed SkyVerge Extensions API.
     *
     * @since x.y.z
     *
     * @return string
     * @throws Exception
     */
    protected static function getManagedExtensionsApiUrl() : string
    {
        return Configuration::get('mwc.extensions.api.url', '') ? StringHelper::trailingSlash(Configuration::get('mwc.extensions.api.url', '')).'extensions/' : '';
    }

    /**
     * Builds an instance of an extension using the data returned by the given adapter.
     *
     * @since 1.0.0
     *
     * @param ExtensionAdapterContract $adapter data source adapter
     *
     * @return AbstractExtension
     */
    protected static function buildManagedExtension(ExtensionAdapterContract $adapter) : AbstractExtension
    {
        if (ThemeExtension::TYPE === $adapter->getType()) {
            return (new ThemeExtension())->setProperties($adapter->convertFromSource());
        }

        return (new PluginExtension())->setProperties($adapter->convertFromSource());
    }

    /**
     * Gets the managed themes.
     *
     * @since 1.0.0
     *
     * @return ThemeExtension[]
     * @throws Exception
     */
    public static function getManagedThemes() : array
    {
        return ArrayHelper::where(static::getManagedExtensions(), static function (AbstractExtension $extension) {
            return $extension->getType() === ThemeExtension::TYPE;
        }, false);
    }

    /**
     * Gets available versions for the given extension.
     *
     * It currently returns data for extensions listed in the SkyVerge Extensions API only.
     *
     * @since 1.0.0
     *
     * @param AbstractExtension $extension the extension object
     *
     * @return AbstractExtension[]
     * @throws Exception
     */
    public static function getManagedExtensionVersions(AbstractExtension $extension) : array
    {
        if (! $extension->getId()) {
            return [$extension];
        }

        return array_map(static function ($data) {
            return static::buildManagedExtension(new ExtensionAdapter($data));
        }, static::getManagedExtensionVersionsDataFromCache($extension));
    }

    /**
     * Gets version data for the given managed extension from cache.
     *
     * It the cache has no value, it attempts to get the data from the API.
     *
     * @since 1.0.0
     *
     * @param AbstractExtension $extension the extension object
     *
     * @return array
     * @throws Exception
     */
    protected static function getManagedExtensionVersionsDataFromCache(AbstractExtension $extension) : array
    {
        return static::getManagedExtensionsFromCache("versions.{$extension->getSlug()}", static function () use ($extension) {
            return static::loadManagedExtensionVersionsData($extension);
        });
    }

    /**
     * Loads data for the available versions of the given managed extension from the API.
     *
     * @since 1.0.0
     *
     * @param AbstractExtension $extension
     *
     * @return array
     * @throws Exception
     */
    protected static function loadManagedExtensionVersionsData(AbstractExtension $extension) : array
    {
        $response = (new GoDaddyRequest())
            ->query(['method' => 'GET'])
            ->url(static::getManagedExtensionVersionsApiUrl($extension))
            ->send();

        $versions = ArrayHelper::get($response->getBody(), 'data', []);

        usort($versions, static function ($a, $b) {
            return version_compare(ArrayHelper::get($a, 'version'), ArrayHelper::get($b, 'version'));
        });

        return static::addExtensionDataToVersionData($extension, $versions);
    }

    /**
     * Gets the URL for the endpoint used to retrieve available versions for a given extension.
     *
     * @since 1.0.0
     *
     * @param AbstractExtension $extension the extension object
     * @param int $count the max number of versions to retrieve
     *
     * @return string
     * @throws Exception
     */
    protected static function getManagedExtensionVersionsApiUrl(AbstractExtension $extension, int $count = 1000) : string
    {
        return StringHelper::trailingSlash(static::getManagedExtensionsApiUrl())."{$extension->getId()}/versions?limit={$count}";
    }

    /**
     * Updates the version data with the values of the properties of the given extension.
     *
     * @since 1.0.0
     *
     * @param AbstractExtension $extension the extension object
     * @param array $versions available versions data
     *
     * @return array
     */
    protected static function addExtensionDataToVersionData(AbstractExtension $extension, array $versions) : array
    {
        return array_map(static function ($version) use ($extension) {
            return [
                'extensionId'      => $extension->getId(),
                'slug'             => $extension->getSlug(),
                'label'            => $extension->getName(),
                'shortDescription' => $extension->getShortDescription(),
                'type'             => $extension->getType(),
                'category'         => $extension->getCategory(),
                'version'          => $version,
                'links'            => [
                    'homepage'      => [
                        'href' => $extension->getHomepageUrl(),
                    ],
                    'documentation' => [
                        'href' => $extension->getDocumentationUrl(),
                    ],
                ],
            ];
        }, $versions);
    }

    /** Deprecated methods ********************************************************************************************/

    /**
     * Builds an instance of an extension using the data from SkyVerge Extensions API.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @param array $data extension data
     *
     * @return AbstractExtension
     * @throws Exception
     */
    protected static function buildManagedSkyVergeExtension(array $data) : AbstractExtension
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return static::buildManagedExtension(new ExtensionAdapter($data));
    }

    /**
     * Builds an instance of an extension using data from GoDaddy's WooCommerce Extensions API.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @param array $data extension data
     *
     * @return AbstractExtension
     * @throws Exception
     */
    protected static function buildManagedWooExtension(array $data) : AbstractExtension
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return static::buildManagedExtension(new WooCommerceExtensionAdapter($data));
    }

    /**
     * Gets the SkyVerge managed extensions.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return AbstractExtension[]
     * @throws Exception
     */
    public static function getManagedSkyVergeExtensions() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getManagedExtensions');

        return ArrayHelper::where(static::getManagedExtensions(), static function (AbstractExtension $extension) {
            return $extension->getBrand() === 'godaddy';
        }, false);
    }

    /**
     * Gets the URL for the Managed SkyVerge Extensions API.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return string
     * @throws Exception
     */
    protected static function getManagedSkyVergeExtensionsApiUrl() : string
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getManagedExtensionsApiUrl');

        return '';
    }

    /**
     * Gets data for managed SkyVerge extensions.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return array
     * @throws Exception
     */
    protected static function getManagedSkyVergeExtensionsData() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getManagedExtensionsData');

        return [];
    }

    /**
     * Gets the Woo managed extensions.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return AbstractExtension[]
     * @throws Exception
     */
    public static function getManagedWooExtensions() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getManagedExtensions');

        return ArrayHelper::where(static::getManagedExtensions(), static function (AbstractExtension $extension) {
            return $extension->getBrand() === 'woo';
        }, false);
    }

    /**
     * Gets the Woo extensions API URL.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return string
     * @throws Exception
     */
    protected static function getManagedWooExtensionsApiUrl() : string
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return '';
    }

    /**
     * Gets data for managed WooCommerce extensions.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return array
     * @throws Exception
     */
    protected static function getManagedWooExtensionsData() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return [];
    }

    /**
     * Loads the SkyVerge managed extensions from the API.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return array
     * @throws Exception
     */
    protected static function loadManagedSkyVergeExtensions() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return [];
    }

    /**
     * Loads the WooCommerce managed extensions from the API.
     *
     * @since 1.0.0
     * @deprecated
     *
     * @return array
     * @throws Exception
     */
    protected static function loadManagedWooExtensions() : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z');

        return [];
    }
}
