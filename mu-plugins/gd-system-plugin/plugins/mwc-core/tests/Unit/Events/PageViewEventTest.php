<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\PageViewEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\ShippingZoneMethodAddedEvent
 */
final class PageViewEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\PageViewEvent::__construct()
     * @covers \GoDaddy\WordPress\MWC\Core\Events\PageViewEvent::getData()
     */
    public function testCanGetData()
    {
        $screen = new Screen([
            'pageId'       => 'plugins',
            'pageContexts' => ['plugins'],
        ]);

        $event = new PageViewEvent($screen);

        $this->assertSame($screen->toArray(), $event->getData());
    }
}
