<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractCouponEvent;
use GoDaddy\WordPress\MWC\Core\Events\CouponCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\CouponUpdatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\Producers\CouponEventsProducer;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionException;
use WC_Coupon;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\CouponEventsProducer
 */
final class CouponEventsProducerTest extends WPTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Tests that the producer sets up properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\CouponEventsProducer::setup()
     * @throws Exception
     */
    public function testCanSetup()
    {
        $producer = new CouponEventsProducer();

        WP_Mock::expectActionAdded('wp_insert_post', [$producer, 'maybeFlagNewCoupon'], 10, 3);
        WP_Mock::expectActionAdded('woocommerce_process_shop_coupon_meta', [$producer, 'maybeFireCouponEvents']);

        $producer->setup();

        $this->assertConditionsMet();
    }

    /**
     * Tests that new coupon is flagged properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\CouponEventsProducer::maybeFlagNewCoupon()
     * @throws Exception
     */
    public function testCanFlagsNewCoupon()
    {
        $post = $this->mockPost(['post_type' => 'post']);
        $coupon = $this->mockPost(['post_type' => 'shop_coupon']);

        // Mock the NewWooCommerceObjectFlag, ensure it only receives turnOn() once
        $flag = $this->createNewWooCommerceObjectFlagMock();
        $flag->expects($this->once())->method('turnOn');

        $producer = $this->createCouponEventProducerWithMockedNewCouponFlag($flag);

        // Try flagging both a regular post and shop coupon - only the coupon should be flagged
        $producer->maybeFlagNewCoupon($post->ID, $post, false);
        $producer->maybeFlagNewCoupon($coupon->ID, $coupon, false);
    }

    /**
     * Tests that the producer fires coupon events correctly.
     *
     * @covers\GoDaddy\WordPress\MWC\Core\Events\Producers\CouponEventsProducer::maybeFireCouponEvents()
     *
     * @dataProvider providerWillFireCouponEvents
	 *
     * @param bool $isNew
     * @param string $expectedEventClass
     *
     * @throws Exception
     */
    public function testWillFireCouponEvents(bool $isNew, string $expectedEventClass)
    {
        $couponPost = $this->mockPost(['post_type' => 'shop_coupon']);
        $coupon = Mockery::mock('WC_Coupon');

        // Ensure coupon returns the same ID as the post object
        $coupon->shouldReceive('get_id')->andReturn($couponPost->ID);

        Mockery::mock('alias:'.CouponsRepository::class)
            ->shouldReceive('get')
            ->andReturn($coupon);

        // Mock the Events class and listen for broadcast calls and ensure the correct event is broadcast
        Mockery::mock('alias:'.Events::class)
            ->shouldReceive('broadcast')
            ->once()
            ->withArgs(function ($event) use ($expectedEventClass, $coupon) {
                return $event instanceof $expectedEventClass && $this->eventHasCoupon($event, $coupon);
            });

        // Mock the NewWooCommerceObjectFlag and set our expectations
        $flag = $this->createNewWooCommerceObjectFlagMock();
        $flag->expects($this->once())->method('isOn')->willReturn($isNew);

        if ($isNew) {
            $flag->expects($this->once())->method('turnOff');
        }

        // Fire!
        $this->createCouponEventProducerWithMockedNewCouponFlag($flag)
            ->maybeFireCouponEvents($coupon->get_id());
    }

    /**
     * Tests that the producer will not fire events for non-coupons.
     *
     * @throws Exception
     */
    public function testWontFireCouponEvents()
    {
        Mockery::mock('WC_Coupon');

        // Ensure repository returns no coupon
        Mockery::mock('alias:'.CouponsRepository::class)
            ->shouldReceive('get')
            ->andReturn(null);

        // Ensure no events will be broadcast
        Mockery::mock('alias:'.Events::class)
            ->shouldNotReceive('broadcast');

        (new CouponEventsProducer())->maybeFireCouponEvents('foo');
    }

    /**
     * @return array
     * @see tests above
     */
    public function providerWillFireCouponEvents(): array
    {
        return [
            'new coupon' => ['isNew' => true, 'eventClass' => CouponCreatedEvent::class],
            'edit coupon' => ['isNew' => false, 'eventClass' => CouponUpdatedEvent::class],
        ];
    }

    /**
     * @return NewWooCommerceObjectFlag
     */
    private function createNewWooCommerceObjectFlagMock(): NewWooCommerceObjectFlag
    {
        return $this->createMock(NewWooCommerceObjectFlag::class);
    }

    /**
     * Mocks the getNewCouponFlag method on the CouponEventsProducer to return a mocked NewWooCommerceObjectFlag and
     * ensures the method is called only once. Because a new instance of NewWooCommerceObjectFlag is created when
     * calling maybeFlagNewCoupon(), we need to wrap the instantiation of the flag to a separate method that we can mock,
     * so that we don't have to deal with NewWooCommerceObjectFlag implementation details.
     *
     * @param NewWooCommerceObjectFlag $flag
     * @return CouponEventsProducer
     */
    private function createCouponEventProducerWithMockedNewCouponFlag(NewWooCommerceObjectFlag $flag): CouponEventsProducer
    {
        $producer = $this->createPartialMock(CouponEventsProducer::class, ['getNewCouponFlag']);
        $producer->expects($this->once())->method('getNewCouponFlag')->willReturn($flag);

        return $producer;
    }

    /**
     * Checks whether the event has the given coupon.
     *
     * @param AbstractCouponEvent $event
     * @param WC_Coupon $coupon
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasCoupon(AbstractCouponEvent $event, WC_Coupon $coupon) : bool
    {
        return TestHelpers::getInaccessibleProperty(get_class($event), 'coupon')->getValue($event) === $coupon;
    }
}
