<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * WooCommerce repository class.
 *
 * @since 1.0.0
 */
class WooCommerceRepository
{
    /**
     * Retrieve the current WooCommerce access token.
     *
     * @since 1.0.0
     *
     * @return string|null
     */
    public static function getWooCommerceAccessToken()
    {
        $authorization = self::getWooCommerceAuthorization();

        return ArrayHelper::get($authorization, 'access_token');
    }

    /**
     * Retrieve the current WooCommerce Authorization Object.
     *
     * @since 1.0.0
     *
     * @return array|null
     */
    public static function getWooCommerceAuthorization()
    {
        if (class_exists('WC_Helper_Options')) {
            return \WC_Helper_Options::get('auth');
        }

        return null;
    }

    /**
     * Checks if the WooCommerce plugin is active.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isWooCommerceActive() : bool
    {
        return ! is_null(Configuration::get('woocommerce.version'));
    }

    /**
     * Checks if the site is connected to WooCommerce.com.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isWooCommerceConnected() : bool
    {
        return self::isWooCommerceActive() && self::getWooCommerceAccessToken();
    }
}
