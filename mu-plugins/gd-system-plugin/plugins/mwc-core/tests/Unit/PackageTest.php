<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Dashboard\Dashboard;
use GoDaddy\WordPress\MWC\Core\Package;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Package
 */
final class PackageTest extends WPTestCase
{
    /**
     * Runs before each test.
     */
    public function setUp() : void
    {
        parent::setUp();

        WP_Mock::userFunction('plugin_dir_url')->andReturn('plugin-dir-url');
        WP_Mock::userFunction('load_plugin_textdomain')->once();
        WP_Mock::userFunction('plugin_basename');

        new Package();
    }

    /**
     * Tests that the configuration is initialized.
     *
     * @throws Exception
     */
    public function testConfigurationInitialized()
    {
        $this->assertIsString(Configuration::get('mwc.version'));
        $this->assertSame('plugin-dir-url', Configuration::get('mwc.url'));
    }

    /**
     * Tests that the dashboard is loaded.
     *
     * @throws Exception
     */
    public function testDashboardIsLoaded()
    {
        $this->assertTrue(Dashboard::isLoaded());
    }
}
