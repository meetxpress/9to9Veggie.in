<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Repositories;

use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository
 */
final class WooCommercePluginsRepositoryTest extends WPTestCase
{
    /**
     * Tests the getPluginDataBySlug() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getPluginDataBySlug()
     * @throws \Exception
     */
    public function testGetPluginDataBySlug()
    {
        $testData = $this->getTestLocalWooPluginsData();

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $this->assertSame($testData['testwooplugin/testwooplugin.php'], WooCommercePluginsRepository::getPluginDataBySlug('testwooplugin'));
        $this->assertSame($testData['woocommerce-memberships-mailchimp/woocommerce-memberships-mailchimp.php'], WooCommercePluginsRepository::getPluginDataBySlug('woocommerce-memberships-mailchimp'));
        $this->assertSame([], WooCommercePluginsRepository::getPluginDataBySlug('another-plugin'));
    }

    /**
     * Tests the getWooCommerceSkyVergePlugins() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceSkyVergePlugins()
     * @throws \Exception
     */
    public function testGetWooCommerceSkyVergePlugins()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $testPluginData = end($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $skyVergePlugins = WooCommercePluginsRepository::getWooCommerceSkyVergePlugins();

        $this->assertIsArray($skyVergePlugins);
        $this->assertCount(1, $skyVergePlugins);
        $skyVergePlugin = $skyVergePlugins[0];
        $this->assertInstanceOf(PluginExtension::class, $skyVergePlugin);
        $this->assertEquals($testPluginData['_product_id'], $skyVergePlugin->getId());
        $this->assertSame($testPluginData['slug'], $skyVergePlugin->getSlug());
        $this->assertSame($testPluginData['Name'], $skyVergePlugin->getName());
        $this->assertSame($testPluginData['Description'], $skyVergePlugin->getShortDescription());
        $this->assertSame($testPluginData['RequiresPHP'], $skyVergePlugin->getMinimumPHPVersion());
        $this->assertSame($testPluginData['RequiresWP'], $skyVergePlugin->getMinimumWordPressVersion());
        $this->assertSame($testPluginData['WC requires at least'], $skyVergePlugin->getMinimumWooCommerceVersion());
        $this->assertSame($testPluginData['PluginURI'], $skyVergePlugin->getHomepageUrl());
        $this->assertSame($testPluginData['Documentation URI'], $skyVergePlugin->getDocumentationUrl());
        $this->assertSame($testPluginData['_filename'], $skyVergePlugin->getBasename());
    }

    /**
     * Tests the getDocumentationUrl() method when getting the documentation URL from the header.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getDocumentationUrl()
     * @throws \Exception
     */
    public function testGetDocumentationUrlFromDocumentationUriHeader()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $testPluginData = end($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $this->assertSame($testPluginData['Documentation URI'], WooCommercePluginsRepository::getDocumentationUrl($testPluginData));
    }

    /**
     * Tests the getDocumentationUrl() method when getting the documentation URL from the plugin instance.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getDocumentationUrl()
     * @throws \Exception
     */
    public function testGetDocumentationUrlFromPluginInstance()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $testPluginData = end($testData);
        $testPluginData['Documentation URI'] = '';

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        WP_Mock::userFunction('is_plugin_active')->andReturn(true);
        WP_Mock::userFunction('wc_memberships_mailchimp')->andReturn($this->getTestPluginInstance());

        $this->assertSame('https://fake-documentation-url.com', WooCommercePluginsRepository::getDocumentationUrl($testPluginData));
    }

    /**
     * Tests the getDocumentationUrl() method when falling back to the plugin URI header.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getDocumentationUrl()
     * @throws \Exception
     */
    public function testGetDocumentationUrlFromPluginUriHeader()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $testPluginData = current($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $this->assertSame($testPluginData['PluginURI'], WooCommercePluginsRepository::getDocumentationUrl($testPluginData));
    }

    /**
     * Tests the getWooCommerceSubscription() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceSubscription()
     * @throws \Exception
     */
    public function testGetWooCommerceSubscription()
    {
        $testData = $this->getTestWooCommerceSubscriptionsData();

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $this->assertSame(current($testData), WooCommercePluginsRepository::getWooCommerceSubscription(['_product_id' => 201963]));
        $this->assertSame(false, WooCommercePluginsRepository::getWooCommerceSubscription(['_product_id' => 123456]));
    }

    /**
     * Tests the getWooCommerceLicense() method when there is no license for the plugin or the site is not connected to WooCommerce.com.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceLicense()
     * @throws \Exception
     */
    public function testGetWooCommerceLicenseNone()
    {
        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn([]);

        $this->assertSame('none', WooCommercePluginsRepository::getWooCommerceLicense([]));
        $this->assertSame('none', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));

        $testData = $this->getTestWooCommerceSubscriptionsData();

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $this->mockStaticMethod(WooCommerceRepository::class, 'isWooCommerceConnected')
             ->andReturn(false);

        $this->assertSame('none', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceLicense() method when there is an expired license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceLicense()
     * @throws \Exception
     */
    public function testGetWooCommerceLicenseExpired()
    {
        $testData = $this->getTestWooCommerceSubscriptionsData(true);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $this->mockStaticMethod(WooCommerceRepository::class, 'isWooCommerceConnected')
             ->andReturn(true);

        $this->assertSame('expired', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceLicense() method when there is an active license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceLicense()
     * @throws \Exception
     */
    public function testGetWooCommerceLicenseActive()
    {
        $testData = $this->getTestWooCommerceSubscriptionsData();

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceConnectedSiteId')
             ->andReturn(1066);

        $this->mockStaticMethod(WooCommerceRepository::class, 'isWooCommerceConnected')
             ->andReturn(true);

        $this->assertSame('active', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceLicense() method when there is an inactive license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceLicense()
     * @throws \Exception
     */
    public function testGetWooCommerceLicenseInactive()
    {
        $testData = $this->getTestWooCommerceSubscriptionsData();

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $this->mockStaticMethod(WooCommerceRepository::class, 'isWooCommerceConnected')
             ->andReturn(true);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceConnectedSiteId')
             ->andReturn(1234);

        $this->assertSame('inactive', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceConnectedSiteId')
             ->andReturn(false);

        $this->assertSame('inactive', WooCommercePluginsRepository::getWooCommerceLicense(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceSubscriptionEnd() method when there is not license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceSubscriptionEnd()
     * @throws \Exception
     */
    public function testGetWooCommerceSubscriptionEndNoLicense()
    {
        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn([]);

        WP_Mock::userFunction('__')->andReturnArg(0);

        $this->assertSame('no subscription', WooCommercePluginsRepository::getWooCommerceSubscriptionEnd(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceSubscriptionEnd() method when there is a lifetime license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceSubscriptionEnd()
     * @throws \Exception
     */
    public function testGetWooCommerceSubscriptionEndLifetimeLicense()
    {
        $testData = $this->getTestWooCommerceSubscriptionsData(false, true);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        WP_Mock::userFunction('__')->andReturnArg(0);

        $this->assertSame('lifetime', WooCommercePluginsRepository::getWooCommerceSubscriptionEnd(['_product_id' => 201963]));
    }

    /**
     * Tests the getWooCommerceSubscriptionEnd() method when there is a regular license for the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getWooCommerceSubscriptionEnd()
     * @throws \Exception
     */
    public function testGetWooCommerceSubscriptionEndRegularLicense()
    {
        $expirationTimestamp = strtotime('next year');
        $testData = $this->getTestWooCommerceSubscriptionsData(false, false, $expirationTimestamp);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptions')
             ->andReturn($testData);

        $expiration = new \DateTime();
        $expiration->setTimestamp($expirationTimestamp);

        $this->assertSame($expiration->format('Y-m-d'), WooCommercePluginsRepository::getWooCommerceSubscriptionEnd(['_product_id' => 201963]));
    }

    /**
     * Tests the getPluginVersion() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getPluginVersion()
     * @throws \Exception
     */
    public function testGetPluginVersion()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $firstPluginData = current($testData);
        $secondPluginData = end($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $this->assertSame($firstPluginData['Version'], WooCommercePluginsRepository::getPluginVersion($firstPluginData));
        $this->assertSame($secondPluginData['Version'], WooCommercePluginsRepository::getPluginVersion($secondPluginData));
        $this->assertSame('', WooCommercePluginsRepository::getPluginVersion(['_product_id' => 333333]));
        $this->assertSame('', WooCommercePluginsRepository::getPluginVersion([]));
    }

    /**
     * Tests the getPluginName() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository::getPluginName()
     * @throws \Exception
     */
    public function testGetPluginName()
    {
        $testData = $this->getTestLocalWooPluginsData();
        $firstPluginData = current($testData);
        $secondPluginData = end($testData);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getLocalWooPluginsData')
             ->andReturn($testData);

        $this->assertSame($firstPluginData['Name'], WooCommercePluginsRepository::getPluginName($firstPluginData));
        $this->assertSame($secondPluginData['Name'], WooCommercePluginsRepository::getPluginName($secondPluginData));
        $this->assertSame('', WooCommercePluginsRepository::getPluginName(['_product_id' => 333333]));
        $this->assertSame('', WooCommercePluginsRepository::getPluginName([]));
    }

    /**
     * Gets a fake plugin instance implementing the get_documentation_url() method.
     *
     * @return object
     */
    private function getTestPluginInstance()
    {
        return new class() {
            public function get_documentation_url()
            {
                return 'https://fake-documentation-url.com';
            }
        };
    }

    /**
     * Gets test data to mock the WC_Helper::get_local_woo_plugins() method.
     *
     * @return array[]
     */
    private function getTestLocalWooPluginsData()
    {
        return [
            'testwooplugin/testwooplugin.php'                                         => [
                'Documentation URI'    => '',
                'WC requires at least' => '4.3',
                'WC tested up to'      => '4.6',
                'Woo'                  => '1234567:180317612cd3b4a761950bdb712ebdd4',
                'Name'                 => 'Test Woo Plugin',
                'PluginURI'            => 'https://woocommerce.com',
                'Version'              => '5.1.3',
                'Description'          => 'Fake WooCommerce plugin.',
                'Author'               => 'WooCommerce',
                'AuthorURI'            => 'https://woocommerce.com',
                'TextDomain'           => 'testwooplugin',
                'DomainPath'           => '/languages',
                'Network'              => false,
                'RequiresWP'           => '',
                'RequiresPHP'          => '',
                'Title'                => 'FakeWooCommerce Plugin',
                'AuthorName'           => 'WooCommerce',
                '_filename'            => 'testwooplugin/testwooplugin.php',
                '_product_id'          => 1234567,
                '_file_id'             => '180317612cd3b4a761950bdb712ebdd4',
                '_type'                => 'plugin',
                'slug'                 => 'testwooplugin',
            ],
            'woocommerce-memberships-mailchimp/woocommerce-memberships-mailchimp.php' => [
                'Documentation URI'    => 'https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/',
                'WC requires at least' => '3.5',
                'WC tested up to'      => '4.7.1',
                'Woo'                  => '3007049:6046684d2432e8520e56028a64de70be',
                'Name'                 => 'MailChimp for WooCommerce Memberships',
                'PluginURI'            => 'https://www.woocommerce.com/products/mailchimp-woocommerce-memberships/',
                'Version'              => '1.4.0',
                'Description'          => 'Sync your member lists to MailChimp for improved email segmentation',
                'Author'               => 'SkyVerge',
                'AuthorURI'            => 'https://www.woocommerce.com/',
                'TextDomain'           => 'woocommerce-memberships-mailchimp',
                'DomainPath'           => '/i18n/languages/',
                'Network'              => false,
                'RequiresWP'           => '',
                'RequiresPHP'          => '',
                'Title'                => 'MailChimp for WooCommerce Memberships',
                'AuthorName'           => 'SkyVerge',
                '_filename'            => 'woocommerce-memberships-mailchimp/woocommerce-memberships-mailchimp.php',
                '_product_id'          => 3007049,
                '_file_id'             => '6046684d2432e8520e56028a64de70be',
                '_type'                => 'plugin',
                'slug'                 => 'woocommerce-memberships-mailchimp',
            ],
        ];
    }

    /**
     * Gets test data to mock the WC_Helper::get_subscriptions() method.
     *
     * @param bool $expired
     * @param bool $lifetime
     * @param int $expiration expiration timestamp
     * @return array[]
     */
    private function getTestWooCommerceSubscriptionsData($expired = false, $lifetime = false, $expiration = 1641419223)
    {
        return [
            '918149-201963' => [
                'product_key' => 'W00-86a66456-4946-8cfb-16b249885e29',
                'product_keys_all' =>  [
                    0 =>  'W00-86a66456-4946-8cfb-16b249885e29',
                ],
                'product_id' =>  201963,
                'product_name' =>  'PDF Product Vouchers by SkyVerge',
                'product_url' =>  'https://woocommerce.com/products/pdf-product-vouchers/',
                'key_type' =>  'multi',
                'key_type_label' =>  'Up to 5 sites',
                'key_parent_order_item_id' => null,
                'autorenew' =>  false,
                'connections' => [
                    0 =>  1066,
                ],
                'legacy_connections' => [],
                'shares' => [],
                'lifetime' =>  $lifetime,
                'expires' =>  $expiration,
                'expired' =>  $expired,
                'expiring' =>  false,
                'sites_max' =>  5,
                'sites_active' =>  1,
                'maxed' =>  false,
                'product_status' =>  'publish',
            ],
        ];
    }
}
