<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\AbstractProductEvent;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractProductEvent
 */
final class AbstractProductEventTest extends WPTestCase
{
    /**
     * Tests that can return event data properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractProductEvent::getData()
     */
    public function testCanGetData(): void
    {
        $product = Mockery::mock('WC_Product');
        $product->shouldReceive('get_id')->andReturn(1);
        $product->shouldReceive('get_type')->andReturn('simple');
        $product->shouldReceive('get_status')->andReturn('publish');

        $mock = $this->getMockForAbstractClass(AbstractProductEvent::class);

        $this->assertEquals([], $mock->getData());

        $mock->setWooCommerceProduct($product);

        $this->assertEquals([
            'product' => [
                'id' => 1,
                'type' => 'simple',
                'status' => 'publish',
            ],
        ], $mock->getData());
    }

    /**
     * Tests that can set the product property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\AbstractProductEvent::setWooCommerceProduct()
     */
    public function testCanSetWooCommerceProduct(): void
    {
        $mock = $this->getMockForAbstractClass(AbstractProductEvent::class);
        $product = Mockery::mock('WC_Product');

        $this->assertInstanceOf(AbstractProductEvent::class, $mock->setWooCommerceProduct($product));
        $this->assertSame($product, TestHelpers::getInaccessibleProperty(AbstractProductEvent::class, 'product')->getValue($mock));
    }
}
