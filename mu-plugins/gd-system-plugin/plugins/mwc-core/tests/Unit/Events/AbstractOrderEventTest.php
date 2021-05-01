<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractOrderEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderEvent
 */
final class AbstractOrderEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderEvent::getData()
     */
    public function testCanGetData() : void
    {
        $order = Mockery::mock('WC_Order');
        $order->shouldReceive('get_id')->andReturn(1);

        $mock = $this->getMockForAbstractClass(AbstractOrderEvent::class);

        $this->assertEquals([], $mock->getData());

        $mock->setWooCommerceOrder($order);

        $this->assertEquals(['id' => 1], $mock->getData());
    }

    /**
     * Tests that can set the order property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractOrderEvent::setWooCommerceOrder()
     * @throws ReflectionException
     */
    public function testCanSetWooCommerceOrder() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractOrderEvent::class);
        $order = Mockery::mock('WC_Order');

        $this->assertInstanceOf(AbstractOrderEvent::class, $mock->setWooCommerceOrder($order));
        $this->assertSame($order, TestHelpers::getInaccessibleProperty(AbstractOrderEvent::class, 'order')->getValue($mock));
    }
}
