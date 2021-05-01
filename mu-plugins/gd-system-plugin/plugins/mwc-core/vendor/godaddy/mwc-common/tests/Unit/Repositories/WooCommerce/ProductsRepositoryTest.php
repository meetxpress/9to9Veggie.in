<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories\WooCommerce;

use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository
 */
final class ProductsRepositoryTest extends WPTestCase
{
    /**
     * Tests that can get a product.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository::get()
     * @dataProvider providerCanGetProduct
     *
     * @param int $productId
     * @param \WC_Product|false $foundProduct
     * @param \WC_Product|null
     */
    public function testCanGetProduct(int $productId, $foundProduct, $expectedResult)
    {
        WP_Mock::userFunction('wc_get_product')->withArgs([$productId])->andReturn($foundProduct);

        $this->assertSame($expectedResult, ProductsRepository::get($productId));
    }

    /** @see testCanGetProduct */
    public function providerCanGetProduct() : array
    {
        $product = Mockery::mock('WC_Product');

        return [
            'Product not found' => [123, false, null],
            'Product found'     => [123, $product, $product],
        ];
    }
}
