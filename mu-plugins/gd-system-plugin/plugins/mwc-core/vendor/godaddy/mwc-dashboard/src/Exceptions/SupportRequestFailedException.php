<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Exceptions;

use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;

/**
 * Sentry Exception Class that serves as a base to report to sentry.
 *
 * @since x.y.z
 */
class SupportRequestFailedException extends SentryException
{
    /** @var int exception code */
    protected $code = 500;

    /** @var string exception level */
    protected $level = 'error';

}
