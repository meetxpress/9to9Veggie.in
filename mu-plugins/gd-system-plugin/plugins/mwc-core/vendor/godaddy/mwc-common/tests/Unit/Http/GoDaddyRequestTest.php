<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Http;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest
 */
final class GoDaddyRequestTest extends WPTestCase
{
    /**
     * Test can set GoDaddy site token by config or manually.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest::siteToken()
     */
    public function testCanSetGDSiteToken()
    {
        $this->mockWordPressTransients();

        $token = 'test1234';
        $request = new GoDaddyRequest();

        // to simulate the token configuration not existing, we must stop the Configuration from reading from its base configuration directory
        $configurationDirectories = TestHelpers::getInaccessibleProperty(Configuration::class, 'configurationDirectories');
        $configurationDirectories->setValue([]);
        Configuration::clear();

        $this->assertEquals($token, $request->siteToken($token)->siteToken);
        $this->assertEquals('empty', $request->siteToken()->siteToken);

        Configuration::set('godaddy.site.token', 'config-token');
        $this->assertEquals('config-token', $request->siteToken()->siteToken);
    }

    /**
     * Test that the X-SITE-TOKEN header is available in the Request class on creation.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest::__construct()
     */
    public function testSetsXSiteHeaderOnCreation()
    {
        $this->mockWordPressTransients();

        $request = new GoDaddyRequest();

        $this->assertTrue(ArrayHelper::has($request->headers, 'X-Site-Token'));
    }
}
