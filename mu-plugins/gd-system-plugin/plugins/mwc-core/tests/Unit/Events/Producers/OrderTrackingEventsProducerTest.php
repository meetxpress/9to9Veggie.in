<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractOrderTrackingInformationEvent;
use GoDaddy\WordPress\MWC\Core\Events\OrderTrackingInformationCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\OrderTrackingInformationUpdatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionException;
use WC_Order;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer
 */
final class OrderTrackingEventsProducerTest extends WPTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Tests that the producer sets up properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::setup()
     * @throws Exception
     */
    public function testCanSetup()
    {
        $producer = new OrderTrackingEventsProducer();

        WP_Mock::expectActionAdded('woocommerce_process_shop_order_meta', [$producer, 'addHooks'], -10);
        WP_Mock::expectActionAdded('woocommerce_process_shop_order_meta', [$producer, 'removeHooks'], 10);
        WP_Mock::expectActionAdded('wp_ajax_wc_shipment_tracking_save_form', [$producer, 'addHooks'], 0);
        WP_Mock::expectActionAdded('wp_ajax_wc_shipment_tracking_save_form', [$producer, 'removeHooks'], 20);

        $producer->setup();

        $this->assertConditionsMet();
    }

    /**
     * Tests that the producer adds and removes hooks properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::addHooks()
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::removeHooks()
     * @throws Exception
     */
    public function testCanAddAndRemoveHooks()
    {
        $producer = new OrderTrackingEventsProducer();

        $this->assertCount(0, $this->getProducerHooksValue($producer));

        WP_Mock::expectActionAdded('added_post_meta', [$producer, 'maybeFireOrderTrackingInformationCreatedEvent'], 20, 4);
        WP_Mock::expectActionAdded('updated_post_meta', [$producer, 'maybeFireOrderTrackingInformationUpdatedEvent'], 20, 4);

        $producer->addHooks();

        $this->assertConditionsMet();
        $this->assertCount(2, $this->getProducerHooksValue($producer));

        // TODO: Use WP_Mock::expectActionNotAdded when WP_Mock supports the remove_action method {IT 2021-03-26}
        WP_Mock::userFunction('remove_action')->twice();

        $producer->removeHooks();
    }

    /**
     * Tests that the producer fires order tracking events correctly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::maybeFireOrderTrackingInformationCreatedEvent()
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::maybeFireOrderTrackingInformationUpdatedEvent()
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\OrderTrackingEventsProducer::fireOrderTrackingInformationEvent()
     *
     * @dataProvider providerGetScenarios
     *
     * @param string $method
     * @param null $orderId
     * @param string $metaKey
     * @param null $metaValue
     * @param string $expectedEventClass
     */
    public function testWillFireOrderTrackingEvents(string $method, $orderId = null, string $metaKey = '', $metaValue = null, string $expectedEventClass = '')
    {
        // Mock the Events class and listen for broadcast calls
        $mock = Mockery::mock('alias:'.Events::class);

        // Ensure the event is correctly broadcast when all arguments are present, and no events are broadcast when order ID is missing or different meta key present
        if ($orderId && $expectedEventClass) {
            $order = Mockery::mock('WC_Order');

            Mockery::mock('alias:'.OrdersRepository::class)
                ->shouldReceive('get')
                ->andReturn($order);

            $mock->shouldReceive('broadcast')->once()->withArgs(function ($event) use ($expectedEventClass, $order, $metaValue) {
                return $event instanceof $expectedEventClass && $this->eventHasOrder($event, $order) && $this->eventHasTrackingItems($event, $metaValue);
            });
        } else {
            $mock->shouldNotReceive('broadcast');
        }

        (new OrderTrackingEventsProducer())->$method(null, $orderId, $metaKey, $metaValue);
    }

    /**
     * @see tests above
     *
     * @return array[]
     */
    public function providerGetScenarios() : array
    {
        return [
            ['maybeFireOrderTrackingInformationCreatedEvent', 1, '_wc_shipment_tracking_items', ['foo', 'bar'], OrderTrackingInformationCreatedEvent::class],
            ['maybeFireOrderTrackingInformationCreatedEvent', 2, '_some_other_meta', 'baz'],
            ['maybeFireOrderTrackingInformationCreatedEvent'],
            ['maybeFireOrderTrackingInformationUpdatedEvent', 1, '_wc_shipment_tracking_items', ['foo', 'bar'], OrderTrackingInformationUpdatedEvent::class],
            ['maybeFireOrderTrackingInformationUpdatedEvent', 2, '_some_other_meta', 'baz'],
            ['maybeFireOrderTrackingInformationUpdatedEvent'],
        ];
    }

    /**
     * Gets the protected hooks value of OrderTrackingEventsProducer.
     *
     * @param OrderTrackingEventsProducer $producer
     * @return array
     * @throws ReflectionException
     */
    private function getProducerHooksValue(OrderTrackingEventsProducer $producer) : array
    {
        return TestHelpers::getInaccessibleProperty(OrderTrackingEventsProducer::class, 'hooks')->getValue($producer);
    }

    /**
     * Checks whether the event has the given order.
     *
     * @param AbstractOrderTrackingInformationEvent $event
     * @param WC_Order $order
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasOrder(AbstractOrderTrackingInformationEvent $event, WC_Order $order) : bool
    {
        return TestHelpers::getInaccessibleProperty(get_class($event), 'order')->getValue($event) === $order;
    }

    /**
     * Checks whether the event has the given tracking items.
     *
     * @param AbstractOrderTrackingInformationEvent $event
     * @param array $trackingItems
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasTrackingItems(AbstractOrderTrackingInformationEvent $event, array $trackingItems) : bool
    {
        return TestHelpers::getInaccessibleProperty(get_class($event), 'trackingItems')->getValue($event) === $trackingItems;
    }
}
