<?php

namespace GoDaddy\WordPress\MWC\Common\Register\Contracts;

/**
 * Something that can be registered, like a static asset, script or style.
 *
 * @since 1.0.0
 */
interface RegistrableContract
{
    /**
     * Sets the registrable type.
     *
     * @since 1.0.0
     */
    public function __construct();

    /**
     * Determines how to deregister the registrable object.
     *
     * @since 1.0.0
     */
    public function deregister();

    /**
     * Determines how to execute the register.
     *
     * @since 1.0.0
     */
    public function execute();

    /**
     * Validates the current instance settings.
     *
     * @since 1.0.0
     */
    public function validate();
}
