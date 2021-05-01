<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Enqueue\Types;

use Exception;
use GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionClass;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript
 */
final class EnqueueScriptTest extends WPTestCase
{
    /**
     * Tests that it can set an inline script object for the script to be enqueued.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::attachInlineScriptObject()
     * @throws ReflectionException
     */
    public function testCanSetInlineScriptObject()
    {
        $script = new EnqueueScript();
        $property = TestHelpers::getInaccessibleProperty($script, 'scriptObject');

        $this->assertNull($property->getValue($script));

        $script->attachInlineScriptObject('testObject');

        $this->assertEquals('testObject', $property->getValue($script));
    }

    /**
     * Tests that it can set variables for the inline script object attached to the script being enqueued.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::attachInlineScriptVariables()
     * @throws ReflectionException
     */
    public function testCanSetInlineScriptVariables()
    {
        $script = new EnqueueScript();
        $property = TestHelpers::getInaccessibleProperty($script, 'scriptVariables');

        $this->assertEquals([], $property->getValue($script));

        $variables = ['foo' => 'bar'];

        $script->attachInlineScriptVariables($variables);

        $this->assertEquals($variables, $property->getValue($script));
    }

    /**
     * Tests that it will not be able to execute if no handle is set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCantExecuteWithoutHandle()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_script', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);
        (new EnqueueScript())->execute();
    }

    /**
     * Tests that it will not be able to execute if a handle is set but is invalid.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCantExecuteWithInvalidHandle()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_script', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);
        (new EnqueueScript())->setHandle('')->execute();
    }

    /**
     * Tests that it will not be able to execute if a handle is set but no source is specified.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCantExecuteWithoutSource()
    {
        $this->expectException(Exception::class);
        WP_Mock::userFunction('wp_register_script', ['times' => 0]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);
        (new EnqueueScript())->setHandle('test')->execute();
    }

    /**
     * Tests that it will not execute if there is a condition that fails.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCantExecuteWhenConditionFail()
    {
        WP_Mock::userFunction('wp_register_script', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);
        (new EnqueueScript())->setHandle('test')->setSource('https://example.com/fake.js')->setCondition(function () {
            return false;
        })->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it will execute if there is a condition that passes.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCanExecuteWhenConditionPass()
    {
        WP_Mock::userFunction('wp_register_script', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        (new EnqueueScript())->setHandle('test')->setSource('https://example.com/fake.js')->setCondition(function () {
            return true;
        })->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it can execute a script enqueue when no conditions are set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCanExecute()
    {
        WP_Mock::userFunction('wp_register_script', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('wp_localize_script', ['times' => 0]);
        (new EnqueueScript())->setHandle('test')->setSource('https://example.com/fake.js')->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it can execute a script enqueue and place an inline object when requested.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::execute()
     * @throws Exception
     */
    public function testCanExecuteWithInlineObject()
    {
        WP_Mock::userFunction('wp_register_script', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('wp_localize_script', ['times' => 1]);
        (new EnqueueScript())->setHandle('test')->setSource('https://example.com/fake.js')->attachInlineScriptObject('test')->execute();

        $this->assertTrue(true);
    }

    /**
     * Tests that it will not be able to execute if no handle is set.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::validate()
     * @throws Exception
     */
    public function testCantValidateWithoutHandle()
    {
        $this->expectException(Exception::class);
        (new EnqueueScript())->validate();
    }

    /**
     * Tests that it will not be able to validate if a handle is set but is invalid.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::validate()
     * @throws Exception
     */
    public function testCantValidateWithInvalidHandle()
    {
        $this->expectException(Exception::class);
        (new EnqueueScript())->setHandle('')->validate();
    }

    /**
     * Tests that it will not be able to validate if a handle is set but no source is specified.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Enqueue\Types\EnqueueScript::validate()
     * @throws Exception
     */
    public function testCantValidateWithoutSource()
    {
        $this->expectException(Exception::class);
        (new EnqueueScript())->setHandle('test')->validate();
    }
}
