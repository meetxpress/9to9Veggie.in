<?php

namespace GoDaddy\WordPress\MWC\Common\Traits;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;

/**
 * Trait for event bridges.
 *
 * @see EventBridgeEventContract interface - this trait implements some of its methods
 *
 * @since x.y.z
 */
trait IsEventBridgeEventTrait
{
    /** @var string the name of the event resource */
    protected $resource;

    /** @var string the name of the event action */
    protected $action;

    /**
     * Gets the name of the resource for the event.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource ?: '';
    }

    /**
     * Gets the name of the action for the event.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getAction() : string
    {
        return $this->action ?: '';
    }
}
