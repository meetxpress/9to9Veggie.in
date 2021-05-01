<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait
 */
final class IsSingletonTraitTest extends \PHPUnit\Framework\TestCase
{
    /** @var TestSingleton instance for testing */
    protected $singleton;

    /**
     * Runs a script for every test in this set.
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->singleton = new TestSingleton();
        $reflection = new \ReflectionClass($this->singleton);

        $reflectionProperty = $reflection->getProperty('instance');

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->singleton, $this->singleton);
    }

    /**
     * Tests that it can determine whether an instance is loaded or not.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait::isLoaded()
     */
    public function testCanCheckIfIsLoaded()
    {
        $this->singleton;

        self::assertTrue($this->singleton::isLoaded());

        $this->singleton::reset();

        self::assertFalse($this->singleton::isLoaded());
    }

    /**
     * Tests that it can initialize and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait::getInstance()
     */
    public function testCanGetInstance()
    {
        self::assertEquals(TestSingleton::class, get_class($this->singleton::getInstance()));
    }

    /**
     * Tests that an instance can be reset.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait::reset()
     */
    public function testCanBeReset()
    {
        $this->singleton::reset();

        $singleton = new \ReflectionClass($this->singleton);
        $instance = $singleton->getProperty('instance');
        $instance->setAccessible(true);

        self::assertNull($instance->getValue());
    }
}

/** dummy class for testing {@see IsSingletonTrait} */
class TestSingleton
{
    use IsSingletonTrait;
}
