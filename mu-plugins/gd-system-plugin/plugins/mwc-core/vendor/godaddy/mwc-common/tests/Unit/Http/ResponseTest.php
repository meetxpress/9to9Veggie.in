<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Http\Response
 */
final class ResponseTest extends WPTestCase
{
    /**
     * Tests that can determine if the Response is an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::isSuccess()
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::isError()
     */
    public function testCanDetermineResponseIsError()
    {
        $response = new Response();
        $this->mockWordPressResponseFunctions(500, 'error', true);

        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isError());
    }

    /**
     * Tests that can determine if the Response is not an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::isSuccess()
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::isError()
     */
    public function testCanDetermineResponseIsNotError()
    {
        $response = new Response();

        $this->mockWordPressResponseFunctions(200, 'success', false);
        $this->assertFalse($response->isError());
        $this->assertTrue($response->isSuccess());
    }

    /**
     * Test that can retrieve the body of the Response.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::body()
     */
    public function testCanGetResponseBody()
    {
        $body = ['key' => 'value'];
        $response = new Response();

        $this->assertEquals($body, $response->body($body)->getBody());
    }

    /**
     * Tests that can set the Response error message.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getErrorMessage()
     */
    public function testCanGetResponseErrorMessage()
    {
        $response = new Response();
        $this->mockWordPressResponseFunctions(200, 'success', false);

        $this->assertNull($response->getErrorMessage());
    }

    /**
     * Tests that can set the Response status code.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getStatus()
     */
    public function testCanGetResponseStatusCode()
    {
        $response = new Response();

        $this->assertEquals(400, $response->status(400)->getStatus());
        $this->assertEquals(200, $response->status(200)->getStatus());
    }

    /**
     * Tests that can set the initial Response body with error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::setInitialBody()
     * @throws ReflectionException
     */
    public function testCanSetInitialResponseBodyError()
    {
        $response = new Response();
        $method = TestHelpers::getInaccessibleMethod($response, 'setInitialBody');

        $this->mockWordPressResponseFunctions(500, ['test' => 'error'], true);
        $method->invokeArgs($response, [['test' => 'error']]);

        $this->assertEquals([], $response->body);
    }

    /**
     * Tests that can set the initial Response body with success.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::setInitialBody()
     * @throws ReflectionException
     */
    public function testCanSetInitialResponseBodySuccess()
    {
        $response = new Response();
        $method = TestHelpers::getInaccessibleMethod($response, 'setInitialBody');

        $this->mockWordPressResponseFunctions(200, ['test' => 'success'], false);
        $method->invokeArgs($response, [['body' => json_encode(['test' => 'success'])]]);

        $this->assertEquals(['test' => 'success'], $response->body);
    }

    /**
     * Tests that can set the initial Response status code with error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::setInitialStatus()
     * @throws ReflectionException
     */
    public function testCanSetInitialResponseStatusCodeError()
    {
        $response = new Response();
        $method = TestHelpers::getInaccessibleMethod($response, 'setInitialStatus');

        $this->mockWordPressResponseFunctions(500, ['test' => 'error'], false);
        $method->invokeArgs($response, [500]);

        $this->assertEquals(500, $response->status);
    }

    /**
     * Test can set the initial Response status code with success.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::setInitialStatus()
     * @throws ReflectionException
     */
    public function testCanSetInitialResponseStatusCodeSuccess()
    {
        $response = new Response();
        $method = TestHelpers::getInaccessibleMethod($response, 'setInitialStatus');

        $this->mockWordPressResponseFunctions(200, ['test' => 'success'], false);
        $method->invokeArgs($response, [200]);

        $this->assertEquals(200, $response->status);
    }

    /**
     * Tests that can set a Response as an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::error()
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getStatus()
     * @throws Exception
     */
    public function testCanSetResponseAsError()
    {
        $response = new Response();
        $response->error(['error']);

        $this->assertEquals(['success' => false, 'code' => null, 'message' => 'error'], $response->getBody());
        $this->assertEquals(null, $response->getStatus());

        $response->error(['new_error'], 410);

        $this->assertEquals(['success' => false, 'code' => 410, 'message' => 'new_error'], $response->getBody());
        $this->assertEquals(410, $response->getStatus());
    }

    /**
     * Tests that can set the Response as a success.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getBody()
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::success()
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getStatus()
     * @throws Exception
     */
    public function testCanSetResponseAsSuccess()
    {
        $response = new Response();
        $response->success();

        $this->assertEquals(['success' => true], $response->success()->getBody());
        $this->assertEquals(300, $response->success(300)->getStatus());
    }

    /**
     * Tests can set the Response body.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getBody()
     */
    public function testCanSetResponseBody()
    {
        $body = ['key' => 'value'];
        $response = new Response();

        $this->assertEquals($body, $response->body($body)->getBody());
    }

    /**
     * Tests that can set the Response status code.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Response::getStatus()
     */
    public function testCanSetResponseStatusCode()
    {
        $response = new Response();

        $this->assertEquals(200, $response->status(200)->getStatus());
    }
}
