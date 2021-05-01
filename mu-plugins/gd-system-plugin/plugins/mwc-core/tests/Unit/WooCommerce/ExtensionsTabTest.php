<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\WooCommerce;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
use GoDaddy\WordPress\MWC\Core\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Tests\Traits\CanMockExtensionsRequestFunctions;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab;
use ReflectionClass;
use ReflectionException;
use stdClass;
use WP_Mock;

/**
 * Provides tests for the ExtensionsTab class.
 *
 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab
 */
final class ExtensionsTabTest extends WPTestCase
{
    use CanMockExtensionsRequestFunctions;

    const USERNAME_ADMIN = 'admin';
    const USERNAME_CUSTOMER = 'customer';

    /**
     * Runs before each test.
     */
    public function setUp() : void
    {
        parent::setUp();

        // prepare Configuration to load variables from vendor/godaddy/mwc-common/configurations/
        Configuration::initialize();

        // wp_create_nonce is called for rendering some plugin buttons
        WP_Mock::userFunction('wp_create_nonce')->andReturnNull();

        // Register::filter()->execute() is called from the constructor of
        // ExtensionsTab and expects add_filter to be defined
        WP_Mock::userFunction('add_filter')->andReturnTrue();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::maybeRemoveManagedPluginsFromBrowser()
     *
     * @throws Exception
     */
    public function testMaybeRemoveManagedPluginsFromBrowser()
    {
        // simulate being on the browse_extensions tab
        ArrayHelper::set($_GET, 'page', ExtensionsTab::EXTENSIONS_PAGE_SLUG);
        ArrayHelper::set($_GET, 'section', '');
        ArrayHelper::set($_GET, 'tab', 'browse_extensions');

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedPlugins')
            ->andReturn(
                [
                    (new PluginExtension)->setSlug('managed-plugin-1'),
                    (new PluginExtension)->setSlug('managed-plugin-2'),
                ]
            );

        // test response data
        $response = [
            'body' => json_encode((object) ['products' => [
                (object) ['slug' => 'managed-plugin-1'],
                (object) ['slug' => 'not-managed-plugin'],
                (object) ['slug' => 'managed-plugin-2'],
            ]]),
        ];

        $modified_response = ArrayHelper::get(
            (new ExtensionsTab())->maybeRemoveManagedPluginsFromBrowser($response, [], 'https://woocommerce.com/wp-json/wccom-extensions/1.0/search'),
            'body',
            ''
        );

        $this->assertStringNotContainsString('managed-plugin-1', $modified_response);
        $this->assertStringNotContainsString('managed-plugin-2', $modified_response);
        $this->assertStringContainsString('not-managed-plugin', $modified_response);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::prepareEnqueuedPluginsData()
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCanPrepareEnqueuedPluginsData()
    {
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedPlugins')
            ->andReturn([
                (new PluginExtension())
                    ->setSlug('product-add-ons')
                    ->setBasename('woocommerce-product-add-ons/woocommerce-product-add-ons.php')
                    ->setHomepageUrl('https://woocommerce.com/products/product-add-ons/'),
                (new PluginExtension())
                    ->setSlug('checkout-fi-gateway')
                    ->setBasename('woocommerce-gateway-checkout-fi/woocommerce-gateway-checkout-fi.php')
                    ->setHomepageUrl('https://woocommerce.com/products/checkout-fi-gateway/'),
            ]);

        $tab = new ExtensionsTab();
        $method = TestHelpers::getStaticHiddenMethod($tab, 'prepareEnqueuedPluginsData');
        $plugins = $method->invoke($tab);

        $this->assertIsArray($plugins);
        $this->assertCount(2, $plugins);
        $this->assertArrayHasKey('slug', current($plugins));
        $this->assertEquals('product-add-ons', current($plugins)['slug']);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::isSubscriptionTabActive()
     *
     * @param string $tab
     * @param bool $expected
     *
     * @throws ReflectionException
     * @dataProvider dataProviderIsSubscriptionTabActive
     */
    public function testIsSubscriptionTabActive(string $tab, bool $expected)
    {
        $_GET['tab'] = $tab;

        $wooCommerceExtensionsTab = new ExtensionsTab();

        $reflectionClass = new ReflectionClass($wooCommerceExtensionsTab);

        $isSubscriptionTabActiveMethod = $reflectionClass->getMethod('isSubscriptionTabActive');
        $isSubscriptionTabActiveMethod->setAccessible(true);

        $this->assertEquals($expected, $isSubscriptionTabActiveMethod->invoke($wooCommerceExtensionsTab));
    }

    /**
     * @see testIsSubscriptionTabActive
     */
    public function dataProviderIsSubscriptionTabActive() : array
    {
        return [['subscriptions', true], ['any_tab', false]];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::maybeRemoveManagedPluginsFromFeatured()
     *
     * @param array $sections
     * @param mixed $expected
     *
     * @throws Exception
     * @dataProvider providerFeaturedAddonsTransientValue
     */
    public function testMaybeRemoveManagedPluginsFromFeatured(array $sections, $expected)
    {
        ArrayHelper::set($_GET, 'section', ExtensionsTab::FEATURED_SECTION_SLUG);

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedPlugins')->andReturn([
            (new PluginExtension())
                ->setSlug('plugin-1')
                ->setBasename('plugin-1/plugin-1.php'),
            (new PluginExtension())
                ->setSlug('plugin-2')
                ->setBasename('plugin-2/plugin-2.php'),
        ]);

        $transient = $match = new stdClass();
        $transient->sections = $sections;

        $match->sections = $expected;

        $filtered = (new ExtensionsTab())
            ->maybeRemoveManagedPluginsFromFeatured(json_decode(json_encode($transient)));

        $this->assertEquals(json_decode(json_encode($match)), $filtered);
    }

    /**
     * @see testMaybeRemoveManagedPluginsFromFeatured
     */
    public function providerFeaturedAddonsTransientValue() : array
    {
        return [
            [[['items' => [['plugin' => 'plugin-1/plugin-1.php'], ['plugin' => 'no/no.php']]]], [['items' => [['plugin' => 'no/no.php']]]]],
            [[['items' => [['plugin' => 'plugin-2/plugin-2.php']]]], null],
            [[['items' => 'not an array']], [['items' => 'not an array']]],
            [[['no-items' => []]], [['no-items' => []]]],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::shouldModifyFeaturedTransient()
     *
     * @param mixed $sections
     * @param mixed $expected
     *
     * @throws Exception
     * @dataProvider providerShouldModifyFeaturedTransient
     */
    public function testShouldModifyFeaturedTransient($transient, $expected)
    {
        ArrayHelper::set($_GET, 'section', ExtensionsTab::FEATURED_SECTION_SLUG);

        $class = new ExtensionsTab();
        $method = TestHelpers::getStaticHiddenMethod($class, 'shouldModifyFeaturedTransient');

        $this->assertEquals($expected, $method->invoke($class, $transient));
    }

    /**
     * @see testShouldModifyFeaturedTransient
     */
    public function providerShouldModifyFeaturedTransient() : array
    {
        return [
            [['sections' => 'test'], false],
            [(object) [], false],
            [(object) ['sections'], false],
            [(object) ['sections' => []], true],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\ExtensionsTab::hasFeaturedPlugins()
     *
     * @param mixed $sections
     * @param mixed $expected
     *
     * @throws Exception
     * @dataProvider providerShouldModifyFeaturedTransient
     */
    public function testCanDetermineHasFeaturedPlugins($transient, $expected)
    {
        $class = new ExtensionsTab();
        $method = TestHelpers::getStaticHiddenMethod($class, 'hasFeaturedPlugins');

        ArrayHelper::set($_GET, 'tab', 'fail');
        ArrayHelper::set($_GET, 'section', null);

        $this->assertFalse($method->invoke($class));

        ArrayHelper::set($_GET, 'tab', 'browse_extensions');

        $this->assertTrue($method->invoke($class));

        ArrayHelper::set($_GET, 'section', 'fail');

        $this->assertFalse($method->invoke($class));

        ArrayHelper::set($_GET, 'section', ExtensionsTab::FEATURED_SECTION_SLUG);

        $this->assertTrue($method->invoke($class));
    }
}
