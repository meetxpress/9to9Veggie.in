<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\Producers\ShippingZoneMethodEventsProducer;
use GoDaddy\WordPress\MWC\Core\Events\ShippingZoneMethodAddedEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ShippingZoneMethodEventsProducer
 */
final class ShippingZoneMethodEventsProducerTest extends WPTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Tests that the producer sets up properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ShippingZoneMethodEventsProducer::setup()
     * @throws Exception
     */
    public function testCanSetup()
    {
        $producer = new ShippingZoneMethodEventsProducer();

        WP_Mock::expectActionAdded('woocommerce_shipping_zone_method_added', [$producer, 'fireShippingZoneMethodAddedEvent'], 10, 3);

        WP_Mock::expectActionAdded('admin_init', [$producer, 'maybeFireLocalPickupShippingMethodAddedEvent'], 10, 1);

        $producer->setup();

        $this->assertConditionsMet();
    }

    /**
     * Tests that the producer fires shipping zone method added event correctly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ShippingZoneMethodEventsProducer::fireShippingZoneMethodAddedEvent()
     *
     * @dataProvider providerGetScenarios
     *
     * @param $type
     * @param $shippingZoneId
     * @throws Exception
     */
    public function testWillFireShippingZoneMethodAddedEvent($type, $shippingZoneId)
    {
        // Mock the Events class and listen for broadcast calls
        $mock = Mockery::mock('alias:'.Events::class);

        // Ensure the event is correctly broadcast when all arguments are present, and no events are broadcast when either type or zone id is missing
        if ($type && $shippingZoneId) {
            $mock->shouldReceive('broadcast')->once()->withArgs(function ($event) use ($type, $shippingZoneId) {
                return $event instanceof ShippingZoneMethodAddedEvent && $this->eventHasShippingZone($event, $shippingZoneId) && $this->eventHasShippingMethodType($event, $type);
            });
        } else {
            $mock->shouldNotReceive('broadcast');
        }

        (new ShippingZoneMethodEventsProducer())->fireShippingZoneMethodAddedEvent(null, $type, $shippingZoneId);
    }

    /**
     * @see tests above
     *
     * @return array[]
     */
    public function providerGetScenarios() : array
    {
        return [
            ['flat_rate', 1],
            ['local_pickup', 2],
            ['foo', null],
            ['', 0],
        ];
    }

    /**
     * Determines whether the event has the given shipping zone ID.
     *
     * @param $event
     * @param $shippingZoneId
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasShippingZone($event, $shippingZoneId) : bool
    {
        return TestHelpers::getInaccessibleProperty(ShippingZoneMethodAddedEvent::class, 'shippingZoneId')->getValue($event) === $shippingZoneId;
    }

    /**
     * Determines whether the event has the given shipping method type.
     *
     * @param $event
     * @param $shippingMethodType
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasShippingMethodType($event, $shippingMethodType) : bool
    {
        return TestHelpers::getInaccessibleProperty(ShippingZoneMethodAddedEvent::class, 'shippingMethodType')->getValue($event) === $shippingMethodType;
    }

    /**
     * Tests that the producer fires shipping zone method added event correctly.
     *
     * @covers       \GoDaddy\WordPress\MWC\Core\Events\Producers\ShippingZoneMethodEventsProducer::maybeFireLocalPickupShippingMethodAddedEvent()
     * @dataProvider providerWillFireLocalPickupShippingMethodAddedEvent
     *
     * @param string $toFireEvent
     * @param array $shippingZones
     * @param bool $shouldFire
     *
     * @throws Exception
     */
    public function testWillFireLocalPickupShippingMethodAddedEvent(string $toFireEvent, array $shippingZones, bool $shouldFire)
    {
        // Mock the Events class and listen for broadcast calls
        $mock = Mockery::mock('alias:'.Events::class);

        Configuration::set('woocommerce.flags.maybeFireLocalPickupShippingMethodAddedEvent', $toFireEvent);

        Mockery::mock('alias:WC_Shipping_Zones')->shouldReceive('get_zones')->andReturn($shippingZones);

        if ('yes' === $toFireEvent) {

            if ($shouldFire) {
                WP_Mock::userFunction('update_option')->once();

                $mock->shouldReceive('broadcast');
            } else {
                $mock->shouldNotReceive('broadcast');
            }
        } else {
            $mock->shouldNotReceive('broadcast');
        }

        (new ShippingZoneMethodEventsProducer())->maybeFireLocalPickupShippingMethodAddedEvent();
    }

    /**
     * @see testWillFireLocalPickupShippingMethodAddedEvent
     *
     * @return array[]
     */
    public function providerWillFireLocalPickupShippingMethodAddedEvent() : array
    {
        return [
            'Event already fired'               => ['no', [], false],
            'Zones without native Local Pickup' => [
                'yes',
                [
                    [
                        'id'               => 1,
                        'shipping_methods' => []
                    ],
                    [
                        'id'               => 2,
                        'shipping_methods' => [
                            $this->getDummyShippingMethodObject('flat_rate')
                        ]
                    ],
                ],
                false
            ],
            'Zones with native Local Pickup'    => [
                'yes',
                [
                    [
                        'id'               => 3,
                        'shipping_methods' => [
                            $this->getDummyShippingMethodObject('free_shipping')
                        ]
                    ],
                    [
                        'id'               => 4,
                        'shipping_methods' => [
                            $this->getDummyShippingMethodObject('flat_rate'),
                            $this->getDummyShippingMethodObject('local_pickup')
                        ]
                    ],
                ],
                true
            ],
        ];
    }

    /**
     * Gets dummy shipping method with given ID.
     *
     * @param string $methodId
     *
     * @return object
     */
    private function getDummyShippingMethodObject(string $methodId)
    {
        return new class($methodId) {
            public $id;

            public function __construct($methodId)
            {
                $this->id = $methodId;
            }
        };
    }
}
