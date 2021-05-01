<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Traits;

use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use WP_Mock;

trait CanMockExtensionsRequestFunctions
{
    /**
     * Mocks WordPress request functions to return WooCommerce extensions data.
     */
    protected function mockWooCommerceExtensionsRequestFunctions()
    {
        // clear cache
        WP_Mock::userFunction('get_transient')->andReturnFalse();
        WP_Mock::userFunction('set_transient')->andReturnNull();

        // mock request response
        WP_Mock::userFunction('wp_remote_request')->andReturn([]);
        WP_Mock::userFunction('is_wp_error')->andReturnFalse();
        WP_Mock::userFunction('wp_remote_retrieve_response_code')->andReturn(200);
        WP_Mock::userFunction('wp_remote_retrieve_body')->andReturn(json_encode([
            'name'        => 'godaddy_ecommerce',
            'description' => 'GoDaddy eCommerce Plan',
            'products'    => [
                [
                    'download_link'         => 'https://example.org/customer-order-csv-export/package',
                    'homepage'              => 'https://woocommerce.com/products/ordercustomer-csv-export/',
                    'icons'                 => [
                        '1x' => 'https://example.org/icons-128x128.png',
                        '2x' => 'https://example.org/icons-256x256.png',
                    ],
                    'last_updated'          => '2020-12-07', // 1607299200
                    'name'                  => 'WooCommerce Customer / Order / Coupon Export',
                    'short_description'     => 'Export customer, order, and coupon data in CSV and XML formats',
                    'slug'                  => 'woocommerce-customer-order-csv-export',
                    'support_documentation' => 'https://docs.woocommerce.com/document/ordercustomer-csv-export/',
                    'type'                  => PluginExtension::TYPE,
                    'version'               => '5.2.0',
                ],
                [
                    'download_link'         => 'https://example.org/shipment-tracking/package',
                    'homepage'              => 'https://woocommerce.com/products/shipment-tracking/',
                    'icons'                 => [
                        '1x' => 'https://example.org/icons-128x128.png',
                        '2x' => 'https://example.org/icons-256x256.png',
                    ],
                    'last_updated'          => '2020-10-12', // 1602460800
                    'name'                  => 'Shipment Tracking',
                    'short_description'     => 'Add shipment tracking information to your orders.',
                    'slug'                  => 'woocommerce-shipment-tracking',
                    'support_documentation' => 'http://docs.woocommerce.com/document/shipment-tracking/',
                    'type'                  => PluginExtension::TYPE,
                    'version'               => '1.6.26',
                ],
                [
                    'download_link'         => 'https://example.org/abc-plugin/package',
                    'homepage'              => 'https://example.org/products/abc-plugin/',
                    'icons'                 => [
                        '1x' => 'https://example.org/icons-128x128.png',
                        '2x' => 'https://example.org/icons-256x256.png',
                    ],
                    'last_updated'          => '2020-10-12', // 1602460800
                    'name'                  => 'ABC Plugin',
                    'short_description'     => 'A plugin that should be at the top of the list.',
                    'slug'                  => 'abc-plugin',
                    'support_documentation' => 'http://docs.example.org/document/abc-plugin/',
                    'type'                  => PluginExtension::TYPE,
                    'version'               => '1.2.3',
                ],
                [
                    'download_link'         => 'https://example.org/third/package',
                    'homepage'              => 'https://example.org/third/homepage',
                    'icons'                 => [
                        '1x' => 'https://example.org/icons-128x128.png',
                        '2x' => 'https://example.org/icons-256x256.png',
                    ],
                    'last_updated'          => '2020-10-16',
                    'name'                  => 'Third Theme',
                    'short_description'     => 'Third Theme description',
                    'slug'                  => 'third-theme',
                    'support_documentation' => 'https://example.org/third/documentation',
                    'type'                  => ThemeExtension::TYPE,
                    'version'               => '1.6.1',
                ],
            ],
        ]));
    }
}
