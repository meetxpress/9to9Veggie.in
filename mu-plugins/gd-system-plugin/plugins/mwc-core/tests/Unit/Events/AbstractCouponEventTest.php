<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractCouponEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractCouponEvent
 */
final class AbstractCouponEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractCouponEvent::getData()
     */
    public function testCanGetData(): void
    {
        $coupon = Mockery::mock('WC_Coupon');
        $coupon->shouldReceive('get_id')->andReturn(1);
        $coupon->shouldReceive('get_code')->andReturn('TEST_COUPON');
        $coupon->shouldReceive('get_discount_type')->andReturn('percentage');
        $coupon->shouldReceive('get_amount')->andReturn(10);

        $mock = $this->getMockForAbstractClass(AbstractCouponEvent::class);

        $this->assertEquals([], $mock->getData());

        $mock->setWooCommerceCoupon($coupon);

       $this->assertEquals([
            'coupon' => [
                'id' => 1,
                'code' => 'TEST_COUPON',
                'discountType' => 'percentage',
                'discountAmount' => 10,
            ],
        ], $mock->getData());
    }

    /**
     * Tests that can set the coupon property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractCouponEvent::setWooCommerceCoupon()
     */
    public function testCanSetWooCommerceCoupon(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractCouponEvent::class);
        $coupon = Mockery::mock('WC_Coupon');

        $this->assertInstanceOf(AbstractCouponEvent::class, $mock->setWooCommerceCoupon($coupon));
        $this->assertSame($coupon, TestHelpers::getInaccessibleProperty(AbstractCouponEvent::class, 'coupon')->getValue($mock));
    }
}
