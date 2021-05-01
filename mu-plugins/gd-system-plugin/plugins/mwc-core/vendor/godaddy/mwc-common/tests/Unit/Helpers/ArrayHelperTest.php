<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Helpers;

use ArrayAccess;
use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use stdClass;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper
 */
final class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that can combine arrays.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::combine()
     * @throws Exception
     */
    public function testCanCombineArrays()
    {
        $simple = ['test' => 1];
        $complex = ['test' => 2, 'second' => ['nested' => 3]];

        $this->assertEquals($simple, ArrayHelper::combine(['test' => 20], $simple));
        $this->assertEquals($complex, ArrayHelper::combine($simple, $complex));

        $this->expectException(Exception::class);
        $this->assertEquals($simple, ArrayHelper::combine(null, $simple));

        $this->expectException(Exception::class);
        $this->assertEquals($simple, ArrayHelper::combine($simple, null));
    }

    /**
     * Test that can combine multiple arrays in a single call.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::combine()
     * @throws Exception
     */
    public function testCanCombineMultipleArrays()
    {
        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'zip' => 3,
        ], ArrayHelper::combine(
            ['foo' => 1],
            ['bar' => 2],
            ['zip' => 3]
        ));
    }

    /**
     * Test that can combine arrays recursively.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::combineRecursive()
     * @throws Exception
     */
    public function testCanCombineArraysRecursively()
    {
        $this->assertEquals([
            'foo' => [
                'bar' => 2,
                'zip' => 3,
            ],
        ], ArrayHelper::combineRecursive(
            ['foo' => ['bar' => 2]],
            ['foo' => ['zip' => 2]],
            ['foo' => ['zip' => 3]]
        ));
    }

    /**
     * Tests that {@see ArrayHelper::combine()} throws an exception if any of the parameters is null.
     *
     * @param array $arrays variable list of arrays to merge
     * @dataProvider provideCombineThrowsExceptionForNullParametersData
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::combine()
     */
    public function testCombineThrowsExceptionForNullParameters(...$arrays)
    {
        $this->expectException(Exception::class);

        ArrayHelper::combine(...$arrays);
    }

    /** @see testCombineThrowsExceptionForNullParameters */
    public function provideCombineThrowsExceptionForNullParametersData() : array
    {
        return [
            [null, [], [], []],
            [[], null, [], []],
            [[], [], null, []],
            [[], [], [], null],
            [null, null, null, null],
        ];
    }

    /**
     * Tests that can convert an array into a query string.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::query()
     */
    public function testCanConvertArrayToQueryString()
    {
        $expected = 'first=hello&second=my&third%5Bworking%5D=function';
        $params = ['first' => 'hello', 'second' => 'my', 'third' => ['working' => 'function']];

        $this->assertEquals($expected, ArrayHelper::query($params));
    }

    /**
     * Test that can identify if array is accessible.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::accessible()
     */
    public function testCanDetermineArrayAccessible()
    {
        $accessible = new Foo();
        $notAccessible = new stdClass();

        $this->assertTrue(ArrayHelper::accessible(['key' => 'value']));
        $this->assertTrue(ArrayHelper::accessible($accessible));
        $this->assertFalse(ArrayHelper::accessible($notAccessible));
    }

    /**
     * Tests that the helper can find nested values in array.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::contains()
     */
    public function testCanDetermineArrayContainsValue()
    {
        $array = [
            'first'  => 'first-level',
            'another' => [
                'second' => 'second-level',
                'another' => [
                    'third' => 'third-level',
                ],
            ],
        ];

        $this->assertTrue(ArrayHelper::contains($array, 'third-level'));
        $this->assertFalse(ArrayHelper::contains($array, 'another'));
    }

    /**
     * Tests that can determine an array has given key or keys by string or dot notation.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::has()
     */
    public function testCanDetermineArrayHasGivenKeys()
    {
        $this->assertTrue(ArrayHelper::has(['key' => 'value'], 'key'));
        $this->assertFalse(ArrayHelper::has([], 'key'));

        $this->assertTrue(ArrayHelper::has(['key' => 'value', 'test' => 'value'], ['key', 'test']));
        $this->assertFalse(ArrayHelper::has(['key' => 'value'], ['key', 'test']));

        $this->assertTrue(ArrayHelper::has(['key' => ['nested' => ['deeply' => true]]], ['key.nested.deeply', 'key.nested']));
        $this->assertTrue(ArrayHelper::has(['key' => ['nested' => ['deeply' => null]]], ['key.nested.deeply', 'key.nested']));
        $this->assertFalse(ArrayHelper::has(['key' => ['nested' => ['deeply' => true]]], ['key.nested', 'key.nested.shallow']));

        $this->assertTrue(ArrayHelper::has(['key' => []], 'key'));
        $this->assertTrue(ArrayHelper::has(['key' => false], 'key'));
        $this->assertTrue(ArrayHelper::has(['key' => ''], 'key'));
        $this->assertTrue(ArrayHelper::has(['key' => 0], 'key'));
        $this->assertTrue(ArrayHelper::has(['key' => null], 'key'));
    }

    /**
     * Tests that can determine is array key exists without PHP error/warning.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::exists()
     */
    public function testCanDetermineArrayKeyExists()
    {
        $this->assertTrue(ArrayHelper::exists(['key' => 'value'], 'key'));
        $this->assertFalse(ArrayHelper::exists([], 'key'));
    }

    /**
     * Tests that can filter array properly by where statement.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::where()
     */
    public function testCanFilterArrayByWhereStatement()
    {
        $this->assertEquals([2 => 3], ArrayHelper::where([15, 12, 3, 9], function ($value) {
            return $value < 5;
        }));
    }

    /**
     * Tests helper can flatten a multi-dimensional array.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::flatten()
     */
    public function testCanFlattenArray()
    {
        $array = [
            'first'  => 'first-level',
            'another' => [
                'second' => 'second-level',
                'another' => [
                    'third' => 'third-level',
                ],
            ],
        ];

        $this->assertEquals(['first-level', 'second-level', 'third-level'], ArrayHelper::flatten($array));
    }

    /**
     * Tests that can retrieve array value by key without PHP error/warning.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::get()
     */
    public function testCanGetArrayValueByKey()
    {
        $this->assertTrue(ArrayHelper::get(['key' => true], 'key'));
        $this->assertTrue(ArrayHelper::get(['key' => ['nested' => ['deeply' => true]]], 'key.nested.deeply'));
    }

    /**
     * Tests that the helper can pluck values by a given key.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::pluck()
     */
    public function testCanPluckValuesByAGivenKey()
    {
        $array = [
            'single' => 'first',
            'double' => [
                'first' => [
                    'test' => 'idk',
                ],
                'second',
                'third',
            ],
            'another' => [
                'first'     => 'value',
                'second'    => 'hidden',
            ],
        ];

        $this->assertEquals([['test' => 'idk'], 'value'], ArrayHelper::pluck($array, 'first'));
        $this->assertEquals(['hidden'], ArrayHelper::pluck($array, 'second'));
        $this->assertEmpty(ArrayHelper::pluck($array, 'fail'));
    }

    /**
     * Tests that can remove items from an array.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::remove()
     * @throws Exception
     */
    public function testCanRemoveItemsFromArray()
    {
        $simple = ['test' => 1];
        $complex = ['test' => 2, 'second' => ['nested' => 3]];
        $multi = ['test' => 2, 'second' => ['nested' => 3], 'third' => 4];

        ArrayHelper::remove($simple, 'test');
        $this->assertEquals([], $simple);

        ArrayHelper::remove($complex, 'second');
        $this->assertEquals(['test' => 2], $complex);

        ArrayHelper::remove($multi, ['second', 'third']);
        $this->assertEquals(['test' => 2], $multi);
    }

    /**
     * Tests that the helper will return default with key not found.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::get()
     */
    public function testCanReturnDefaultWhenGetArrayValueByKeyNotFound()
    {
        $this->assertNull(ArrayHelper::get([], 'key'), 'ArrayHelper::get() does not return null by default as expected');
        $this->assertEquals('myDefault', ArrayHelper::get([], 'key', 'myDefault'));
        $this->assertNotEquals('myDefault', ArrayHelper::get(['key' => 'value'], 'key', 'myDefault'));
    }

    /**
     * Test that can return array exception given key(s).
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::except()
     * @throws Exception
     */
    public function testCanReturnArrayExceptGivenKeys()
    {
        $simple = ['test' => 1];
        $complex = ['test' => 2, 'second' => ['nested' => 3]];
        $multi = ['test' => 2, 'second' => ['nested' => 3], 'third' => 4];

        $this->assertEquals([], ArrayHelper::except($simple, 'test'));

        $this->assertEquals(['test' => 2], ArrayHelper::except($complex, 'second'));

        $this->assertEquals(['test' => 2], ArrayHelper::except($multi, ['second', 'third']));
    }

    /**
     * Test that can set array values in single and multi-dimensional arrays.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::get()
     */
    public function testCanSetArrayValues()
    {
        $array = ['first' => 'value', 'hello' => ['nested' => 'value']];

        ArrayHelper::set($array, 'first', 'new');
        $this->assertEquals('new', ArrayHelper::get($array, 'first'));

        ArrayHelper::set($array, 'hello.nested', 'new');
        $this->assertEquals('new', ArrayHelper::get($array, 'hello.nested'));

        ArrayHelper::set($array, 'hello.nothing', 'new');
        $this->assertEquals('new', ArrayHelper::get($array, 'hello.nothing'));
    }

    /**
     * Test that can wrap non arrays.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper::wrap()
     */
    public function testCanWrapNonArrayValues()
    {
        $this->assertIsArray(ArrayHelper::wrap(['key' => 'value']));
        $this->assertIsArray(ArrayHelper::wrap('key'));
        $this->assertIsArray(ArrayHelper::wrap((new stdClass())));
        $this->assertIsArray(ArrayHelper::wrap(null));
        $this->assertNotEquals([null], ArrayHelper::wrap(null));
    }
}

/**
 * Dummy class to test Array Accessible Object.
 */
class Foo implements ArrayAccess
{
    protected $_data = [];

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    public function offsetGet($offset)
    {
        return $this->_data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }
}
