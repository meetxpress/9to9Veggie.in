<?php

namespace GoDaddy\WordPress\MWC\Common\Traits;

use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;

/**
 * A trait for singletons.
 *
 * @since 1.0.0
 */
trait IsSingletonTrait
{
    /** @var AbstractExtension holds the current singleton instance */
    private static $instance;

    /**
     * Determines if the current instance is loaded.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function isLoaded() : bool
    {
        return (bool) self::$instance;
    }

    /**
     * Gets the singleton instance.
     *
     * @since 1.0.0
     *
     * @return AbstractExtension
     */
    public static function getInstance()
    {
        if (! self::isLoaded()) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Resets the singleton instance.
     *
     * @since 1.0.0
     */
    public static function reset()
    {
        self::$instance = null;
    }
}
