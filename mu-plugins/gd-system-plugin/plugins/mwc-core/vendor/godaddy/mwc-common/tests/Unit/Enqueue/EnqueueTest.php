<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Enqueue;

use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;
use GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript;
use GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueStyle;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use ReflectionClass;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue
 */
final class EnqueueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that should ready a script for enqueue.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::script()
     */
    public function testShouldEnqueueScript()
    {
        $this->assertInstanceOf(EnqueueScript::class, Enqueue::script());

        $this->assertEquals('script', Enqueue::script()->getType());
    }

    /**
     * Tests that should ready a script for enqueue.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::style()
     */
    public function testShouldEnqueueStyle()
    {
        $this->assertInstanceOf(EnqueueStyle::class, Enqueue::style());

        $this->assertEquals('style', Enqueue::style()->getType());
    }

    /**
     * Tests it can set the enqueue type.
     *
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setType()
     */
    public function canSetType()
    {
        $instance = new Enqueue();
        $handler = new ReflectionClass($instance);

        $method = $handler->getMethod('setType');
        $method->setAccessible(true);

        $property = $handler->getProperty('enqueueType');
        $property->setAccessible(true);

        $this->assertNull($property->getValue($instance));

        $method->invokeArgs($instance, 'testType');

        $this->assertEquals('testType', $property->getValue($instance));
    }

    /**
     * Tests it can get the enqueue type.
     *
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::getType()
     */
    public function testCanGetType()
    {
        $instance = new Enqueue();
        $handler = new ReflectionClass($instance);

        $typeProperty = $handler->getProperty('enqueueType');
        $typeProperty->setAccessible('true');
        $typeProperty->setValue($instance, 'testType');

        $method = $handler->getMethod('getType');

        $this->assertEquals('testType', $method->invoke($instance));
    }

    /**
     * Tests that it can set the asset handle.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setHandle()
     * @throws ReflectionException
     */
    public function testCanSetHandle()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'handle');

        $this->assertNull($property->getValue($enqueue));

        $enqueue->setHandle('testHandle');

        $this->assertEquals('testHandle', $property->getValue($enqueue));
    }

    /**
     * Tests that it can set the asset handle.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setSource()
     * @throws ReflectionException
     */
    public function testCanSetSource()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'source');

        $this->assertEquals('', $property->getValue($enqueue));

        $enqueue->setSource('https://example.com/asset.mock');

        $this->assertEquals('https://example.com/asset.mock', $property->getValue($enqueue));
    }

    /**
     * Tests that it can set the asset dependencies.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setDependencies()
     * @throws ReflectionException
     */
    public function testCanSetDependencies()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'dependencies');

        $this->assertEquals([], $property->getValue($enqueue));

        $dependencies = ['dep1', 'dep2', 'dep3'];

        $enqueue->setDependencies($dependencies);

        $this->assertEquals($dependencies, $property->getValue($enqueue));
    }

    /**
     * Tests that it can set the asset handle.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setVersion()
     * @throws ReflectionException
     */
    public function testCanSetVersion()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'version');

        $this->assertEquals('', $property->getValue($enqueue));

        $enqueue->setVersion('1.2.3');

        $this->assertEquals('1.2.3', $property->getValue($enqueue));
    }

    /**
     * Tests that it can set the asset loading to be deferred.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setDeferred()
     * @throws ReflectionException
     */
    public function testCanSetDeferred()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'deferred');

        $this->assertFalse($property->getValue($enqueue));

        $enqueue->setDeferred(true);

        $this->assertTrue($property->getValue($enqueue));
    }

    /**
     * Tests that it can set a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::setCondition()
     * @throws ReflectionException
     */
    public function testCanSetCondition()
    {
        $enqueue = new Enqueue();
        $property = TestHelpers::getInaccessibleProperty($enqueue, 'enqueueCondition');

        $this->assertNull($property->getValue($enqueue));

        $closure = static function () {
            return true;
        };

        $enqueue->setCondition($closure);

        $this->assertEquals($closure, $property->getValue($enqueue));
    }

    /**
     * Tests that it can remove a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::removeCondition()
     * @throws ReflectionException
     */
    public function testCanRemoveCondition()
    {
        $enqueue = new Enqueue();
        $hasCondition = TestHelpers::getInaccessibleMethod($enqueue, 'hasCondition');

        $enqueue->setCondition(function () {
            return true;
        });

        $this->assertTrue($hasCondition->invoke($enqueue));

        $enqueue->removeCondition();

        $this->assertFalse($hasCondition->invoke($enqueue));
    }

    /**
     * Tests that it can determine whether it has a condition.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::hasCondition()
     * @throws ReflectionException
     */
    public function testCanDetermineHasCondition()
    {
        $enqueue = new Enqueue();
        $method = TestHelpers::getInaccessibleMethod($enqueue, 'hasCondition');

        $this->assertFalse($method->invoke($enqueue));

        $enqueue->setCondition(function () {
            return true;
        });

        $this->assertTrue($method->invoke($enqueue));
    }

    /**
     * Tests that it can determine whether if it can be enqueued.
     *
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue::shouldEnqueue()
     */
    public function testCanDetermineShouldEnqueue()
    {
        $instance = new Enqueue();
        $handler = new ReflectionClass($instance);

        $shouldApply = $handler->getMethod('shouldEnqueue');
        $shouldApply->setAccessible(true);

        $this->assertTrue($shouldApply->invoke($instance));

        $setCondition = $handler->getMethod('setCondition');
        $setCondition->invoke($instance, function () {
            return false;
        });

        $this->assertFalse($shouldApply->invoke($instance));
    }
}
