<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce;

/**
 * Repository for handling WooCommerce coupons.
 *
 * @since x.y.z
 */
class CouponsRepository
{
    /**
     * Gets a WooCommerce coupon object.
     *
     * @since x.y.z
     *
     * @param int|string $identifier coupon identifier, like an ID or code
     * @return \WC_Coupon|null
     */
    public static function get($identifier)
    {
        $coupon = new \WC_Coupon($identifier);

        return is_callable([$coupon, 'get_id']) && $coupon->get_id() ? $coupon : null;
    }
}
