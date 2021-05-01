<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Core\Events\ShippingZoneMethodAddedEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\ShippingZoneMethodAddedEvent
 */
final class ShippingZoneMethodAddedEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\ShippingZoneMethodAddedEvent::getData()
     */
    public function testCanGetData(): void
    {
        $event = new ShippingZoneMethodAddedEvent(99, 'flat_rate');

        $this->assertEquals([
            'shippingZone' => [
                'id' => 99,
            ],
            'shippingMethod' => [
                'type' => 'flat_rate',
            ],
        ], $event->getData());
    }
}
