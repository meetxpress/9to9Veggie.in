<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Helpers;

use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper
 */
final class StringHelperTest extends WPTestCase
{
    /**
     * Tests that can add a string to the end fo a string.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::endWith()
     */
    public function testCanAddACapToAGivenString()
    {
        $string = 'this/is/a/test';

        $this->assertEquals($string, StringHelper::endWith($string, 'test'));
        $this->assertEquals("{$string}-hello", StringHelper::endWith($string, '-hello'));
        $this->assertEquals("{$string} space", StringHelper::endWith($string, ' space'));
        $this->assertEquals("{$string}/", StringHelper::endWith("{$string}    ", '/'));
    }

    /**
     * Tests that can add a trailing slash to a string.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::trailingSlash()
     */
    public function testCanAddATrailingSlash()
    {
        $string = 'this/is/a/test';

        $this->assertEquals("{$string}/", StringHelper::trailingSlash($string));
        $this->assertEquals("{$string}/", StringHelper::trailingSlash($string.'/'));
        $this->assertEquals("{$string}/", StringHelper::trailingSlash($string.'/   '));
    }

    /**
     * Tests that can return a string after a given delimiter.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::after()
     */
    public function testCanGetStringAfterDelimiter()
    {
        $delimiter = '@';
        $string = 'hello@person';
        $repeater = 'hello@person@fail';
        $missing = 'hello';

        $this->assertEquals('person', StringHelper::after($string, $delimiter));
        $this->assertEquals('person@fail', StringHelper::after($repeater, $delimiter));
        $this->assertEquals($missing, StringHelper::after($missing, $delimiter));
    }

    /**
     * Tests that can determine if a string contains another string array of strings.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::contains()
     */
    public function testCanDetermineIfContains()
    {
        $string = 'my crazy string may contain';

        $this->assertFalse(StringHelper::contains($string, null));
        $this->assertFalse(StringHelper::contains($string, ''));
        $this->assertFalse(StringHelper::contains($string, 'false'));
        $this->assertTrue(StringHelper::contains($string, 'contain'));
        $this->assertTrue(StringHelper::contains($string, ['false', 'string']));
        $this->assertTrue(StringHelper::contains($string, ['may', 'string']));
    }

    /**
     * Tests that can return a string after the last occurrence of a given delimiter.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::afterLast()
     */
    public function testCanGetStringAfterLastOccurrenceDelimiter()
    {
        $this->assertEquals('test', StringHelper::afterLast('fail.hello.test', '.'));
        $this->assertEquals('', StringHelper::afterLast('ends.with.delimiter.', '.'));
        $this->assertEquals('no.delimiter.present', StringHelper::afterLast('no.delimiter.present', '-'));
    }

    /**
     * Tests that can return a string before a given delimiter.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::before()
     */
    public function testCanGetStringBeforeDelimiter()
    {
        $delimiter = '@';
        $string = 'hello@person';
        $repeater = 'hello@person@fail';
        $missing = 'hello';

        $this->assertEquals('hello', StringHelper::before($string, $delimiter));
        $this->assertEquals('hello', StringHelper::before($repeater, $delimiter));
        $this->assertEquals($missing, StringHelper::before($missing, $delimiter));
    }

    /**
     * Test that can return a string before the last occurrence of a given delimiter.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::beforeLast()
     */
    public function testCanGetStringBeforeLastOccurrenceDelimiter()
    {
        $this->assertEquals('fail.hello', StringHelper::beforeLast('fail.hello.test', '.'));
        $this->assertEquals('ends.with.delimiter', StringHelper::beforeLast('ends.with.delimiter.', '.'));
        $this->assertEquals('', StringHelper::beforeLast('.start.with.delimiter.', '.'));
        $this->assertEquals('no.delimiter.present', StringHelper::beforeLast('no.delimiter.present', '-'));
    }

    /**
     * Tests that can replace the first occurrence of a string with another.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::replaceFirst()
     * @dataProvider providerCanReplaceFirstOccurrenceOfString()
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param string $expected
     */
    public function testCanReplaceFirstOccurrenceOfString(string $subject, string $search, string $replace, string $expected)
    {
        $this->assertEquals($expected, StringHelper::replaceFirst($subject, $search, $replace));
    }

    /** @see testCanReplaceFirstOccurrenceOfString */
    public function providerCanReplaceFirstOccurrenceOfString() : array
    {
        return [
            'nothing to replace'  => ['My name will not be replaced', '', 'Bob', 'My name will not be replaced'],
            'regular replacement' => ['Hi, my name is {name}', '{name}', 'Bob', 'Hi, my name is Bob'],
            'nothing found'       => ['My shirt is {size}', '{color}', 'red', 'My shirt is {size}'],
        ];
    }

    /**
     * Tests that sanitize is calling WP sanitize_text_field function.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::sanitize()
     */
    public function testCanSanitizeString()
    {
        WP_Mock::userFunction('sanitize_text_field')
            ->with('original')
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', StringHelper::sanitize('original'));
    }

    /**
     * Tests that can transform a string to snake_case.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::snakeCase()
     */
    public function testCanTransformStringToSnakeCase()
    {
        $strings = [
            'hello_my_friend' => 'HelloMyFriend',
            'welcome_to_this' => 'Welcome!To#This',
            'hi_lo' => 'hi lo',
            'helper_method_upper' => 'helper_method upper',
        ];

        foreach ($strings as $expected => $string) {
            $this->assertEquals($expected, StringHelper::snakeCase($string));
        }
    }

    /**
     * Tests that can determine if a string starts with another string
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\Helpers\StringHelper::startsWith()
     *
     * @param string $string
     * @param string $search
     * @param bool $expected
     *
     * @dataProvider providerPluginNames
     */
    public function testCanDetermineIfStartsWith(string $string, string $search, bool $expected)
    {
        $this->assertSame($expected, StringHelper::startsWith($string, $search));
    }

    /** @see testCanDetermineIfStartsWith */
    public function providerPluginNames() : array
    {
        return [
            ['a silly string a b', 'a', true],
            ['a silly string a b', 'b', false],
            ['a silly string a b', 'string', false],
            ['a silly string a b', 'a ', true],
            ['a silly string a b', 'a s', true],
        ];
    }
}
