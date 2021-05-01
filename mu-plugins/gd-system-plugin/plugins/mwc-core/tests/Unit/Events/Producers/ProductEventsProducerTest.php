<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractProductEvent;
use GoDaddy\WordPress\MWC\Core\Events\Producers\ProductEventsProducer;
use GoDaddy\WordPress\MWC\Core\Events\ProductCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\ProductUpdatedEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionException;
use WC_Product;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ProductEventsProducer
 */
final class ProductEventsProducerTest extends WPTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Tests that the producer sets up properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ProductEventsProducer::setup()
     * @throws Exception
     */
    public function testCanSetup()
    {
        $producer = new ProductEventsProducer();

        WP_Mock::expectActionAdded('wp_insert_post', [$producer, 'maybeFlagNewProduct'], 10, 3);
        WP_Mock::expectActionAdded('woocommerce_process_product_meta', [$producer, 'maybeFireProductEvents']);

        $producer->setup();

        $this->assertConditionsMet();
    }

    /**
     * Tests that new product is flagged properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\ProductEventsProducer::maybeFlagNewProduct()
     * @throws Exception
     */
    public function testFlagsNewProduct()
    {
        $post = $this->mockPost(['post_type' => 'post']);
        $product = $this->mockPost(['post_type' => 'product']);

        // Mock the NewWooCommerceObjectFlag, ensure it only receives turnOn() once
        $flag = $this->createNewWooCommerceObjectFlagMock();
        $flag->expects($this->once())->method('turnOn');

        $producer = $this->createProductEventProducerWithMockedNewProductFlag($flag);

        // Try flagging both a regular post and shop product - only the product should be flagged
        $producer->maybeFlagNewProduct($post->ID, $post, false);
        $producer->maybeFlagNewProduct($product->ID, $product, false);
    }

    /**
     * Tests that the producer fires product events correctly.
     *
     * @covers\GoDaddy\WordPress\MWC\Core\Events\Producers\ProductEventsProducer::maybeFireProductEvents()
     *
     * @dataProvider providerGetScenarios
     *
     * @param bool $isNew
     * @param string $expectedEventClass
     * @throws Exception
     */
    public function testWillFireProductEvents(bool $isNew, string $expectedEventClass)
    {
        $productPost = $this->mockPost(['post_type' => 'product']);
        $product = Mockery::mock('WC_Product');

        // Ensure product returns the same ID as the post object
        $product->shouldReceive('get_id')->andReturn($productPost->ID);

        Mockery::mock('alias:'.ProductsRepository::class)
            ->shouldReceive('get')
            ->andReturn($product);

        // Mock the Events class and listen for broadcast calls and ensure the correct event is broadcast
        Mockery::mock('alias:'.Events::class)
            ->shouldReceive('broadcast')
            ->once()
            ->withArgs(function ($event) use ($expectedEventClass, $product) {
                return $event instanceof $expectedEventClass && $this->eventHasProduct($event, $product);
            });

        // Mock the NewWooCommerceObjectFlag and set our expectations
        $flag = $this->createNewWooCommerceObjectFlagMock();
        $flag->expects($this->once())->method('isOn')->willReturn($isNew);

        if ($isNew) {
            $flag->expects($this->once())->method('turnOff');
        }

        // Fire!
        $this->createProductEventProducerWithMockedNewProductFlag($flag)
            ->maybeFireProductEvents($product->get_id(), $productPost);
    }

    /**
     * Tests that the producer will not fire events for non-products.
     *
     * @throws Exception
     */
    public function testWontFireProductEvents()
    {
        Mockery::mock('WC_Product');

        // Mock the Events class and listen for broadcast calls and ensure no events will be broadcast
        Mockery::mock('alias:'.Events::class)
            ->shouldNotReceive('broadcast');

        // Ensure the repo returns no results
        Mockery::mock('alias:'.ProductsRepository::class)
            ->shouldReceive('get')
            ->andReturn(null);

        (new ProductEventsProducer())->maybeFireProductEvents('foo', 'bar');
    }

    /**
     * @return array
     * @see tests above
     */
    public function providerGetScenarios(): array
    {
        return [
            'new product' => ['isNew' => true, 'eventClass' => ProductCreatedEvent::class],
            'edit product' => ['isNew' => false, 'eventClass' => ProductUpdatedEvent::class],
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
     * Mocks the getNewProductFlag method on the ProductEventsProducer to return a mocked NewWooCommerceObjectFlag and
     * ensures the method is called only once. Because a new instance of NewWooCommerceObjectFlag is created when
     * calling maybeFlagNewProduct(), we need to wrap the instantiation of the flag to a separate method that we can mock,
     * so that we don't have to deal with NewWooCommerceObjectFlag implementation details.
     *
     * @param NewWooCommerceObjectFlag $flag
     * @return ProductEventsProducer
     */
    private function createProductEventProducerWithMockedNewProductFlag(NewWooCommerceObjectFlag $flag): ProductEventsProducer
    {
        $producer = $this->createPartialMock(ProductEventsProducer::class, ['getNewProductFlag']);
        $producer->expects($this->once())->method('getNewProductFlag')->willReturn($flag);

        return $producer;
    }

    /**
     * Checks whether the event has the given product.
     *
     * @param AbstractProductEvent $event
     * @param WC_Product $product
     * @return bool
     * @throws ReflectionException
     */
    private function eventHasProduct(AbstractProductEvent $event, WC_Product $product) : bool
    {
        return TestHelpers::getInaccessibleProperty(get_class($event), 'product')->getValue($event) === $product;
    }
}
