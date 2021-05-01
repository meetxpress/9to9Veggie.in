<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Helpers;

use GoDaddy\WordPress\MWC\Common\Helpers\ObjectHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ObjectHelper
 */
final class ObjectHelperTest extends TestCase
{
    /**
     * Test that ObjectHelper converts basic object into an array.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ObjectHelper::toArray()
     */
    public function testCanConvertObjectToArray()
    {
        $array = ['first' => 'value', 'second' => ['tier' => 'value']];

        $this->assertEquals($array, ObjectHelper::toArray((object)$array));
    }
}
