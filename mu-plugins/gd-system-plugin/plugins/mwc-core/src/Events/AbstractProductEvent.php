<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;
use WC_Product;

/**
 * Abstract product event class.
 *
 * @since x.y.z
 */
abstract class AbstractProductEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var WC_Product The product object */
    protected $product;

    /**
     * AbstractProductEvent constructor.
     */
    public function __construct()
    {
        $this->resource = 'product';
    }

    /**
     * Sets the WooCommerce product object for this event.
     *
     * @param WC_Product $product
     * @return $this
     */
    public function setWooCommerceProduct(WC_Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Gets the data for the event.
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->product ? [
            'product' => [
                'id' => $this->product->get_id(),
                'type' => $this->product->get_type(),
                'status' => $this->product->get_status(),
            ],
        ] : [];
    }
}
