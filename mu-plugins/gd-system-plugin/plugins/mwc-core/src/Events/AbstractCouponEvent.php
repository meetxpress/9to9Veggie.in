<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;
use WC_Coupon;

/**
 * Abstract coupon event class.
 *
 * @since x.y.z
 */
abstract class AbstractCouponEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var WC_Coupon The coupon object */
    protected $coupon;

    /**
     * AbstractCouponEvent constructor.
     */
    public function __construct()
    {
        $this->resource = 'coupon';
    }

    /**
     * Sets the WooCommerce coupon object for this event.
     *
     * @param WC_Coupon $coupon
     * @return $this
     */
    public function setWooCommerceCoupon(WC_Coupon $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Gets the data for the event.
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->coupon ? [
            'coupon' => [
                'id' => $this->coupon->get_id(),
                'code' => $this->coupon->get_code(),
                'discountType' => $this->coupon->get_discount_type(),
                'discountAmount' => $this->coupon->get_amount(),
            ],
        ] : [];
    }
}
