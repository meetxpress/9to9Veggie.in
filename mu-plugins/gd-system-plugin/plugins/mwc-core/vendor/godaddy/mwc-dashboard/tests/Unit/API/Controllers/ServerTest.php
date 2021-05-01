<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ServerController;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController
 */
final class ServerTest extends WPTestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ServerController::registerRoutes()
     */
    public function testCanRegisterRoutes()
    {
        WP_Mock::userFunction('__');

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/disableSentry', Mockery::any());

        (new ServerController())->registerRoutes();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ServerController::disableSentry()
     * @throws Exception
     */
    public function testCanDisableSentry()
    {
        ResponseHelper::fake();

        Configuration::set('reporting.sentry.enabled', true);
        $this->assertTrue(Configuration::get('reporting.sentry.enabled'));

        (new ServerController())->disableSentry();
        $this->assertFalse(Configuration::get('reporting.sentry.enabled'));

        ResponseHelper::assertSent();
        ResponseHelper::assertStatusCode(200);
    }
}
