<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce;

/**
 * Repository for handling WooCommerce orders.
 *
 * @since x.y.z
 */
class OrdersRepository
{
    /**
     * Gets a WooCommerce order object.
     *
     * @since x.y.z
     *
     * @param int order ID
     * @return \WC_Order|null
     */
    public static function get(int $id)
    {
        return wc_get_order($id) ?: null;
    }
}
