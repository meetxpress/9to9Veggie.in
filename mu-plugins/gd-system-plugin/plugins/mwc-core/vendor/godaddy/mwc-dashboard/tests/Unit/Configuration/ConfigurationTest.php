<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Configuration;

use Action_Scheduler\Migration\Config;
use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use WP_Mock;

/**
 * Tests the integrity of the configuration files under /configurations.
 */
class ConfigurationTest extends WPTestCase
{
    /**
     * This is a simple test to grant the configurations/support.php is consistent and returns a valid array.
     *
     * @covers GoDaddy\WordPress\MWC\Common\Configuration
     *
     * @throws Exception
     */
    public function testSupportConfigurationFile()
    {
        WP_Mock::userFunction('get_transient')->andReturnNull();

        Configuration::initialize([
            StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations'),
        ]);

        $this->assertIsArray(Configuration::get('support.support_user'));
        $this->assertIsArray(Configuration::get('support.support_bot'));
        $this->assertIsBool(Configuration::get('features.mwc_dashboard'));
    }

    /**
     * Tests that important configuration values are set in configurations/mwc-extensions.php.
     *
     * @covers configurations/mwc-extensions.php
     */
    public function testExtensionsConfigurationFile()
    {
        Configuration::initialize([
            StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations'),
        ]);

        $this->assertIsString(Configuration::get('mwc_extensions.assets.css.admin.url'));
        $this->assertIsArray(Configuration::get('mwc_extensions.featured'));
        $this->assertIsArray(Configuration::get('mwc_extensions.categories'));
    }
}
