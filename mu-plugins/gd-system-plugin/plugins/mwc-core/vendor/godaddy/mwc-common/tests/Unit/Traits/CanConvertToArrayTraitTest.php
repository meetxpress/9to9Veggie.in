<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

final class CanConvertToArrayTraitTest extends TestCase
{
    /**
     * Tests that it can set properties.
     *
     * @dataProvider provideTypeDeclarationsForToArrayTestData
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait::toArray()
     */
    public function testCanConvertPropertiesToArray($private, $protected, $public, $expected)
    {
        $subject = $this->getTestSubject();
        $privateProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludePrivate');
        $protectedProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludeProtected');
        $publicProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludePublic');

        $privateProperty->setValue($subject, $private);
        $protectedProperty->setValue($subject, $protected);
        $publicProperty->setValue($subject, $public);

        $this->assertEquals($expected, $subject->toArray());
    }

    /**
     * Tests that can determine if a property should be accessible.
     *
     * @dataProvider providePropertyAccessibilityData
     * @covers       \GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait::toArrayShouldPropertyBeAccessible()
     * @throws       ReflectionException
     */
    public function testCanDeterminePropertyAccessibility($private, $protected, $public, $expected)
    {
        $results = [];
        $subject = $this->getTestSubject();
        $method = TestHelpers::getInaccessibleMethod($subject, 'toArrayShouldPropertyBeAccessible');

        $privateProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludePrivate');
        $protectedProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludeProtected');
        $publicProperty = TestHelpers::getInaccessibleProperty($subject, 'toArrayIncludePublic');

        $privateProperty->setValue($subject, $private);
        $protectedProperty->setValue($subject, $protected);
        $publicProperty->setValue($subject, $public);

        foreach (array_keys($expected) as $propertyName) {
            $results[$propertyName] = $method->invokeArgs(
                $subject,
                [TestHelpers::getInaccessibleProperty($subject, $propertyName)]
            );
        }

        $this->assertEquals($expected, $results);
    }

    /**
     * @see testCanConvertPropertiesToArray
     */
    public function provideTypeDeclarationsForToArrayTestData() : array
    {
        return [
            [false, false, false, []],
            [true, false, false, ['privateProperty' => 'private']],
            [true, true, false, ['privateProperty' => 'private', 'protectedProperty' => 'protected']],
            [true, true, true, ['privateProperty' => 'private', 'protectedProperty' => 'protected', 'publicProperty' => 'public']],
            [true, false, true, ['privateProperty' => 'private', 'publicProperty' => 'public']],
            [false, true, true, ['protectedProperty' => 'protected', 'publicProperty' => 'public']],
        ];
    }

    /**
     * @see testCanConvertPropertiesToArray
     */
    public function providePropertyAccessibilityData() : array
    {
        return [
            [false, false, false, ['privateProperty' => false, 'protectedProperty' => false, 'publicProperty' => false]],
            [true, false, false, ['privateProperty' => true, 'protectedProperty' => false, 'publicProperty' => false]],
            [true, true, false, ['privateProperty' => true, 'protectedProperty' => true, 'publicProperty' => false]],
            [true, true, true, ['privateProperty' => true, 'protectedProperty' => true, 'publicProperty' => true]],
            [true, false, true, ['privateProperty' => true, 'protectedProperty' => false, 'publicProperty' => true]],
            [false, true, true, ['privateProperty' => false, 'protectedProperty' => true, 'publicProperty' => true]],
        ];
    }

    /**
     * Anonymous Class for Testing Trait.
     *
     * @see testCanConvertPropertiesToArray
     */
    private function getTestSubject()
    {
        return new class {
            use CanConvertToArrayTrait;

            private $privateProperty = 'private';

            protected $protectedProperty = 'protected';

            public $publicProperty = 'public';
        };
    }
}
