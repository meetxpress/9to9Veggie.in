<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Enqueue\Types;

use Exception;
use GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle
 */
final class EnqueueStyleTest extends WPTestCase
{
    /**
     * Tests that it can set the media context for the stylesheet to be enqueued.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::setMedia()
     * @throws ReflectionException
     */
    public function testCanSetMedia()
    {
        $style = new EnqueueStyle();
        $property = TestHelpers::getInaccessibleProperty($style, 'media');

        $this->assertEquals('all', $property->getValue($style));

        $style->setMedia('print');

        $this->assertEquals('print', $property->getValue($style));
    }

    /**
     * Tests that it will not be able to execute if no handle is set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCantExecuteWithoutHandle()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_style', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 0]);
        (new EnqueueStyle())->execute();
    }

    /**
     * Tests that it will not be able to execute if a handle is set but is invalid.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCantExecuteWithInvalidHandle()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_style', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 0]);
        (new EnqueueStyle())->setHandle('')->execute();
    }

    /**
     * Tests that it will not be able to execute if a handle is set but no source is specified.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCantExecuteWithoutSource()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_style', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 0]);
        (new EnqueueStyle())->setHandle('test')->execute();
    }

    /**
     * Tests that it will not execute if there is a condition that fails.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCantExecuteWhenConditionFail()
    {
        WP_Mock::userFunction('wp_register_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 0]);
        (new EnqueueStyle())->setHandle('test')->setSource('https://example.com/fake.css')->setCondition(function () {
            return false;
        })->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it will execute if there is a condition that passes.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCanExecuteWhenConditionPass()
    {
        WP_Mock::userFunction('wp_register_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        (new EnqueueStyle())->setHandle('test')->setSource('https://example.com/fake.css')->setCondition(function () {
            return true;
        })->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it can execute a style enqueue when no conditions are set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::execute()
     * @throws Exception
     */
    public function testCanExecute()
    {
        WP_Mock::userFunction('wp_register_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        (new EnqueueStyle())->setHandle('test')->setSource('https://example.com/fake.css')->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it will not be able to execute if no handle is set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::validate()
     * @throws Exception
     */
    public function testCantValidateWithoutHandle()
    {
        $this->expectException(Exception::class);
        (new EnqueueStyle())->validate();
    }

    /**
     * Tests that it will not be able to validate if a handle is set but is invalid.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::validate()
     * @throws Exception
     */
    public function testCantValidateWithInvalidHandle()
    {
        $this->expectException(Exception::class);
        (new EnqueueStyle())->setHandle('')->validate();
    }

    /**
     * Tests that it will not be able to validate if a handle is set but no source is specified.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle::validate()
     * @throws Exception
     */
    public function testCantValidateWithoutSource()
    {
        $this->expectException(Exception::class);
        (new EnqueueStyle())->setHandle('test')->validate();
    }
}
