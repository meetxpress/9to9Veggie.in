<?php

namespace GoDaddy\WordPress\MWC\Core\Exceptions;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;

/**
 * Sentry exception extension to handle failed sent events.
 *
 * @since x.y.z
 */
class EventBridgeEventSendFailedException extends SentryException
{
    /**
     * EventBridgeEventSendFailedException constructor.
     *
     * @since x.y.z
     *
     * @param string $message exception message
     * @throws Exception
     */
    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->code = 500;
        $this->level = 'error';
    }
}
