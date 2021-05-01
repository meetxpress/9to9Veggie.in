<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController;
use GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController
 */
class SupportTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new SupportController();
        $route = TestHelpers::getInaccessibleProperty($controller, 'route');
        $this->assertSame('support-requests', $route->getValue($controller));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController::createItem()
     *
     * @param string $message
     * @param string $sanitizedMessage
     * @dataProvider dataProviderCreateItem
     * @throws Exception
     */
    public function testCreateItem(string $message, string $sanitizedMessage)
    {
        RequestHelper::fake();
        ResponseHelper::fake();

        $controller = new SupportController();

        // sanitize_text_field is used by StringHelper::sanitize()
        WP_Mock::userFunction('sanitize_text_field')->with('createDebugUser')->andReturn('');
        WP_Mock::userFunction('sanitize_text_field')->andReturn('sanitized');

        // mocks the request parameters
        $request = Mockery::mock('\WP_REST_Request');
        $request->shouldReceive('get_params')->andReturn([
            'subject' => 'subject',
            'message' => $message,
            'replyTo' => 'replyTo',
            'reason'  => 'reason',
            'createDebugUser' => ''
        ]);

        // we don't need to test data formatting here
        \Patchwork\redefine(SupportRequest::class.'::getFormattedRequestData', function() {
            return [];
        });

        $controller->createItem($request);

        // Check successful request response
        RequestHelper::assertSent();
        ResponseHelper::assertSent();
        ResponseHelper::assertStatusCode(200);

        // Check failed request response
        /* @TODO: Uncomment after Sentry Error Changes are included from Common {JO 2021-03-07}
        ResponseHelper::fake();
        RequestHelper::fake(function() {
            throw Exception('fail the support request');
        });

        RequestHelper::assertSent();
        ResponseHelper::assertSent();
        ResponseHelper::assertStatusCode(500);
        **/
    }

    /**
     * @see testCreateItem
     */
    public function dataProviderCreateItem() : array {
        return [
          [ 'message', 'message' ],
          [ 'message with a line break ' . PHP_EOL . 'in the middle', 'message with a line break ' . PHP_EOL . 'in the middle' ],
          [ 'message with <b>some nice HTML</b>', 'message with &lt;b&gt;some nice HTML&lt;/b&gt;' ],
        ];
    }

    /**
     * Tests the getItemSchema() method is returning the correct required and optional arguments.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController::getItemSchema()
     *
     * @param string $arg
     * @param bool $required
     *
     * @dataProvider dataProviderGetItemSchema
     */
    public function testGetItemSchema(string $arg, bool $required)
    {
        WP_Mock::userFunction('__');

        $controller = new SupportController();

        $args = $controller->getItemSchema();

        $this->assertIsArray($args);
        $this->assertEquals($required, $args[$arg]['required'] ?? false);
    }

    /**
     * @see testGetItemSchema
     */
    public function dataProviderGetItemSchema() : array
    {
        return [
            ['replyTo', true],
            ['plugin', false],
            ['subject', true],
            ['message', true],
            ['reason', true],
            ['createDebugUser', false],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController::registerRoutes()
     */
    public function testRegisterRoutes()
    {
        WP_Mock::userFunction('__');

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/support-requests', Mockery::any());

        (new SupportController())->registerRoutes();

        $this->assertConditionsMet();
    }
}
