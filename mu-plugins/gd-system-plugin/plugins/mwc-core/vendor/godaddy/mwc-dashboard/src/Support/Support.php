<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Support;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

class Support
{
    /**
     * Checks whether or not the site is connected to the support bot.
     *
     * @return bool
     * @throws Exception
     */
    public static function isSupportConnected() : bool
    {
        $appName  = static::getSupportAppName();
        $database = ArrayHelper::get($GLOBALS, 'wpdb');

        // look for WooCommerce API keys containing the support bot app name in their description
        $keys = $database->get_var($database->prepare("SELECT COUNT(key_id) FROM {$database->prefix}woocommerce_api_keys WHERE description LIKE %s", "{$appName}%"));

        return ! empty($keys);
    }

    /**
     * Gets the support bot app name.
     *
     * @return string
     * @throws Exception
     */
    public static function getSupportAppName()
    {
        return ManagedWooCommerceRepository::isReseller() ? Configuration::get('support.support_bot.app_name_reseller') : Configuration::get('support.support_bot.app_name');
    }

    /**
     * Gets the type of connection that should be formed with support
     *
     * @return string `godaddy` or `reseller`
     * @throws Exception
     */
    public static function getConnectType()
    {
        return ManagedWooCommerceRepository::isReseller() ? 'reseller' : 'godaddy';
    }

    /**
     * Gets the URL to connect the site to support.
     *
     * @return string
     * @throws Exception
     */
    public static function getConnectUrl() : string
    {
        $baseUrl = StringHelper::beforeLast(StringHelper::trailingSlash(Configuration::get('support.support_bot.connect_url')), '/');

        return "{$baseUrl}?" . ArrayHelper::query([
            'context' => static::getConnectType(),
            'url' => urlencode(site_url()),
        ]);
    }
}
