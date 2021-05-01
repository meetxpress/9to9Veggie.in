<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;
use WC_Order;

/**
 * Abstract order event class.
 *
 * @since x.y.z
 */
abstract class AbstractOrderEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var WC_Order The order object */
    protected $order;

    /**
     * AbstractOrderEvent constructor.
     */
    public function __construct()
    {
        $this->resource = 'order';
    }

    /**
     * Sets the WooCommerce order object for this event.
     *
     * @since x.y.z
     *
     * @param WC_Order $order
     * @return $this
     */
    public function setWooCommerceOrder(WC_Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Gets the order data for the event.
     *
     * @since x.y.z
     *
     * TODO: remove this method when a native Order object is available in the Common package {IT 2021-03-24}
     *
     * @param WC_Order $order
     * @return array
     */
    protected function getOrderData(WC_Order $order) : array
    {
        return ['id' => $order->get_id()];
    }

    /**
     * Gets the data for the event.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->order ? $this->getOrderData($this->order) : [];
    }
}
