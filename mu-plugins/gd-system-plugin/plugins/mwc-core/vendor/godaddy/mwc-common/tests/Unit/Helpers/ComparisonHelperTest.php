<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Helpers;

use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper
 */
class ComparisonHelperTest extends TestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::create()
     */
    public function testCanCreateCompareInstance()
    {
        $this->assertEquals(ComparisonHelper::class, get_class(ComparisonHelper::create()));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::setOperator()
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::setValue()
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::setWith()
     */
    public function testSettersAreChaining()
    {
        $instance = ComparisonHelper::create();

        $this->assertEquals(
            [$instance, $instance, $instance],
            [$instance->setOperator(''), $instance->setWith(''), $instance->setValue('')]
        );
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::compare()
     *
     * @param bool $caseSensitive
     * @param $value
     * @param string $operator
     * @param $with
     * @param bool $expected
     *
     * @dataProvider dataProviderTestCompare
     */
    public function testCanCompare(bool $caseSensitive, $value, string $operator, $with, bool $expected)
    {
        $helper = ComparisonHelper::create();

        // sanity check before asserting the expected result
        $this->assertFalse($helper->compare());

        $this->assertEquals(
            $expected,
            $helper
                ->setCaseSensitive($caseSensitive)
                ->setValue($value)
                ->setOperator($operator)
                ->setWith($with)
                ->compare()
        );
    }

    /** @see testCanCompare */
    public function dataProviderTestCompare() : array
    {
        return [
            // equals
            'Equals with different types'                                     => [true, 1, ComparisonHelper::EQUALS, '1', false],
            'Equals with numbers'                                             => [true, 1, ComparisonHelper::EQUALS, 1, true],
            'Equals with strings'                                             => [true, 'a', ComparisonHelper::EQUALS, 'a', true],
            'Equals with strings and not case sensitive'                      => [false, 'A', ComparisonHelper::EQUALS, 'a', true],
            'Equals with a null "with"'                                       => [true, 'a', ComparisonHelper::EQUALS, null, false],
            'Equals with a null value'                                        => [true, null, ComparisonHelper::EQUALS, 'a', false],
            'Equals with "equals" operator'                                   => [true, 'a', 'equals', 'a', true],
            'Equals with "equal" operator'                                    => [true, 'a', 'equal', 'a', true],
            'Equals with "=" operator'                                        => [true, 'a', '=', 'a', true],

            // in
            'Check if value is in an empty array'                             => [true, 'a', ComparisonHelper::IN, [], false],
            'Check if value is in a null "with"'                              => [true, 'a', ComparisonHelper::IN, null, false],
            'Check if a null value is in an array'                            => [true, null, ComparisonHelper::IN, ['a', 'b', 'c'], false],
            'Check if value is in an array when it\'s not'                    => [true, 'a', ComparisonHelper::IN, ['b', 'c', 'd'], false],
            'Check if value is in an array when it is'                        => [true, 'a', ComparisonHelper::IN, ['a', 'b', 'c'], true],
            'Check if value is in an array when it is and not case sensitive' => [false, 'a', ComparisonHelper::IN, ['A', 'B', 'C'], true],

            // sanity tests
            'Compare with an invalid operator'                                => [true, 'a', 'operator', 'b', false],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper::normalize()
     *
     * @param bool $caseSensitive
     * @param mixed $value
     * @param mixed $expected
     *
     * @throws ReflectionException
     *
     * @dataProvider dataProviderCanNormalizeValue
     */
    public function testCanNormalizeValue(bool $caseSensitive, $value, $expected)
    {
        $helper = ComparisonHelper::create();
        $helper->setCaseSensitive($caseSensitive);

        $reflection = new ReflectionClass($helper);

        $normalizeMethod = $reflection->getMethod('normalize');
        $normalizeMethod->setAccessible(true);

        $this->assertEquals($expected, $normalizeMethod->invoke($helper, $value));
    }

    /** @see testCanNormalizeValue */
    public function dataProviderCanNormalizeValue() : array
    {
        return [
            'Not case sensitive normalize called with a string'         => [false, 'AbcD', 'abcd'],
            'Not case sensitive normalize called with a null value'     => [false, null, null],
            'Not case sensitive normalize called with an integer'       => [false, 1, 1],
            'Not case sensitive normalize called with an object'        => [false, new ComparisonHelper(), new ComparisonHelper()],
            'Not case sensitive normalize called with an array'         => [false, ['A', 'b', 'C', 'd'], ['a', 'b', 'c', 'd']],
            'Not case sensitive normalize called with an integer array' => [false, [1, 2, 3, 4], [1, 2, 3, 4]],
            'Case sensitive normalize call'                             => [true, 'AbcD', 'AbcD'],
        ];
    }
}
