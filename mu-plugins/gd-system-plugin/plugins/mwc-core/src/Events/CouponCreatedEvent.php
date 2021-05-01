<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

/**
 * Coupon created event class.
 *
 * @since x.y.z
 */
class CouponCreatedEvent extends AbstractCouponEvent
{
    /**
     * CouponCreatedEvent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->action = 'create';
    }
}
