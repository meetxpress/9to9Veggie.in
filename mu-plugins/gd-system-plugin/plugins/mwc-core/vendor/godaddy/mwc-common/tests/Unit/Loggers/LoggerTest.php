<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Loggers;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Loggers\Logger;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Loggers\Logger
 */
final class LoggerTest extends WPTestCase
{
    /** @var Logger instance for testing */
    private $logger;

    /** @var string stores the current 'error_log' PHP configuration */
    private $error_log;

    /** @var resource temporary file to write logger data to */
    private $temp_file;

    /**
     * Sets up the logger tests by altering the output of 'error_log' in PHP.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->logger = new Logger();
        $this->error_log = ini_get('error_log');
        $this->temp_file = tmpfile();

        ini_set('error_log', stream_get_meta_data($this->temp_file)['uri']);
    }

    /**
     * Restores the original PHP 'error_log' configuration.
     */
    public function tearDown(): void
    {
        ini_set('error_log', $this->error_log);

        parent::tearDown();
    }

    /**
     * Tests the logger main log function.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Loggers\Logger::log()
     */
    public function testCanLog()
    {
        Configuration::set('mwc.debug', false);

        $this->logger->log(0, 'test', []);

        $error_log = stream_get_contents($this->temp_file);

        $this->assertStringContainsString('test', $error_log);
        $this->assertStringNotContainsString('level', $error_log);
        $this->assertStringNotContainsString('message', $error_log);
        $this->assertStringNotContainsString('context', $error_log);
    }

    /**
     * Tests the logger main log function when the MWC debug mode is on.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Loggers\Logger::log()
     */
    public function testCanLogDebugMode()
    {
        Configuration::set('mwc.debug', true);

        $this->logger->log(0, 'test', []);

        $error_log = stream_get_contents($this->temp_file);

        $this->assertStringContainsString('test', $error_log);
        $this->assertStringContainsString('level', $error_log);
        $this->assertStringContainsString('message', $error_log);
        $this->assertStringContainsString('context', $error_log);
    }
}
