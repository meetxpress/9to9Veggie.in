<?php

namespace GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce;

/**
 * Repository for handling WooCommerce products.
 *
 * @since x.y.z
 */
class ProductsRepository
{
    /**
     * Gets a WooCommerce product object.
     *
     * @since x.y.z
     *
     * @param int $id product ID
     * @return \WC_Product|null
     */
    public static function get(int $id)
    {
        return wc_get_product($id) ?: null;
    }
}
