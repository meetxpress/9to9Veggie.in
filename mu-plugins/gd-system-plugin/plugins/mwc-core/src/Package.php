<?php

namespace GoDaddy\WordPress\MWC\Core;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait;
use GoDaddy\WordPress\MWC\Core\Client\Client;
use GoDaddy\WordPress\MWC\Core\Events\Producers;
use GoDaddy\WordPress\MWC\Core\Pages\Plugins\IncludedWooCommerceExtensionsTab;
use GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Updates;
use GoDaddy\WordPress\MWC\Dashboard\Dashboard;

/**
 * MWC Core package class.
 *
 * @since x.y.z
 */
final class Package extends BasePlatformPlugin
{
    use IsSingletonTrait;

    /** @var string Plugin name */
    protected $name = 'mwc-core';

    /** @var array Classes to instantiate */
    protected $classesToInstantiate = [
        ExtensionsTab::class                    => 'web',
        Producers::class                        => 'web',
        Overrides::class                        => 'web',
        Updates::class                          => 'web',
        Client::class                           => 'web',
        IncludedWooCommerceExtensionsTab::class => 'web',
    ];

    /**
     * Package constructor.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        // skip in CLI mode.
        if (! WordPressRepository::isCliMode()) {

            // load the textdomain
            load_plugin_textdomain('mwc-core', false, plugin_basename(dirname(__DIR__)).'/languages');

            // load the dashboard
            Dashboard::getInstance();
        }
    }

    /**
     * Gets configuration values.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getConfigurationValues() : array
    {
        return array_merge(parent::getConfigurationValues(), [
            'version'    => '2.0.0',
            'plugin_dir' => dirname(__DIR__),
            'plugin_url' => plugin_dir_url(dirname(__FILE__)),
        ]);
    }

    /**
     * Initializes the Configuration class adding the plugin's configuration directory.
     *
     * @since x.y.z
     */
    protected function initializeConfiguration()
    {
        Configuration::initialize(StringHelper::trailingSlash(dirname(__DIR__)).'configurations');
    }
}
