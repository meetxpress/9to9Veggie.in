<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Http\Request
 */
final class RequestTest extends WPTestCase
{
    /**
     * Tests that can determine if should set ssl verify.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::__construct()
     * @throws Exception
     */
    public function testCanDetermineIfShouldSSLVerify()
    {
        $this->mockWordPressTransients();

        Configuration::set('mwc.env', 'testing');

        $request = new Request();

        $this->assertTrue($request->sslVerify(true)->sslVerify);
        $this->assertFalse($request->sslVerify(false)->sslVerify);

        Configuration::set('mwc.env', 'production');
        $this->assertTrue($request->sslVerify(false)->sslVerify);
    }

    /**
     * Tests that can set a default return object on construction.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::__construct()
     * @throws Exception
     */
    public function testCanSetDefaultRequestObject()
    {
        $this->mockWordPressTransients();

        Configuration::set('mwc.env', 'testing');

        $request = new Request();

        $this->assertEquals(['Content-Type' => 'application/json'], $request->headers);
        $this->assertEquals(false, $request->sslVerify);
        $this->assertEquals(30, $request->timeout);
    }

    /**
     * Tests that can send a successful Request.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::body()
     * @throws Exception
     */
    public function testCanSendRequestSuccess()
    {
        $this->mockWordPressRequestFunctions();

        $request = new Request();
        $response = $request->body(['key' => 'value'])
            ->url('https://foo/bar')
            ->send();

        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isSuccess());
    }

    /**
     * Tests that can send a Request that errors out.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::body()
     * @throws Exception
     */
    public function testCanSendRequestError()
    {
        $this->mockWordPressRequestFunctions(500, 'error', true);

        $request = new Request();
        $response = $request->body(['key' => 'value'])
            ->url('https://foo/bar')
            ->send();

        $this->assertTrue($response instanceof Response);
        $this->assertTrue($response->isError());
    }

    /**
     * Tests that can set the Request headers.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::headers()
     * @throws Exception
     */
    public function testCanSetRequestHeaders()
    {
        $required = ['Content-Type' => 'application/json'];

        $request = new Request();

        $this->assertEquals($required, $request->headers()->headers);
        $this->assertEquals(array_merge($required, ['test' => 'header']), $request->headers(['test' => 'header'])->headers);
    }

    /**
     * Test that can set the Request query.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::query()
     * @throws Exception
     */
    public function testCanSetRequestQuery()
    {
        $query = ['key' => 'value'];

        $request = new Request();

        $request->query($query);

        $this->assertEquals($query, $request->query);
    }

    /**
     * Test that can send a Request with a body.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::send()
     */
    public function testCanSendRequestWithBody()
    {
        WP_Mock::userFunction('wp_remote_request', [
            'times' => 1,
        ])->with('https://example.org', [
            'body' => json_encode(['test' => 'value']),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'method' => 'POST',
            'sslverify' => false,
            'timeout' => 30,
        ]);

        (new Request('https://example.org'))
            ->setMethod('POST')
            ->body(['test' => 'value'])
            ->send();

        $this->assertConditionsMet();
    }

    /**
     * Test that can send a Request without a body.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::send()
     */
    public function testCanSendRequestWithoutBody()
    {
        WP_Mock::userFunction('wp_remote_request', [
            'times' => 1,
        ])->with('https://example.org', [
            'body' => null,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'method' => 'POST',
            'sslverify' => false,
            'timeout' => 30,
        ]);

        (new Request('https://example.org'))
            ->setMethod('POST')
            ->send();

        $this->assertConditionsMet();
    }

    /**
     * Test that can set the Request Method.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::setMethod()
     * @throws ReflectionException
     */
    public function testCanSetRequestType()
    {
        $request = new Request();
        $type    = TestHelpers::getInaccessibleProperty($request, 'method');

        $this->assertEquals('GET', $type->getValue($request));

        $request->setMethod('POST');

        $this->assertEquals('POST', $type->getValue($request));

        $request->setMethod('INVALID');

        $this->assertEquals('GET', $type->getValue($request));
    }

    /**
     * Test that can set the Request timeout.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Request::timeout()
     */
    public function testCanSetRequestTimeout()
    {
        $request = new Request();

        $this->assertEquals(10, $request->timeout(10)->timeout);
        $this->assertEquals(20, $request->timeout(20)->timeout);
        $this->assertEquals(30, $request->timeout()->timeout);
        $this->assertEquals(40, $request->timeout(40)->timeout);
    }
}
