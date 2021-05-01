<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractOrderTrackingInformationEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderTrackingInformationEvent
 */
final class AbstractOrderTrackingInformationEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderTrackingInformationEvent::getData()
     */
    public function testCanGetData() : void
    {
        $order = Mockery::mock('WC_Order');
        $order->shouldReceive('get_id')->andReturn(1);

        $trackingItems = ['foo', 'bar'];

        $mock = $this->getMockForAbstractClass(AbstractOrderTrackingInformationEvent::class);

        $this->assertEquals([], $mock->getData());

        $mock->setWooCommerceOrder($order);
        $mock->setTrackingItems($trackingItems);

        $this->assertEquals(['order' => ['id' => 1], 'trackingItems' => $trackingItems], $mock->getData());
    }

    /**
     * Tests that can set the tracking items property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderTrackingInformationEvent::setTrackingItems()
     */
    public function testCanSetTrackingItems() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractOrderTrackingInformationEvent::class);
        $items = ['foo'];

        $this->assertInstanceOf(AbstractOrderTrackingInformationEvent::class, $mock->setTrackingItems($items));
        $this->assertSame($items, TestHelpers::getInaccessibleProperty(AbstractOrderTrackingInformationEvent::class, 'trackingItems')->getValue($mock));
    }
}
