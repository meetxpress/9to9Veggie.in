<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\WooCommerce;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Tests\Traits\CanMockExtensionsRequestFunctions;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides;
use ReflectionClass;
use WP_Mock;

/**
 * Provides tests for the Overrides class.
 */
class OverridesTest extends WPTestCase
{
    use CanMockExtensionsRequestFunctions;

    /**
     * Runs before each test.
     */
    public function setUp(): void
    {
        // Register::filter()->execute() is called from the constructor of
        // Overrides and requires add_filter to be defined
		$this->mockWordPressTransients();
        WP_Mock::userFunction('add_filter')->andReturnTrue();
    }

    /**
     * Tests that the authentication headers are set when making a request to the Extensions API.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::addExtensionsApiAuthenticationHeaders()
     *
     * @param bool $isAdmin
     * @param bool $isApiRequest
     * @param string $url
     * @param array $expectedArgs
     *
     * @throws Exception
     * @dataProvider dataProviderAddExtensionsApiAuthenticationHeaders
     */
    public function testAddExtensionsApiAuthenticationHeaders(bool $isAdmin, bool $isApiRequest, string $url, array $expectedArgs)
    {
	    WP_Mock::userFunction('is_admin')->andReturn($isAdmin);

	    $this->mockStaticMethod(WordPressRepository::class, 'isApiRequest')->andReturn($isApiRequest);

	    Configuration::set('mwc.extensions.api.url', 'https://extensions.api.url');
	    Configuration::set('godaddy.site.token', 'abcdef');
	    Configuration::set('godaddy.account.uid', '1234');

        $this->assertEquals($expectedArgs, (new Overrides())->addExtensionsApiAuthenticationHeaders([], $url));
    }

	/** @see testAddExtensionsApiAuthenticationHeaders */
	public function dataProviderAddExtensionsApiAuthenticationHeaders() : array
	{
		return [
			[false, false, 'https://any.other.url', []],
			[true, false, 'https://any.other.url', []],
			[false, true, 'https://any.other.url', []],
			[false, false, 'https://extensions.api.url', []],
			[false, false, 'https://extensions.api.url/xyz', []],
			[true, false, 'https://extensions.api.url', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
			[false, true, 'https://extensions.api.url', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
			[true, true, 'https://extensions.api.url', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
			[true, false, 'https://extensions.api.url/xyz', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
			[false, true, 'https://extensions.api.url/xyz', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
			[true, true, 'https://extensions.api.url/xyz', [
				'headers' => [
					'X-Site-Token' => 'abcdef',
					'X-Account-UID' => '1234',
				],
			]],
		];
	}

    /**
     * Tests that the SSL default option is set.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::maybeSetForceSsl()
     * @throws Exception
     */
    public function testSetsSslDefault()
    {
        Configuration::set('godaddy.account.uid', '1234');
        Configuration::set('godaddy.temporary_domain', '/');

        $this->assertEquals('yes', (new Overrides())->maybeSetForceSsl(false));

        Configuration::set('godaddy.temporary_domain', 'fail');

        $this->assertFalse((new Overrides())->maybeSetForceSsl(false));
    }

    /**
     * Tests if the expected actions and filters are being registered on the class constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::registerActions()
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::registerFilters()
     *
     * @param string $registerType
     * @param string $hookName
     * @param string $callback
     * @param int $priority
     * @param int $args
     * @param bool $isMwp
     * @param bool $isMwpEcommercePlan
     * @param bool $shouldRegister
     *
     * @throws Exception
     * @dataProvider dataProviderIsRegisteringActionOrFilter
     */
    public function testIsRegisteringActionOrFilter(string $registerType, string $hookName, string $callback, int $priority, int $args, bool $isMwp, bool $isMwpEcommercePlan, bool $shouldRegister) : void
    {
        Configuration::set('godaddy.account.uid', $isMwp ? '1234' : null);
        Configuration::set('godaddy.account.plan.name', $isMwpEcommercePlan ? Configuration::get('mwc.plan_name') : null);

        $wooCommerceOverrides = new Overrides();
        $reflectionInstance = new ReflectionClass($wooCommerceOverrides);

        if ($shouldRegister) {
            if ('filter' === $registerType) {
                WP_Mock::expectFilterAdded($hookName, [$wooCommerceOverrides, $callback], $priority, $args);
            } else {
                WP_Mock::expectActionAdded($hookName, [$wooCommerceOverrides, $callback], $priority, $args);
            }
        } else {
            if ('filter' === $registerType) {
                WP_Mock::expectFilterNotAdded($hookName, [$wooCommerceOverrides, $callback]);
            } else {
                WP_Mock::expectActionNotAdded($hookName, [$wooCommerceOverrides, $callback]);
            }
        }

        $registerMethod = $reflectionInstance->getMethod('filter' === $registerType ? 'registerFilters' : 'registerActions');
        $registerMethod->setAccessible(true);
        $registerMethod->invoke($wooCommerceOverrides);

        $this->assertConditionsMet();
    }

    /** @see testIsRegisteringActionOrFilter */
    public function dataProviderIsRegisteringActionOrFilter() : array
    {
        return [
            ['action', 'plugins_loaded', 'setDefaults', PHP_INT_MAX, 0, true, true, true],
            ['action', 'admin_init', 'maybeDisableMarketplaceSuggestions', 10, 0, true, true, true],
            ['filter', 'wc_pdf_product_vouchers_admin_hide_low_memory_notice', 'hidePdfProductVouchersLowMemoryNotice', 10, 1, true, true, true],
            ['filter', 'wc_pdf_product_vouchers_admin_hide_low_memory_notice', 'hidePdfProductVouchersLowMemoryNotice', 10, 1, false, true, false],
            ['filter', 'wc_pdf_product_vouchers_admin_hide_sucuri_notice', 'hidePdfProductVouchersSucuriNotice', 10, 1, true, true, true],
            ['filter', 'wc_pdf_product_vouchers_admin_hide_sucuri_notice', 'hidePdfProductVouchersSucuriNotice', 10, 1, false, true, false],
            ['filter', 'woocommerce_show_admin_notice', 'suppressNotices', 10, 2, true, true, true],
            ['filter', 'woocommerce_helper_suppress_connect_notice', 'suppressConnectNotice', PHP_INT_MAX, 1, true, true, true],
            ['filter', 'http_request_args', 'addExtensionsApiAuthenticationHeaders', 10, 2, true, true, true],
            ['filter', 'http_request_args', 'addExtensionsApiAuthenticationHeaders', 10, 2, false, false, false],
            ['filter', 'pre_option_woocommerce_force_ssl_checkout', 'maybeSetForceSsl', 10, 1, true, true, true],
            ['filter', 'pre_option_woocommerce_force_ssl_checkout', 'maybeSetForceSsl', 10, 1, false, false, false],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::shouldDisableMarketplaceSuggestions()
     *
     * @param $godaddySiteCreated
     * @param $disableMarketplaceSuggestions
     * @param $expected
     *
     * @throws Exception
     * @dataProvider dataProviderShouldDisableMarketplaceSuggestions
     */
    public function testShouldDisableMarketplaceSuggestions($godaddySiteCreated, $disableMarketplaceSuggestions, $expected)
    {
        if (isset($godaddySiteCreated)) {
            Configuration::set('godaddy.site.created', $godaddySiteCreated);
        }

        if (isset($disableMarketplaceSuggestions)) {
            Configuration::set('woocommerce.flags.disableMarketplaceSuggestions', $disableMarketplaceSuggestions);
        }

        $wooCommerceOverrides = new Overrides();

        $reflection = new ReflectionClass($wooCommerceOverrides);

        $method = $reflection->getMethod('shouldDisableMarketplaceSuggestions');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($wooCommerceOverrides));
    }

    /** @see testShouldDisableMarketplaceSuggestions */
    public function dataProviderShouldDisableMarketplaceSuggestions() : array
    {
        return [
            'godaddy.site.created is not set'                               => [null, 'yes', false],
            'woocommerce.flags.disableMarketplaceSuggestions is not set'    => [1617235199, null, false],
            'site is created before April 1st, 2021 but should not disable' => [1617235199, 'no', false],
            'site is created before April 1st, 2021 and should disable'     => [1617235199, 'yes', false],
            'site is created after April 1st, 2021 but should not disable'  => [1617235200, 'no', false],
            'site is created after April 1st, 2021 and should disable'      => [1617235200, 'yes', true],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Overrides::disableMarketplaceSuggestions()
     *
     * @throws Exception
     */
    public function testDisableMarketplaceSuggestions()
    {
        WP_Mock::userFunction('update_option')
            ->with('woocommerce_show_marketplace_suggestions', 'no')
            ->times(1);

        WP_Mock::userFunction('update_option')
            ->with('gd_mwc_disable_woocommerce_marketplace_suggestions', 'no')
            ->times(1);

        Configuration::set('woocommerce.flags.disableMarketplaceSuggestions', 'yes');

        $this->assertSame('yes', Configuration::get('woocommerce.flags.disableMarketplaceSuggestions'));

        $wooCommerceOverrides = new Overrides();

        $reflection = new ReflectionClass($wooCommerceOverrides);

        $method = $reflection->getMethod('disableMarketplaceSuggestions');
        $method->setAccessible(true);

        $method->invoke($wooCommerceOverrides);

        $this->assertSame('no', Configuration::get('woocommerce.flags.disableMarketplaceSuggestions'));
    }
}
