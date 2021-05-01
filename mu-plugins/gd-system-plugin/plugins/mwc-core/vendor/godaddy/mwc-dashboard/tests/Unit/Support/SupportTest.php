<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Support;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Support\Support;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support
 */
final class SupportTest extends WPTestCase
{
    /**
     * Tests the getSupportBotAppName() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support::getSupportAppName()
     *
     * @param bool $isReseller
     * @param string $expected
     * @param string $configPath
     *
     * @throws Exception
     * @dataProvider providerCanDetermineSupportAppName
     */
    public function testCanDetermineSupportAppName(bool $isReseller, string $expected, string $configPath)
    {
        $this->mockWordPressTransients();
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
            ->andReturn($isReseller);

        Configuration::set($configPath, $expected);

        $this->assertSame($expected, Support::getSupportAppName());
    }

    /** @see testCanDetermineSupportAppName */
    public function providerCanDetermineSupportAppName() : array
    {
        return [
            [false, 'Not Reseller Name', 'support.support_bot.app_name'],
            [true, 'Reseller Name', 'support.support_bot.app_name_reseller'],
        ];
    }

    /**
     * Tests that can determine the support source type
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support::getConnectType()
     *
     * @param bool $isReseller
     * @param string $expected
     *
     * @throws Exception
     * @dataProvider providerCanDetermineSupportConnectionType
     */
    public function testCanDetermineSupportConnectionType(bool $isReseller, string $expected)
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
            ->andReturn($isReseller);

        $this->assertSame($expected, Support::getConnectType());
    }

    /** @see testCanDetermineSupportConnectionType */
    public function providerCanDetermineSupportConnectionType() : array
    {
        return [
            [false, 'godaddy'],
            [true, 'reseller'],
        ];
    }

    /**
     * Tests that can determine the support connection url
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support::getConnectUrl()
     *
     * @throws Exception
     * @dataProvider providerCanGetConnectUrl
     */
    public function testCanGetConnectUrl(bool $isReseller, string $baseUrl, string $siteUrl, string $expected)
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
            ->andReturn($isReseller);

        WP_Mock::userFunction('site_url')
            ->once()
            ->andReturn($siteUrl);

        Configuration::set('support.support_bot.connect_url', $baseUrl);

        $this->assertEquals($expected, Support::getConnectUrl());
    }

    /** @see testCanGetConnectUrl */
    public function providerCanGetConnectUrl() : array
    {
        return [
            [false, 'http://foobar.com', 'https://example.test', 'http://foobar.com?context=godaddy&url=https%253A%252F%252Fexample.test'],
            [false, 'http://foobar.com/', 'https://example.test', 'http://foobar.com?context=godaddy&url=https%253A%252F%252Fexample.test'],
            [true, 'http://foobar.com', 'https://example.test', 'http://foobar.com?context=reseller&url=https%253A%252F%252Fexample.test'],
            [true, 'http://foobar.com/', 'https://example.test', 'http://foobar.com?context=reseller&url=https%253A%252F%252Fexample.test'],
        ];
    }

    /**
     * Tests if support is currently connected with specific keys
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support::isSupportConnected()
     * @throws Exception
     */
    public function testIsSupportBotConnectedWithKeys()
    {
        global $wpdb;

        $database = $wpdb = Mockery::mock('\WPDB');
        $database->prefix = '';

        $this->mockStaticMethod(Support::class, 'getSupportAppName')
            ->andReturn('app');

        $database->shouldReceive('prepare')
            ->once()
            ->withArgs(["SELECT COUNT(key_id) FROM {$database->prefix}woocommerce_api_keys WHERE description LIKE %s", 'app%'])
            ->andReturnArg(0);
        $database->shouldReceive('get_var')
            ->once()
            ->withArgs(["SELECT COUNT(key_id) FROM {$database->prefix}woocommerce_api_keys WHERE description LIKE %s"])
            ->andReturn(1);

        $this->assertTrue(Support::isSupportConnected());
    }

    /**
     * Tests if support is currently connected without keys
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\Support::isSupportConnected()
     * @throws Exception
     */
    public function testIsSupportBotConnectedWithoutKeys()
    {
        global $wpdb;

        $database = $wpdb = Mockery::mock('\WPDB');
        $database->prefix = '';

        $this->mockStaticMethod(Support::class, 'getSupportAppName')
            ->andReturn('app');

        $database->shouldReceive('prepare')
            ->once()
            ->withArgs(["SELECT COUNT(key_id) FROM {$database->prefix}woocommerce_api_keys WHERE description LIKE %s", 'app%'])
            ->andReturnArg(0);
        $database->shouldReceive('get_var')
            ->once()
            ->withArgs(["SELECT COUNT(key_id) FROM {$database->prefix}woocommerce_api_keys WHERE description LIKE %s"])
            ->andReturn(0);

        $this->assertFalse(Support::isSupportConnected());
    }
}
