<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Exceptions\Handlers;

use ErrorException;
use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Loggers\Logger;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\BaseExceptionHandler
 */
final class BaseExceptionHandlerTest extends WPTestCase
{
    /** @var BaseExceptionHandler */
    private $handler;

    /** @var string|int stores the current error reporting value from PHP configuration */
    private $errorReporting;

    /**
     * Sets up the test variables.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->handler = new BaseExceptionHandler();
        $this->errorReporting = ini_get('error_reporting');
    }

    /**
     * Restores PHP configuration after completing tests.
     */
    public function tearDown(): void
    {
        ini_set('error_reporting', $this->errorReporting);

        parent::tearDown();
    }

    /**
     * Tests that it can get the context.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::getContext()
     *
     * @throws ReflectionException
     */
    public function testCanGetContext()
    {
        $method = new ReflectionMethod($this->handler, 'getContext');
        $method->setAccessible(true);

        $this->assertIsArray($method->invoke($this->handler, (new BaseException('test exception'))));
    }

    /**
     * Tests that it can convert an exception to array.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::convertExceptionToArray()
     *
     * @throws ReflectionException|Exception
     */
    public function testCanConvertExceptionToArray()
    {
        $method = new ReflectionMethod($this->handler, 'convertExceptionToArray');
        $method->setAccessible(true);

        Configuration::set('mwc.debug', false);
        Configuration::set('mwc.env', 'production');

        $result = $method->invokeArgs($this->handler, [new BaseException('')]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayNotHasKey('exception', $result);
        $this->assertArrayNotHasKey('file', $result);
        $this->assertArrayNotHasKey('line', $result);
        $this->assertArrayNotHasKey('trace', $result);

        Configuration::set('mwc.debug', true);
        Configuration::set('mwc.env', 'testing');

        $result = $method->invokeArgs($this->handler, [new BaseException('')]);

        $this->assertArrayHasKey('exception', $result);
        $this->assertArrayHasKey('file', $result);
        $this->assertArrayHasKey('line', $result);
        $this->assertArrayHasKey('trace', $result);
    }

    /**
     * Tests that it can get get an exception stack trace.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::getExceptionStackTrace()
     *
     * @throws ReflectionException
     */
    public function testCanGetExceptionStackTrace()
    {
        $method = new ReflectionMethod($this->handler, 'getExceptionStackTrace');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($this->handler, [new BaseException('')]));
    }

    /**
     * Tests that it can get get an exception stack trace.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::getExceptionMessage()
     *
     * @throws Exception
     */
    public function testCanGetExceptionMessage()
    {
        $this->assertStringContainsString('test', $this->handler->getExceptionMessage(new BaseException('test')));
    }

    /**
     * Tests that it can handle an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::handleError()
     *
     * @throws ErrorException
     */
    public function testCanHandleError()
    {
        ini_set('error_reporting', 0);

        $this->handler->handleError(1, 'test');

        $this->expectException(ErrorException::class);

        ini_set('error_reporting', E_ALL);

        $this->handler->handleError(1, 'test');
    }

    /**
     * Tests that it can handle an exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::handleException()
     * @dataProvider dataProviderCanHandleException
     *
     * @param bool $shouldIgnore
     * @throws Throwable
     */
    public function testCanHandleException(bool $shouldIgnore)
    {
        $mock = $this->getMockBuilder(BaseExceptionHandler::class)
                     ->enableOriginalConstructor()
                     ->onlyMethods(['report', 'shouldIgnore'])
                     ->getMock();
        $mock->expects($this->once())->method('shouldIgnore')->willReturn($shouldIgnore);

        $exception = $this->getMockBuilder(BaseException::class)
                          ->enableOriginalConstructor()
                          ->setConstructorArgs(['test'])
                          ->onlyMethods(['callback'])
                          ->getMock();

        if ($shouldIgnore) {

            $exception->expects($this->never())->method('callback');
            $mock->expects($this->never())->method('report');
        } else {

            $exception->expects($this->once())->method('callback');
            $mock->expects($this->once())->method('report');
        }

        $mock->handleException($exception);
    }

    /** @see testCanHandleException */
    public function dataProviderCanHandleException() : array
    {
        return [[true], [false]];
    }

    /**
     * Tests that it can ignore exceptions.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::ignore()
     *
     * @throws ReflectionException
     */
    public function testCanIgnoreExceptions()
    {
        $handler = new ReflectionClass(BaseExceptionHandler::class);

        $method = $handler->getMethod('ignore');
        $method->invokeArgs($this->handler, [BaseException::class]);

        $property = $handler->getProperty('dontReport');
        $property->setAccessible(true);

        $this->assertContains(BaseException::class, $property->getValue($this->handler));
    }

    /**
     * Tests that it can see if the current is a HTTP response.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::isHttpResponse()
     *
     * @throws ReflectionException|Exception
     */
    public function testCanSeeIsHttpResponse()
    {
        Configuration::set('mwc.mode', null);

        $method = new ReflectionMethod($this->handler, 'isHttpResponse');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->handler));

        Configuration::set('mwc.mode', 'cli');

        $this->assertFalse($method->invoke($this->handler));
    }

    /**
     * Tests that it can log an exception error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::log()
     *
     * @throws Exception
     */
    public function testCanLog()
    {
        $level = 'error';
        $message = 'test';
        $exception = new BaseException($message);
        $context = ['key' => 'value'];
        $contextWithException = ['key' => 'value', 'exception' => $exception];

        $logger = $this->getMockBuilder(Logger::class)->getMock();
        $logger->expects($this->once())->method('log')->with($level, $message, $contextWithException);

        $mock = $this->getMockBuilder(BaseExceptionHandler::class)
                     ->enableOriginalConstructor()
                     ->onlyMethods(['getLogger', 'getContext'])
                     ->getMock();
        $mock->expects($this->once())->method('getLogger')->willReturn($logger);
        $mock->expects($this->once())->method('getContext')->willReturn($context);

        $handler = new ReflectionClass(BaseExceptionHandler::class);

        $method = $handler->getMethod('log');
        $method->setAccessible(true);

        $method->invokeArgs($mock, [$exception, $level]);
    }

    /**
     * Tests that it can get a Logger instance.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::getLogger()
     *
     * @throws ReflectionException
     */
    public function testCanGetLogger()
    {
        $method = new ReflectionMethod($this->handler, 'getLogger');
        $method->setAccessible(true);

        $this->assertInstanceOf(Logger::class, $method->invoke($this->handler));
    }

    /**
     * Tests that sets handler properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\BaseException
     *
     * @throws Exception
     */
    public function testCanSetHandlers()
    {
        $exception = new BaseException('foobar');

        set_error_handler($handler = set_exception_handler('var_dump'));

        $this->assertInstanceOf(BaseExceptionHandler::class, $handler[0]);

        $this->expectErrorMessage('Show!');

        throw new BaseException('Show!');
    }

    /**
     * Tests that sets and restores handler properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\BaseException
     *
     * @throws Exception
     */
    public function testCanSetAndRestoreHandlers()
    {
        $this->expectErrorMessage('Show!');

        try {
            throw new BaseException('Do Not Show!');
        } catch (BaseException $e) {
            // @NOTE: recover
        }

        throw new Exception('Show!');
    }

    /**
     * Tests that it can report an exception.
     *
     * @TODO: Create a proper Test
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::report()
     *
     * @throws Exception
     */
    public function testCanReport()
    {
        $mock = $this->getMockBuilder(BaseExceptionHandler::class)
                     ->enableOriginalConstructor()
                     ->onlyMethods(['log'])
                     ->getMock();

        $mock->expects($this->once())->method('log');
        $mock->report(new BaseException(''));
    }

    /**
     * Tests that it can determine whether an exception should be ignored.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler::shouldIgnore()
     *
     * @throws ReflectionException
     */
    public function testCanDetermineWhetherShouldIgnoreException()
    {
        $handler = new ReflectionClass(BaseExceptionHandler::class);

        $method = $handler->getMethod('shouldIgnore');
        $method->setAccessible(true);

        $property = $handler->getProperty('dontReport');
        $property->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->handler, [new BaseException('')]));

        $property->setValue($this->handler, [BaseException::class]);

        $this->assertTrue($method->invokeArgs($this->handler, [new BaseException('')]));
    }
}
