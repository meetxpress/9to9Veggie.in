<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;
 */
final class IsEventBridgeEventTraitTest extends WPTestCase
{
    /**
     * Tests that can get the event resource.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait::getResource()
     * @dataProvider providerGetProperties
     *
     * @param mixed $value
     * @param mixed $expectedResult
     */
    public function testCanGetResource($value, $expectedResult)
    {
        $trait = $this->getMockInstance('resource', $value);

        $this->assertEquals($expectedResult, $trait->getResource());
    }

    /**
     * Tests that can get the event action.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait::getAction()
     * @dataProvider providerGetProperties
     *
     * @param mixed $value
     * @param mixed $expectedResult
     */
    public function testCanGetAction($value, $expectedResult)
    {
        $trait = $this->getMockInstance('action', $value);

        $this->assertEquals($expectedResult, $trait->getAction());
    }

    /** @see IsEventBridgeEventTraitTest tests above */
    public function providerGetProperties() : array
    {
        return [
          'String' => ['property-value', 'property-value'],
          'Non-string' => [null, ''],
        ];
    }

    /**
     * Gets a mock instance implementing the trait.
     *
     * @param string $property the property to set a value for
     * @param mixed $value the value to set
     * @return object|IsEventBridgeEventTraitTest
     */
    private function getMockInstance(string $property, $value)
    {
        return new class($property, $value)
        {
            use IsEventBridgeEventTrait;

            public function __construct(string $property, $value)
            {
                $this->$property = $value;
            }
        };
    }
}
