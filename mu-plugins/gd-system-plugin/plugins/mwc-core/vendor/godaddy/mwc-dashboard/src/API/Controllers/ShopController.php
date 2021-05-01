<?php

namespace GoDaddy\WordPress\MWC\Dashboard\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Dashboard\Support\Support;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository;

/**
 * ShopController controller class.
 */
class ShopController extends AbstractController
{
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->route = 'shop';
    }

    /**
     * Registers the API routes for the endpoints provided by the controller.
     */
    public function registerRoutes()
    {
        register_rest_route(
            $this->namespace, "/{$this->route}", [
                [
                    'methods'             => 'GET', // \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'getItem'],
                    'permission_callback' => [$this, 'getItemsPermissionsCheck'],
                ],
                'schema' => [$this, 'getItemSchema'],
            ]
        );
    }

    /**
     * Gets the shop information.
     *
     * @return \WP_REST_Response|\WP_Error
     * @throws Exception
     */
    public function getItem()
    {
        $adminUser = WordPressRepository::getUser();
        $supportUser = null;

        if (Configuration::get('support.support_user.email')) {
            $supportUser = WordPressRepository::getUserByEmail(Configuration::get('support.support_user.email'));
        }

        if (! $supportUser && Configuration::get('support.support_user.login')) {
            $supportUser = WordPressRepository::getUserByLogin(Configuration::get('support.support_user.login'));
        }

        $shopPageId = wc_get_page_id('shop');
        $shopPage = get_post($shopPageId);

        $item = [
            'shop' => [
                'siteId' => ManagedWooCommerceRepository::getSiteId(),
                'siteURL' => site_url(),
                'adminEmail' => $adminUser ? $adminUser->get('user_email') : '',
                'supportUserEmail' => $supportUser ? $supportUser->get('user_email') : '',
                'supportBotConnected' => Support::isSupportConnected(),
                'woocommerceConnected' => WooCommerceRepository::isWooCommerceConnected(),
                'dashboardType' => ManagedWooCommerceRepository::hasEcommercePlan() ? 'MWC' : '',
                'isReseller' => ManagedWooCommerceRepository::isReseller(),
                'privateLabelId' => ManagedWooCommerceRepository::getResellerId(),
                'supportBotConnectUrl' => Support::getConnectUrl(),
                'isCurrentUserOptedInForDashboardMessages' => UserRepository::userOptedInForDashboardMessages(),
                'createdAt' => ! empty( $shopPage ) ? $shopPage->post_date : '',
                'location' => [
                    'address1'   => WC()->countries->get_base_address(),
                    'address2'   => WC()->countries->get_base_address_2(),
                    'city'       => WC()->countries->get_base_city(),
                    'country'    => WC()->countries->get_base_country(),
                    'state'      => WC()->countries->get_base_state(),
                    'postalCode' => WC()->countries->get_base_postcode(),
                ],
            ],
        ];

        return rest_ensure_response($item);
    }

    /**
     * Returns the schema for REST items provided by the controller.
     *
     * @return array
     */
    public function getItemSchema() : array
    {
        return [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'shop',
            'type'       => 'object',
            'properties' => [
                'siteId' => [
                    'description' => __('Site ID.', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'siteUrl'      => [
                    'description' => __('Site URL.', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'adminEmail'      => [
                    'description' => __('Current admin user\'s email.', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'supportUserEmail'      => [
                    'description' => __('Support user\'s email, if a support user exists.', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'supportBotConnected'      => [
                    'description' => __('Whether or not the site is connected to the support bot.', 'mwc-dashboard'),
                    'type'        => 'bool',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'woocommerceConnected'      => [
                    'description' => __('Whether or not the site is connected to WooCommerce.com.', 'mwc-dashboard'),
                    'type'        => 'bool',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'dashboardType'    => [
                    'description' => __('Dashboard type (MWC or empty).', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'isReseller'      => [
                    'description' => __('Whether or not the site is sold by a reseller.', 'mwc-dashboard'),
                    'type'        => 'bool',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'privateLabelId'    => [
                    'description' => __('The reseller private label ID (1 means GoDaddy, so not a reseller).', 'mwc-dashboard'),
                    'type'        => 'int',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'supportBotConnectUrl'    => [
                    'description' => __('URL to connect the site to the support bot.', 'mwc-dashboard'),
                    'type'        => 'string',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'isCurrentUserOptedInForDashboardMessages' => [
                    'description' => __('Whether or not the current user is opted in to receive MWC Dashboard messages.', 'mwc-dashboard'),
                    'type'        => 'bool',
                    'context'     => ['view', 'edit'],
                    'readonly'    => true,
                ],
                'createdAt' => [
                    'description' => __( 'The Shop page\'s creation date.', 'mwc-dashboard' ),
                    'type'        => 'string',
                    'context'     => [ 'view', 'edit' ],
                    'readonly'    => true,
                ],
                'location' => [
                    'type'       => 'object',
                    'properties' => [
                        'address1' => [
                            'description' => __( 'Address line 1', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                        'address2' => [
                            'description' => __( 'Address line 2', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                        'city'     => [
                            'description' => __( 'City', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                        'country'  => [
                            'description' => __( 'Country', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                        'state'    => [
                            'description' => __( 'State', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                        'postalCode'    => [
                            'description' => __( 'Postal code', 'mwc-dashboard' ),
                            'type'        => 'string',
                            'context'     => [ 'view', 'edit' ],
                            'readonly'    => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
