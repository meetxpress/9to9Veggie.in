<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http;

use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use PHPUnit\Framework\Assert;

/**
 * Test helper class for faking responses
 */
trait HasHttpAssertionsTrait
{
    /** @var Request|Response */
    protected static $httpItemInstance;

    /** @var int number of times the item instance was called */
    protected static $httpItemCalledCount = 0;

    /**
     * Asserts the number of times a specific http item instance was called.
     *
     * @param int $times
     * @return void
     */
    public static function assertSentTimes(int $times)
    {
        Assert::assertEquals($times, static::$httpItemCalledCount);
    }

    /**
     * Asserts the http item was sent.
     *
     * @return void
     */
    public static function assertSent()
    {
        Assert::assertGreaterThan(0, static::$httpItemCalledCount);
    }

    /**
     * Asserts the http item was not sent.
     *
     * @return void
     */
    public static function assertNotSent()
    {
        static::assertSentTimes(0);
    }
}
