<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Configuration;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;

/**
 * Tests the integrity of the configuration files under /configurations.
 */
class ConfigurationTest extends WPTestCase
{
    /**
     * Tests that important configuration values are set in configurations/mwc.php.
     *
     * @covers configurations/mwc.php
     * @throws Exception
     */
    public function testManagedWooCommerceConfigurationFile()
    {
        Configuration::initialize([
            StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations'),
        ]);

        $this->assertSame('https://cdn4.mwc.secureserver.net/vendors.js', Configuration::get('mwc.client.vendors.url'));
        $this->assertSame('https://cdn4.mwc.secureserver.net/runtime.js', Configuration::get('mwc.client.runtime.url'));
        $this->assertSame('https://cdn4.mwc.secureserver.net/index.js', Configuration::get('mwc.client.index.url'));
    }

    /**
     * Tests that important configuration values are set in configurations/events.php.
     *
     * @covers configurations/events.php
     * @throws Exception
     */
    public function testEventsConfigurationFile()
    {
        Configuration::initialize([
            StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations'),
        ]);

        $this->assertSame('Bearer', Configuration::get('events.auth.type'));
        $this->assertSame('eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJhcGktZXZlbnRzLm13Yy5zZWN1cmVzZXJ2ZXIubmV0Iiwic2NvcGUiOiJ3cml0ZSIsImlhdCI6MTYxNzMwNDUwOSwiZXhwIjoxNjI1MDgwNTA5LCJpc3MiOiJhcGktZXZlbnRzLWF1dGgifQ.9CQuWuykArqzbFFXg0IbIwSJ9cKs2VzvqjjPLya7UktKEx9HnYNgcPnB5FTHbEY2aUc4yz9UBkYfJgRiiD5dfA', Configuration::get('events.auth.token'));
        $this->assertFalse(Configuration::get('events.send_local_events'));
    }
}
