<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories\WooCommerce;

use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository
 */
final class OrdersRepositoryTest extends WPTestCase
{
    /**
     * Tests that can get an order.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository::get()
     * @dataProvider providerCanGetOrder
     *
     * @param int $orderId
     * @param \WC_Order|false $foundOrder
     * @param \WC_Order|null
     */
    public function testCanGetOrder(int $orderId, $foundOrder, $expectedResult)
    {
        WP_Mock::userFunction('wc_get_order')->withArgs([$orderId])->andReturn($foundOrder);

        $this->assertSame($expectedResult, OrdersRepository::get($orderId));
    }

    /** @see testCanGetOrder */
    public function providerCanGetOrder() : array
    {
        $order = Mockery::mock('WC_Order');

        return [
            'Order not found' => [123, false, null],
            'Order found'     => [123, $order, $order],
        ];
    }
}
