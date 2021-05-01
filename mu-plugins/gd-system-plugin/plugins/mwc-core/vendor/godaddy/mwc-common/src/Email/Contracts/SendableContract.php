<?php

namespace GoDaddy\WordPress\MWC\Common\Email\Contracts;

/**
 * Something that can be sent, like an email.
 *
 * @since 1.0.0
 */
interface SendableContract
{
    /**
     * Sends it.
     *
     * @since 1.0.0
     */
    public function send();
}
