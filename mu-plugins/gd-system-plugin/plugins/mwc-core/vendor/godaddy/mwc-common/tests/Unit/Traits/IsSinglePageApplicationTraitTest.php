<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait
 */
final class IsSinglePageApplicationTraitTest extends WPTestCase
{
    /**
     * Gets an instance of a class implementing the trait.
     */
    private function getInstance()
    {
        return new class {
            use IsSinglePageApplicationTrait;

            public function __construct()
            {
                $this->appHandle = 'testHandle';
                $this->appSource = 'https://example.com/source.js';
                $this->divId = 'testDivId';
            }
        };
    }

    /**
     * Tests that it can render the page HTML.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait::render()
     * @throws ReflectionException
     */
    public function testCanRender()
    {
        $instance = $this->getInstance();
        $property = TestHelpers::getInaccessibleProperty($instance, 'divId');
        $styles   = TestHelpers::getInaccessibleProperty($instance, 'divStyles');
        $property->setValue($instance, 'testId');
        $styles->setValue($instance, 'testStyles');

        $this->expectOutputRegex('/'.preg_quote('<div id="testId" style="testStyles"></div>', '/').'/');

        $instance->render();
    }

    /**
     * Tests that it can enqueue the single page application script.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait::enqueueApp()
     */
    public function testCanEnqueueApp()
    {
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('wp_register_script', ['times' => 1]);
        $this->getInstance()->enqueueApp();

        $this->assertTrue(true);
    }
}
