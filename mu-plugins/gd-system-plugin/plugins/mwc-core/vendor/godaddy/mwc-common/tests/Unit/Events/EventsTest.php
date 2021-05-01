<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Events;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\SubscriberContract;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use TypeError;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Events\Events
 */
final class EventsTest extends WPTestCase
{
    /**
     * Tests that can broadcast events.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Events\Events::broadcast()
     * @dataProvider provideCanBroadcast
     *
     * @throws Exception
     */
    public function testCanBroadcast($events, $eventCount, $shouldFail = false)
    {
        $method = TestHelpers::getInaccessibleMethod(Events::class, 'broadcast');
        $class  = $this->mockStaticMethod(Events::class, 'broadcastEvent')
            ->times($eventCount);

        if ($shouldFail) {
            $this->expectException(TypeError::class);
        }

        $method->invoke($class, $events);

        $this->assertConditionsMet();
    }

    /** @see testCanBroadcast */
    public function provideCanBroadcast() : array
    {
        return [
            [[
                // @NOTE: MUST BE ON SEPARATE LINES!
                new class implements EventContract {},
                new class implements EventContract {},
                new class implements EventContract {},
            ], 3],
            [new class implements EventContract {}, 1],
            [new class {}, 0, true],
        ];
    }

    /**
     * Tests that can get subscribers for a given event
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Events\Events::getSubscribers()
     *
     * @throws Exception
     */
    public function testCanGetSubscribers()
    {
        // @NOTE: Need unique names so have to declare inline since class name is based on line location
        $eventWithSubs = new DummyEventWithSubs();
        $eventWithoutSubs = new DummyEventWithoutSubs();
        $sub1        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };
        $sub2        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };
        $sub3        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };
        $subscribers = [get_class($sub1), get_class($sub2), get_class($sub3)];

        Configuration::set('events.listeners', [
            get_class($eventWithSubs)    => $subscribers,
            get_class($eventWithoutSubs) => [],
        ]);

        $this->assertEquals($subscribers, Events::getSubscribers($eventWithSubs));
        $this->assertEquals([], Events::getSubscribers($eventWithoutSubs));
    }

    /**
     * Tests that can determine if has subscriber for a given event
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Events\Events::hasSubscriber()
     *
     * @throws Exception
     */
    public function testCanDetermineIfHasSubscriber()
    {
        // @NOTE: Need unique names so have to declare inline since class name is based on line location
        $eventWithSubs = new DummyEventWithSubs();
        $eventWithoutSubs = new DummyEventWithoutSubs();
        $randomEvent = new DummyRandomEvent();
        $sub1        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };
        $sub2        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };
        $sub3        = new class implements SubscriberContract {
            public function handle(EventContract $event) {}
        };

        Configuration::set('events.listeners', [
            get_class($eventWithSubs) => [get_class($sub1), get_class($sub2), get_class($sub3)]
        ]);

        $this->assertTrue(Events::hasSubscriber($eventWithSubs, $sub1));
        $this->assertTrue(Events::hasSubscriber($eventWithSubs, $sub3));
        $this->assertFalse(Events::hasSubscriber($eventWithoutSubs, $sub3));
        $this->assertFalse(Events::hasSubscriber($randomEvent, $sub2));
    }
}

final class DummyEventWithSubs implements EventContract
{

}

final class DummyEventWithoutSubs implements EventContract
{

}

final class DummyRandomEvent implements EventContract
{

}
