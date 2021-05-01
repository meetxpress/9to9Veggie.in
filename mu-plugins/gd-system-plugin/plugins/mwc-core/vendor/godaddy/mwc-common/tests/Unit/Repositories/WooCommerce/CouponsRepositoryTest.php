<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories\WooCommerce;

use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository
 */
final class CouponsRepositoryTest extends WPTestCase
{
    /**
     * Tests that can get a coupon.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository::get()
     */
    public function testCanGetCoupon()
    {
        Mockery::mock('WC_Coupon');

        // @TODO needs better test, but method references an external object's method inside a static method {FN 2021-03-23}
        $this->assertNull(CouponsRepository::get(null));
    }
}
