<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Http\Redirect;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;
use ReflectionException;

final class RedirectTest extends TestCase
{
    /**
     * Test the Redirect Class can build a valid query string
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Redirect::buildUrlString()
     * @throws ReflectionException
     */
    public function testCanBuildARedirectQueryString()
    {
        $method  = TestHelpers::getInaccessibleMethod(Redirect::class, 'buildUrlString');
        $request = new Redirect;

        $request->setQueryParameters(['redirect' => 'param'])->setPath('my/url');
        $this->assertEquals("my/url?redirect=param", $method->invoke($request));

        $request = new Redirect;
        $this->expectException(Exception::class);
        $method->invoke($request);
    }

    /**
     * Test properly stores the query parameters
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Redirect::setQueryParameters()
     */
    public function testCanStoreRedirectQueryParameters()
    {
        $request = new Redirect;
        $params  = ['redirect' => 'param'];

        $this->assertEquals($params, $request->setQueryParameters($params)->queryParameters);
    }

    /**
     * Test can trigger a redirect
     * @TODO: implement after wordpress integration
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Redirect::execute()
     */
    public function testCanRedirect()
    {
        $this->assertTrue(true);
    }

    /**
     * Test can set the url in the constructor
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Http\Redirect::setPath()
     */
    public function testCanSetRedirectPath()
    {
        $request = new Redirect('test/url');

        $this->assertEquals('test/url', $request->path);
        $this->assertEquals('new/url', $request->setPath('new/url')->path);

        $request = new Redirect();

        $this->assertNull($request->path);
    }
}
