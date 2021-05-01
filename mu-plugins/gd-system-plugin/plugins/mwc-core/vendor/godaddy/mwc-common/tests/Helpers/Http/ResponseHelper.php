<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use PHPUnit\Framework\Assert;
use function Patchwork\redefine;

/**
 * Test helper class for faking responses
 */
class ResponseHelper
{
    use HasHttpAssertionsTrait;

    /** @var Response Must override so static instance is specific to this class */
    protected static $httpItemInstance;

    /** @var int Must override so that static count is kept specifically on this class */
    protected static $httpItemCalledCount = 0;

    /**
     * Register listeners that will intercept responses and return resulting request.
     *
     * @param callable|null $callback
     *
     * @return void
     * @throws Exception
     */
    public static function fake(callable $callback = null)
    {
        static::$httpItemInstance = new Response();
        static::$httpItemCalledCount = 0;

        $instance = &static::$httpItemInstance;
        $count    = &static::$httpItemCalledCount;

        redefine(Response::class.'::send', function() use ($callback, &$instance, &$count) {
            // @NOTE: Strict comparison will only compare the class name where we want the full object values compared
            if ($instance != $this) {
                $instance = $this;
                $count    = 0;
            }

            $count++;

            if (is_null($callback)) {
                return json_encode($this->getBody());
            }

            return $callback($this);
        });
    }

    /**
     * Custom assertion to check the response status is not a particular code
     *
     * @param int $code
     * @param string|null $message
     * @return void
     */
    public static function assertNotStatusCode(int $code, string $message = '')
    {
        Assert::assertNotEquals($code, static::$httpItemInstance->getStatus(), $message);
    }

    /**
     * Custom assertion to check the response status is a particular code
     *
     * @param int $code
     * @param string|null $message
     * @return void
     */
    public static function assertStatusCode(int $code, string $message = '')
    {
        Assert::assertEquals($code, static::$httpItemInstance->getStatus(), $message);
    }
}
