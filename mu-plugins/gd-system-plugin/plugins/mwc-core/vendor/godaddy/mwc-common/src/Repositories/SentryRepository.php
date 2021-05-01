<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use Sentry\Event as SentryEvent;
use function Sentry\init as InitializeSentry;

class SentryRepository
{
    /**
     * Retrieves the current WooCommerce access token.
     *
     * @since x.y.z
     *
     * @return string|null
     * @throws Exception
     */
    public static function initialize()
    {
        if (! ($dsn = Configuration::get('reporting.sentry.dsn')) || ! static::hasSystemRequirements() || ! Configuration::get('reporting.sentry.enabled')) {
            return;
        }
        
        $currentEnv = ManagedWooCommerceRepository::getEnvironment();

        if (! ArrayHelper::contains(['development', 'staging', 'production'], $currentEnv)) {
            return;
        }

        InitializeSentry([
            'dsn'             => $dsn,
            'environment'     => $currentEnv,
            'max_breadcrumbs' => 50, // Amount of trace breadcrumbs -- default is 100
            'release'         => Configuration::get('mwc.version'), // @TODO: Replace version with commit hash {JO 2021-03-03}
            'sample_rate'     => 1, // Percentage of error events to send -- random selection
            'before_send'     => function (SentryEvent $event) {
                if (static::hasSentryException($event)) {
                    return $event;
                }

                return null;
            },
        ]);

        // Set scopes
        static::setSentryScopes();
    }

    /**
     * Checks if exception is explicitly declared as a reportable Sentry Exception.
     *
     * @NOTE If exceptions do not extend the base sentry exception they are not considered reportable.
     *
     * @since x.y.z
     *
     * @param SentryEvent $event
     * @return bool
     */
    protected static function hasSentryException(SentryEvent $event) : bool
    {
        foreach (ArrayHelper::wrap($event->getExceptions()) as $exceptionBag) {
            // @NOTE: Only send Exceptions intended for Sentry {JO 2021-03-03}
            if (is_a($exceptionBag->getType(), SentryException::class, true)) {
                return true;
            }
        }

        return false;

    }

    /**
     * Checks if the server instance meets the system requirements for Sentry.
     *
     * @NOTE: The sentry SDK Require PHP 7.2 or higher so we should check before loading any classes.
     *
     * @since x.y.z
     *
     * @return bool
     */
    public static function hasSystemRequirements() : bool
    {
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            return true;
        }

        return false;
    }

    /**
     * Set the sentry scopes for tagging
     *
     * @since x.y.z
     *
     * @return void
     */
    protected static function setSentryScopes()
    {
        if (! static::hasSystemRequirements()) {
            return;
        }

        \Sentry\configureScope(function (\Sentry\State\Scope $scope) {
            // Use domain as unique identifier
            $scope->setUser(['id' =>  ArrayHelper::get($_SERVER, 'HTTP_HOST', '')]);
            $scope->setTag('account_plan', Configuration::get('godaddy.account.plan.name') ?? '');
            $scope->setTag('cdn_enabled', Configuration::get('godaddy.cdn.enabled') ? 'yes' : 'no');
            $scope->setTag('request_type', Configuration::get('mwc.mode', ''));
            $scope->setTag('temporary_domain', ManagedWooCommerceRepository::isTemporaryDomain() ? 'yes' : 'no');
            $scope->setTag('managed_wordpress', ManagedWooCommerceRepository::isManagedWordPress() ? 'yes' : 'no');
            $scope->setTag('managed_woocommerce_version', Configuration::get('mwc.version') ?? '');
            $scope->setTag('woocommerce_active', WooCommerceRepository::isWooCommerceActive(), 'yes', 'no');
            $scope->setTag('woocommerce_version', Configuration::get('woocommerce.version') ?? '');
            $scope->setTag('wordpress_version', WordPressRepository::getVersion() ?? '');
            $scope->setTag('wordpress_cli_mode', WordPressRepository::isCliMode() ? 'yes' : 'no');
        });
    }
}
