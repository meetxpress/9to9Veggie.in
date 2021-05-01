<?php

namespace GoDaddy\WordPress\MWC\Common\Exceptions;

use GoDaddy\WordPress\MWC\Common\Repositories\SentryRepository;

/**
 * Sentry Exception Class that serves as a base to report to sentry.
 *
 * @since x.y.z
 */
class SentryException extends BaseException
{
    public function __destruct()
    {
        if (SentryRepository::hasSystemRequirements()) {
            \Sentry\captureException($this);
        }

        parent::__destruct();
    }
}
