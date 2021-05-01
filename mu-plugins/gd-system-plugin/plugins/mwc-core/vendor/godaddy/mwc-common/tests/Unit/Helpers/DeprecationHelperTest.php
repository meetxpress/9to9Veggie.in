<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Helpers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper
 * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper::logDeprecation()
 */
final class DeprecationHelperTest extends WPTestCase
{
    /** @var string stores the configured PHP error log for restoring after tests done */
    private $errorLog;

    /**
     * Runs before every test.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->mockWordPressTransients();

        $this->errorLog = ini_get('error_log');

        $logFile = tmpfile();

        // prevent printing error logs to console while executing tests: we are not interested in testing the logger in this context
        ini_set('error_log', stream_get_meta_data($logFile)['uri']);

        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Runs after every test.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        ini_set('error_log', $this->errorLog);

        restore_error_handler();
    }

    /**
     * Converts an error to exception for testing purposes.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @throws Exception
     */
    public function errorHandler(int $level, string $message, string $file = '', int $line = 0, array $context = [])
    {
        throw new Exception($message, $level);
    }

    /**
     * Tests that it can flag a class as deprecated while debug mode is enabled.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper::deprecatedClass()
     * @dataProvider providerCanFlagElementAsDeprecated
     *
     * @param $class
     * @param $version
     * @param $replacement
     * @throws Exception
     */
    public function testCanFlagClassAsDeprecated($class, $version, $replacement)
    {
        Configuration::set('mwc.debug', true);

        try {
            DeprecationHelper::deprecatedClass($class, $version, $replacement);
        } catch ( Exception $error ) {
            $this->assertSame($this->getExpectedLogMessage($class, $version, $replacement), $error->getMessage());
            $this->assertSame(E_USER_DEPRECATED, $error->getCode());
        }
    }

    /**
     * Tests that it can flag a class as deprecated while debug mode is disabled.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper::deprecatedClass()
     * @dataProvider providerCanFlagElementAsDeprecated
     *
     * @param $class
     * @param $version
     * @param $replacement
     * @throws Exception
     */
    public function testCanFlagClassAsDeprecatedNoDebugMode($class, $version, $replacement)
    {
        $expected = $this->getExpectedLogMessage($class, $version, $replacement);
        $message = DeprecationHelper::deprecatedClass($class, $version, $replacement);

        $this->assertSame($expected, $message);
    }

    /**
     * Tests that it can flag a function or method as deprecated while debug mode is enabled.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper::deprecatedFunction()
     * @dataProvider providerCanFlagElementAsDeprecated
     *
     * @param $function
     * @param $version
     * @param $replacement
     * @throws Exception
     */
    public function testCanFlagFunctionAsDeprecated($function, $version, $replacement)
    {
        Configuration::set('mwc.debug', true);

        try {
            DeprecationHelper::deprecatedClass($function, $version, $replacement);
        } catch ( Exception $error ) {
            $this->assertSame($this->getExpectedLogMessage($function, $version, $replacement), $error->getMessage());
            $this->assertSame(E_USER_DEPRECATED, $error->getCode());
        }
    }

    /**
     * Tests that it can flag a function or method as deprecated while debug mode is disabled.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper::deprecatedClass()
     * @dataProvider providerCanFlagElementAsDeprecated
     *
     * @param $class
     * @param $version
     * @param $replacement
     * @throws Exception
     */
    public function testCanFlagFunctionAsDeprecatedNoDebugMode($class, $version, $replacement)
    {
        $expected = $this->getExpectedLogMessage($class, $version, $replacement);
        $message = DeprecationHelper::deprecatedClass($class, $version, $replacement);

        $this->assertSame($expected, $message);
    }

    /** {@see testCanFlagClassAsDeprecated} and {@see testCanFlagFunctionAsDeprecated} */
    public function providerCanFlagElementAsDeprecated() : array
    {
        return [
            ['deprecatedItem', '1.2.3', ''],
            ['AnotherDeprecatedItem', '4.5.6', 'replacementItem'],
        ];
    }

    /**
     * Gets an expected log message.
     *
     * @param string $element
     * @param string $version
     * @param string $replacement
     * @return string
     */
    private function getExpectedLogMessage(string $element, string $version, string $replacement) : string
    {
        if ($replacement) {
            $logMessage = "{$element} is deprecated since version {$version}! Use {$replacement} instead.";
        } else {
            $logMessage = "{$element} is deprecated since version {$version} with no alternative available.";
        }

        return $logMessage;
    }
}
