<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Tests\Helpers\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository
 */
final class RequestHelperTest extends WPTestCase
{
    /**
     * Tests that can override the return of a request for testing scenarios
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::fake()
     * @throws Exception
     */
    public function testCanOverrideRequestSendReturn()
    {
        $this->mockWordPressTransients();

        RequestHelper::fake(function() {
            return (new Response())
                ->status(519);
        });

        $response = (new Request())->send();

        $this->assertEquals(519, $response->getStatus());
    }

    /**
     * Tests that fake a requests typically returned response object with mimicked body
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::fake()
     * @throws Exception
     */
    public function testCanReturnExpectedResponse()
    {
        $this->mockWordPressTransients();

        RequestHelper::fake();

        $response = (new Request())
            ->headers(['FOO' => 'BAR'])
            ->body(['key' => 'value'])
            ->setMethod('post')
            ->timeout(100)
            ->send();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(['key' => 'value'], $response->getBody());
    }

    /**
     * Tests that fake can properly track call counts
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertSent()
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertSentTimes()
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertNotSent()
     * @throws Exception
     */
    public function testCanDetermineResponseCounts()
    {
        RequestHelper::fake();

        RequestHelper::assertNotSent();

        $request = (new Request());

        $request->send();

        RequestHelper::assertSent();

        $request->send();

        RequestHelper::assertSentTimes(2);
    }

    /**
     * Tests that can assert a request is to a given url
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertSentTo()
     * @throws Exception
     */
    public function testCanAssertSentToUrl()
    {
        RequestHelper::fake();

        (new Request())->url('foobar')->send();

        RequestHelper::assertSentTo('foobar');
    }

    /**
     * Tests that can assert the query params used in the request match a given array of params
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertHasQueryParams()
     * @throws Exception
     */
    public function testCanAssertQueryParamsUsed()
    {
        RequestHelper::fake();

        (new Request())->query(['foo' => 'bar', 'test' => 'second'])->send();

        RequestHelper::assertHasQueryParams(['foo' => 'bar', 'test' => 'second']);
    }

    /**
     * Tests that can assert the headers sent to the request match a given array of headers
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertHasHeaders()
     * @throws Exception
     */
    public function testCanAssertHeadersUsed()
    {
        RequestHelper::fake();

        (new Request())->headers(['foo' => 'bar'])->send();

        // makes sure these are the only headers we have
        RequestHelper::assertHasAllHeaders(['foo' => 'bar', 'Content-Type' => 'application/json']);

        RequestHelper::assertHasHeaders(['foo' => 'bar']);
        RequestHelper::assertHasHeaders(['foo' => 'bar', 'Content-Type' => 'application/json']);
    }

    /**
     * Tests that can assert the all headers sent to the request match a given array of headers
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertHasAllHeaders()
     * @throws Exception
     */
    public function testCanAssertAllHeadersUsed()
    {
        RequestHelper::fake();

        (new Request())->headers(['foo' => 'bar'])->send();

        RequestHelper::assertHasAllHeaders(['foo' => 'bar', 'Content-Type' => 'application/json']);

        try {
            RequestHelper::assertHasAllHeaders(['foo' => 'bar']);
            $this->fail();
        } catch(Exception $ex) {
            $this->assertEquals(ExpectationFailedException::class, get_class($ex));
        }
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertBodyContains()
     *
     * @param string search string
     * @param string|int|float|array $value test request body
     * @param bool $shouldFail whether the assertion is expected to fail
     *
     * @dataProvider providerCanAssertBodyContains
     */
    public function testCanAssertBodyContains(string $needle, $value, bool $shouldFail)
    {
        RequestHelper::fake();

        (new Request())->body(['query' => $value])->send();

        if ($shouldFail) {
            $this->expectException(ExpectationFailedException::class);
        }

        RequestHelper::assertBodyContains($needle);
    }

    /** @see testCanAssertBodyContains() */
    public function providerCanAssertBodyContains()
    {
        return [
            ['foobar', 'foobar', false],
            ['{"property":"value"}', ['property' => 'value'], false],
            ['foo', 'bar', true],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper::assertBodyEquals()
     *
     * @param string search string
     * @param string|int|float|array $value test request body
     * @param bool $shouldFail whether the assertion is expected to fail
     *
     * @dataProvider providerCanAssertBodyEquals
     */
    public function testCanAssertBodyEquals(string $expected, $value, bool $shouldFail)
    {
        RequestHelper::fake();

        (new Request())->body(['query' => $value])->send();

        if ($shouldFail) {
            $this->expectException(ExpectationFailedException::class);
        }

        RequestHelper::assertBodyEquals($expected);
    }

    /** @see testCanAssertBodyEquals() */
    public function providerCanAssertBodyEquals()
    {
        return [
            ['{"query":"foobar"}', 'foobar', false],
            ['{"query":{"property":"value"}}', ['property' => 'value'], false],
            ['{"query":"foo"}', 'bar', true],
            ['bar', 'bar', true],
        ];
    }
}
