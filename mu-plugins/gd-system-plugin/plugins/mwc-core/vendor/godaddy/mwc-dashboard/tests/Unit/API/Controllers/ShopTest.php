<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository;
use GoDaddy\WordPress\MWC\Dashboard\Support\Support;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController
 */
final class ShopTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new ShopController();
        $route = TestHelpers::getInaccessibleProperty($controller, 'route');
        $this->assertSame('shop', $route->getValue($controller));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController::registerRoutes()
     */
    public function testCanRegisterRoutes()
    {
        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/shop', Mockery::any());

        (new ShopController())->registerRoutes();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController::getItem()
     *
     * TODO: consider splitting this method into several test methods that check that a smaller group of properties are generated correctly {WV 2021-03-30}
     *
     * @param string $siteId
     * @param string $siteUrl
     * @param string $adminUserEmail
     * @param string $supportUserEmail
     * @param bool $supportBotConnected
     * @param bool $wooCommerceConnected
     * @param string $dashboardType
     * @param bool $isReseller
     * @param int $privateLabelId
     * @param string $supportBotConnectUrl
     * @param bool $isCurrentUserOptedInForDashboardMessages
     * @param string $createdAt
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $country
     * @param string $state
     * @param string $postalCode
     *
     * @throws Exception
     * @dataProvider dataProviderGetItem
     */
    public function testCanGetItem(string $siteId, string $siteUrl, string $adminUserEmail, string $supportUserEmail, bool $supportBotConnected, bool $wooCommerceConnected, string $dashboardType, bool $isReseller, int $privateLabelId, string $supportBotConnectUrl, bool $isCurrentUserOptedInForDashboardMessages, string $createdAt, string $address1, string $address2, string $city, string $country, string $state, string $postalCode )
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'getSiteId')->andReturn($siteId);

        WP_Mock::userFunction('site_url')->andReturn($siteUrl);

        Configuration::set('support.support_user.email', $supportUserEmail);

        $adminUser = Mockery::mock('\WP_User');
        $adminUser->user_email = $adminUserEmail;
        $adminUser->shouldReceive('get')
             ->with('user_email')
             ->andReturn($adminUserEmail);

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
             ->andReturn($adminUser);

        if (! empty($supportUserEmail)) {
            $supportUser = Mockery::mock('\WP_User');
            $supportUser->user_email = $supportUserEmail;
            $supportUser->shouldReceive('get')
                 ->with('user_email')
                 ->andReturn($supportUserEmail);

            WP_Mock::userFunction('get_user_by')->andReturn($supportUser);
        } else {
            WP_Mock::userFunction('get_user_by')->andReturn(false);
        }

        WP_Mock::userFunction('wc_get_page_id')
               ->withArgs(['shop'])
               ->andReturn(6);
        if (!empty($createdAt)) {
            $shopPage = Mockery::mock('\WP_Post');
            $shopPage->post_date = $createdAt;
            WP_Mock::userFunction('get_post')
                   ->withArgs([6])
                   ->andReturn($shopPage);
        } else {
            WP_Mock::userFunction('get_post')
                   ->withArgs([6])
                   ->andReturn(null);
        }

        $this->mockStaticMethod(Support::class, 'isSupportConnected')
             ->andReturn($supportBotConnected);

        $this->mockStaticMethod(WooCommerceRepository::class, 'isWooCommerceConnected')
             ->andReturn($wooCommerceConnected);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'hasEcommercePlan')
             ->andReturn('MWC' === $dashboardType);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
             ->andReturn($isReseller);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'getResellerId')
             ->andReturn($privateLabelId);

        $this->mockStaticMethod(Support::class, 'getConnectUrl')
             ->andReturn($supportBotConnectUrl);

        $this->mockStaticMethod(UserRepository::class, 'userOptedInForDashboardMessages')
             ->andReturn($isCurrentUserOptedInForDashboardMessages);

        $countries = Mockery::mock('\WC_Countries');
        $countries->shouldReceive('get_base_address')
                    ->andReturn($address1);
        $countries->shouldReceive('get_base_address_2')
                    ->andReturn($address2);
        $countries->shouldReceive('get_base_city')
                    ->andReturn($city);
        $countries->shouldReceive('get_base_country')
                    ->andReturn($country);
        $countries->shouldReceive('get_base_state')
                    ->andReturn($state);
        $countries->shouldReceive('get_base_postcode')
                    ->andReturn($postalCode);
        $wooCommerce = Mockery::mock('\WooCommerce');
        $wooCommerce->countries = $countries;
        WP_Mock::userFunction('WC')->andReturn($wooCommerce);

        $expectedShopData = [
            'siteId' => $siteId,
            'siteURL' => $siteUrl,
            'adminEmail' => $adminUserEmail,
            'supportUserEmail' => $supportUserEmail,
            'supportBotConnected' => $supportBotConnected,
            'woocommerceConnected' => $wooCommerceConnected,
            'dashboardType' => $dashboardType,
            'isReseller'  => $isReseller,
            'privateLabelId'  => $privateLabelId,
            'supportBotConnectUrl' => $supportBotConnectUrl,
            'isCurrentUserOptedInForDashboardMessages' => $isCurrentUserOptedInForDashboardMessages,
            'createdAt' => $createdAt,
            'location' => [
                'address1'   => $address1,
                'address2'   => $address2,
                'city'       => $city,
                'country'    => $country,
                'state'      => $state,
                'postalCode' => $postalCode,
            ]
        ];

        // let's make sure the method returns the result of calling rest_ensure_response()
        $response = (object) ['shop' => $expectedShopData];

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'shop' => $expectedShopData,
            ])
            ->andReturn($response);

        $this->assertSame($response, (new ShopController())->getItem());
    }

    /** @see testGetItem() */
    public function dataProviderGetItem(): array
    {
        return [
            'full data' => [
                '1', 'https://example.test', 'admin@example.test', 'support@example.test', true, true, 'MWC', false, 1, 'https://connect.example.test', true, '2015-01-30 13:54:12', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
            'no support user and support bot not connected' => [
                '2', 'https://example.test', 'admin@example.test', '', false, true, 'MWC', false, 1, 'https://connect.example.test', true, '2015-01-30 13:54:12', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
            'not connected to WooCommerce' => [
                '3', 'https://example.test', 'admin@example.test', 'support@example.test', true, false, 'MWC', false, 1, 'https://connect.example.test', true, '2015-01-30 13:54:12', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
            'reseller' => [
                '4', 'https://example.test', 'admin@example.test', 'support@example.test', true, true, 'MWC', true, 777, 'https://connect.example.test', true, '2015-01-30 13:54:12', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
            'user not opted in for MWC Dashboard messages' => [
                '5', 'https://example.test', 'admin@example.test', 'support@example.test', true, true, 'MWC', true, 777, 'https://connect.example.test', false, '2015-01-30 13:54:12', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
            'no shop page' => [
                '6', 'https://example.test', 'admin@example.test', 'support@example.test', true, true, 'MWC', false, 1, 'https://connect.example.test', true, '', '123 Main St', 'Unit 456', 'Nashville', 'US', 'TN', '37213'
            ],
        ];
    }

    /**
     * Tests the getItemSchema() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController::getItemSchema()
     */
    public function testGetItemSchema()
    {
        WP_Mock::userFunction('__');

        $controller = new ShopController();

        $this->assertIsArray($controller->getItemSchema());
    }
}
