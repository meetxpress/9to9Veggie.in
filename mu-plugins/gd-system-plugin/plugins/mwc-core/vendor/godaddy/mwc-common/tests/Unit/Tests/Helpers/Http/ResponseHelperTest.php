<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Tests\Helpers\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository
 */
final class ResponseHelperTest extends WPTestCase
{
    /**
     * Tests that can override a faked response send
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::fake()
     * @throws Exception
     */
    public function testCanOverrideResponseSendReturn()
    {
        $this->mockWordPressTransients();

        ResponseHelper::fake(function() {
            return true;
        });

        $this->assertTrue((new Response)->send());

        ResponseHelper::fake(function() {
            return false;
        });

        $this->assertFalse((new Response)->send());
    }

    /**
     * Tests that fake returns normally expected response.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::fake()
     * @throws Exception
     */
    public function testCanReturnExpectedResponse()
    {
        ResponseHelper::fake();

        $response = (new Response())
            ->error('test error', 500)
            ->send();

        $this->assertJson($response);
        $this->assertEquals([
            'code' => 500,
            'message' => 'test error',
            'success' => false,
        ], json_decode($response, true));

        $response = (new Response())
            ->body(['foo' => 'bar'])
            ->send();

        $this->assertEquals(['foo' => 'bar'], json_decode($response, true));
    }

    /**
     * Tests that fake can properly track call counts
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::assertSent()
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::assertSentTimes()
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::assertNotSent()
     * @throws Exception
     */
    public function testCanDetermineResponseCounts()
    {
        ResponseHelper::fake();

        ResponseHelper::assertNotSent();

        $response = (new Response())->body(['foo' => 'bar']);

        $response->send();

        ResponseHelper::assertSent();

        $response->send();

        ResponseHelper::assertSentTimes(2);
    }

    /**
     * Tests that fake can properly track call counts
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::assertStatusCode()
     * @covers \GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper::assertNotStatusCode()
     * @throws Exception
     */
    public function testCanAssertResponseStatusCode()
    {
        ResponseHelper::fake();

        ResponseHelper::assertNotStatusCode(200);

        (new Response())->status(200)->send();

        ResponseHelper::assertStatusCode(200);

        (new Response())->status(500)->send();

        ResponseHelper::assertStatusCode(500);
    }
}
