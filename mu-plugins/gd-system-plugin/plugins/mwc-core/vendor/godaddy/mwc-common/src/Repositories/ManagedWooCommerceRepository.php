<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

/**
 * Managed WooCommerce repository class.
 *
 * @since 1.0.0
 */
class ManagedWooCommerceRepository
{
    /**
     * Gets the current Managed WordPress environment.
     *
     * @since 1.0.0
     *
     * @return string|null
     * @throws Exception
     */
    public static function getEnvironment()
    {
        if (Configuration::get('mwc.env')) {
            return Configuration::get('mwc.env');
        }

        if (Configuration::get('godaddy.is_staging_site')) {
            return 'staging';
        }

        if (empty(Configuration::get('mwc.mwp_settings.cname_link'))) {
            return 'development';
        }

        // @TODO: We could have a way to get 'testing' as the env in the future 
        
        $env = 'production';

        Configuration::set('mwc.env', $env);

        return $env;
    }

    /**
     * Determines if the current is a production environment.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isProductionEnvironment() : bool
    {
        return 'production' === self::getEnvironment();
    }

    /**
     * Determines if the current environment is a staging environment.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isStagingEnvironment() : bool
    {
        return 'staging' === self::getEnvironment();
    }

    /**
     * Determines if the current is a local environment.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isLocalEnvironment() : bool
    {
        return 'development' === self::getEnvironment();
    }

    /**
     * Determines if the current is a testing environment.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isTestingEnvironment() : bool
    {
        return 'testing' === self::getEnvironment();
    }

    /**
     * Determines if the site is hosted on Managed WordPress and has an eCommerce plan.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function hasEcommercePlan() : bool
    {
        $godaddy_expected_plan = Configuration::get('godaddy.account.plan.name');

        return self::isManagedWordPress() && $godaddy_expected_plan === Configuration::get('mwc.plan_name');
    }

    /**
     * Determines if the site is hosted on Managed WordPress.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isManagedWordPress() : bool
    {
        return (bool) Configuration::get('godaddy.account.uid');
    }

    /**
     * Determines if the site is hosted on MWP and sold by a reseller.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isReseller() : bool
    {
        return self::isManagedWordPress() && (int) self::getResellerId() > 1;
    }

    /**
     * Gets the configured reseller account, if present.
     *
     * `1` means the site is not a reseller site, but sold directly by GoDaddy.
     *
     * @since 1.0.0
     *
     * @return int|null
     * @throws Exception
     */
    public static function getResellerId()
    {
        return Configuration::get('godaddy.reseller');
    }

    /**
     * Determines if the site is hosted on MWP and sold by a reseller with support agreement.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isResellerWithSupportAgreement() : bool
    {
        if (! self::isReseller()) {
            return false;
        }

        return ! ArrayHelper::get(self::getResellerSettings(), 'customerSupportOptOut', true);
    }

    /**
     * Gets settings for a reseller account.
     *
     * @since 1.0.0
     *
     * @return array
     */
    private static function getResellerSettings() : array
    {
        try {
            $settings = (new Request())
                ->url(StringHelper::trailingSlash(static::getStorefrontSettingsApiUrl()).static::getResellerId())
                ->query(['fields' => 'customerSupportOptOut'])
                ->send()
                ->getBody();
        } catch (Exception $e) {
            $settings = [];
        }

        return ArrayHelper::wrap($settings);
    }

    /**
     * Gets the Storefront Settings API URL.
     *
     * @since 1.0.0
     *
     * @return string
     * @throws Exception
     */
    private static function getStorefrontSettingsApiUrl()
    {
        return StringHelper::trailingSlash(Configuration::get('mwc.extensions.api.url', ''))
            .Configuration::get('mwc.extensions.api.settings.reseller.endpoint', '');
    }

    /**
     * Determines if the site is hosted on MWP and is using a temporary domain.
     *
     * @since 1.0.0
     *
     * @return bool
     * @throws Exception
     */
    public static function isTemporaryDomain() : bool
    {
        $domain = Configuration::get('godaddy.temporary_domain');
        $home_url = function_exists('home_url') ? parse_url(home_url(), PHP_URL_HOST) : '';

        return self::isManagedWordPress() && $domain && StringHelper::trailingSlash($domain) === StringHelper::trailingSlash($home_url);
    }

    /**
     * Determines if the site used the WPNux template on-boarding system.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function hasCompletedWPNuxOnboarding() : bool
    {
        return WordPressRepository::hasWordPressInstance() && (bool) get_option('wpnux_imported');
    }

    /**
     * Gets the value of the XID server variable.
     *
     * @since x.y.z
     *
     * @return int
     */
    public static function getXid() : int
    {
        $siteXId = (int) ArrayHelper::get($_SERVER, 'XID', 0);

        return $siteXId > 1000000 ? $siteXId : 0;
    }

    /**
     * Gets the ID for the site.
     *
     * @since x.y.z
     *
     * @return string
     * @throws Exception
     */
    public static function getSiteId() : string
    {
        $siteId = Configuration::get('godaddy.site.id');

        if (empty($siteId) && $siteXid = (string) self::getXid()) {
            // use XID instead
            $siteId = $siteXid;

            // update configuration
            Configuration::set('godaddy.site.id', $siteId);
            update_option('gd_mwc_site_id', $siteId);
        }

        return $siteId;
    }
}
