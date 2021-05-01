<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

/**
 * Coupon updated event class.
 *
 * @since x.y.z
 */
class CouponUpdatedEvent extends AbstractCouponEvent
{
    /**
     * CouponUpdatedEvent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->action = 'update';
    }
}
