<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\HasHttpAssertionsTrait;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;
use GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest
 */
class EventBridgeRequestTest extends WPTestCase
{
    use HasHttpAssertionsTrait;

	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest::setSiteId()
	 * @throws Exception
	 */
    public function testCanSetSiteId()
    {
        $bridgeRequest = new EventBridgeRequest();

        $this->assertNull($bridgeRequest->siteId);

        $result = $bridgeRequest->setSiteId('1');

        $this->assertSame($bridgeRequest, $result);
        $this->assertSame('1', $result->siteId);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest::send()
     *
     * @throws Exception
     */
    public function testCanSend()
    {
        RequestHelper::fake();

        (new EventBridgeRequest('http://test'))
            ->setSiteId(1)
            ->send();

        RequestHelper::assertSentTo('http://test');
        RequestHelper::assertSentTimes(1);
        RequestHelper::assertHasHeaders(['X-Site-ID' => '1', 'Content-Type' => 'application/json']);
    }

	/**
	 * @covers       \GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest::headers()
	 * @dataProvider providerCanSetHeadersAndNotClobber
	 *
	 * @param array $additionalHeaders
	 * @param array $expectedHeaders
	 * @throws Exception
	 */
	public function testCanSetHeadersAndNotClobber(array $additionalHeaders, array $expectedHeaders)
	{
		RequestHelper::fake();

		(new EventBridgeRequest('http://test'))
			->headers($additionalHeaders)
			->setSiteId(1)
			->send();

		RequestHelper::assertSentTo('http://test');
		RequestHelper::assertSentTimes(1);
		RequestHelper::assertHasHeaders($expectedHeaders);
	}

	/**
	 * @see testCanSetHeadersAndNotClobber
	 *
	 * @throws Exception
	 */
	public function providerCanSetHeadersAndNotClobber() : array
	{
		$alwaysHeaders = ['X-Site-ID' => '1', 'Content-Type' => 'application/json'];

		return [
			[[], $alwaysHeaders],
			[['foo' => 'bar'], ArrayHelper::combine($alwaysHeaders, ['foo' => 'bar'])],
		];
	}
}
