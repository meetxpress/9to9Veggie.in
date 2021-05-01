<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Register;

use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterAction;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use ReflectionClass;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Register\Register
 */
final class RegisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that should register an action.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::action()
     */
    public function testShouldRegisterAction()
    {
        $this->assertInstanceOf(RegisterAction::class, Register::action());

        $action = Register::action();

        $this->assertEquals('action', $action->getType());
        $this->assertSame(10, TestHelpers::getInaccessibleProperty(Register::class, 'processPriority')->getValue($action));
        $this->assertSame(1, TestHelpers::getInaccessibleProperty(Register::class, 'numberOfArguments')->getValue($action));
    }

    /**
     * Tests that should register a filter.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::filter()
     */
    public function testShouldRegisterFilter()
    {
        $this->assertInstanceOf(RegisterFilter::class, Register::filter());

        $filter = Register::filter();

        $this->assertEquals('filter', $filter->getType());
        $this->assertSame(10, TestHelpers::getInaccessibleProperty(Register::class, 'processPriority')->getValue($filter));
        $this->assertSame(1, TestHelpers::getInaccessibleProperty(Register::class, 'numberOfArguments')->getValue($filter));
    }

    /**
     * Tests it can set the registrable type.
     *
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::setType()
     */
    public function canSetType()
    {
        $instance = new Register();
        $handler = new ReflectionClass($instance);

        $method = $handler->getMethod('setType');
        $method->setAccessible(true);

        $property = $handler->getProperty('registrableType');
        $property->setAccessible(true);

        $this->assertNull($property->getValue($instance));

        $method->invokeArgs($instance, 'testType');

        $this->assertEquals('testType', $property->getValue($instance));
    }

    /**
     * Tests it can get the registrable type.
     *
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::getType()
     */
    public function testCanGetType()
    {
        $instance = new Register();
        $handler = new ReflectionClass($instance);

        $typeProperty = $handler->getProperty('registrableType');
        $typeProperty->setAccessible('true');
        $typeProperty->setValue($instance, 'testType');

        $method = $handler->getMethod('getType');

        $this->assertEquals('testType', $method->invoke($instance));
    }

    /**
     * Tests that it can set the group name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::setGroup()
     * @throws ReflectionException
     */
    public function testCanSetGroupName()
    {
        $register = new Register();
        $property = TestHelpers::getInaccessibleProperty($register, 'groupName');

        $this->assertNull($property->getValue($register));

        $register->setGroup('testGroup');

        $this->assertEquals('testGroup', $property->getValue($register));
    }

    /**
     * Tests that it can set the attached handler.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::setHandler()
     * @throws ReflectionException
     */
    public function testCanSetHandler()
    {
        $register = new Register();
        $property = TestHelpers::getInaccessibleProperty($register, 'handler');

        $this->assertNull($property->getValue($register));

        $register->setHandler('testHandler');

        $this->assertEquals('testHandler', $property->getValue($register));
    }

    /**
     * Tests that it can set the expected number of arguments.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::setArgumentsCount()
     * @throws ReflectionException
     */
    public function testCanSetNumberOfArguments()
    {
        $register = new Register();
        $property = TestHelpers::getInaccessibleProperty($register, 'numberOfArguments');

        $this->assertNull($property->getValue($register));

        $register->setArgumentsCount(3);

        $this->assertEquals(3, $property->getValue($register));
    }

    /**
     * Tests that it can determine whether it has a valid handler.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::hasHandler()
     * @throws ReflectionException
     */
    public function testCanDetermineHasHandler()
    {
        $register = new Register();
        $method = TestHelpers::getInaccessibleMethod($register, 'hasHandler');

        // no handler
        $this->assertFalse($method->invoke($register));

        // invalid handler
        $register->setHandler('invalidHandler');
        $this->assertFalse($method->invoke($register));

        // valid handler as a closure
        $register->setHandler(function () {
            return true;
        });
        $this->assertTrue($method->invoke($register));
    }

    /**
     * Tests that it can set a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::setCondition()
     */
    public function testCanSetCondition()
    {
        $register = new Register();
        $property = TestHelpers::getInaccessibleProperty($register, 'registrableCondition');

        $this->assertNull($property->getValue($register));

        $closure = static function () {
            return true;
        };

        $register->setCondition($closure);

        $this->assertEquals($closure, $property->getValue($register));
    }

    /**
     * Tests that it can remove a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::removeCondition()
     * @throws ReflectionException
     */
    public function testCanRemoveCondition()
    {
        $register = new Register();
        $hasCondition = TestHelpers::getInaccessibleMethod($register, 'hasCondition');

        $register->setCondition(function () {
            return true;
        });
        $this->assertTrue($hasCondition->invoke($register));

        $register->removeCondition();
        $this->assertFalse($hasCondition->invoke($register));
    }

    /**
     * Tests that it can determine whether it has a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::hasCondition()
     * @throws ReflectionException
     */
    public function testCanDetermineHasCondition()
    {
        $register = new Register();
        $method = TestHelpers::getInaccessibleMethod($register, 'hasCondition');

        $this->assertFalse($method->invoke($register));

        $register->setCondition(function () {
            return true;
        });

        $this->assertTrue($method->invoke($register));
    }

    /**
     * Tests that it can determine whether if it can be registered.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Register::shouldRegister()
     * @throws ReflectionException
     */
    public function testCanDetermineShouldRegister()
    {
        $instance = new Register();
        $handler = new ReflectionClass($instance);

        $shouldApply = $handler->getMethod('shouldRegister');
        $shouldApply->setAccessible(true);

        $this->assertTrue($shouldApply->invoke($instance));

        $setCondition = $handler->getMethod('setCondition');
        $setCondition->invoke($instance, function () {
            return false;
        });

        $this->assertFalse($shouldApply->invoke($instance));
    }
}
