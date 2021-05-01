<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository
 */
final class WordPressRepositoryTest extends WPTestCase
{
    /**
     * Tests that it can get the WordPress repository assets URL.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getAssetsUrl()
     * @throws Exception
     */
    public function testCanGetAssetsUrl()
    {
        $this->mockWordPressTransients();

        Configuration::set('mwc.url', '');
        $this->assertIsString(WordPressRepository::getAssetsUrl());
        $this->assertStringNotContainsString('https://example.com', WordPressRepository::getAssetsUrl());

        Configuration::set('mwc.url', 'https://example.com');
        $this->assertStringStartsWith('https://example.com', WordPressRepository::getAssetsUrl());
        $this->assertStringEndsWith('test', WordPressRepository::getAssetsUrl('test'));
    }

    /**
     * Test can retrieve the current page.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getCurrentPage()
     */
    public function testCanGetCurrentPage()
    {
        ArrayHelper::set($GLOBALS, 'pagenow', 'test-page');
        $this->assertEquals('test-page', WordPressRepository::getCurrentPage());
    }

    /**
     * Test can determine if the current page match a value or values.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::isCurrentPage()
     */
    public function testCanDetermineIsAGivenPage()
    {
        ArrayHelper::set($GLOBALS, 'pagenow', 'test-page');
        $this->assertFalse(WordPressRepository::isCurrentPage('wrong-page'));
        $this->assertTrue(WordPressRepository::isCurrentPage('test-page'));
    }

    /**
     * Tests that it can determined whether there's a WordPress instance configured.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::hasWordPressInstance()
     * @throws Exception
     */
    public function testCanDetermineWordPressInstance()
    {
        $this->mockWordPressTransients();

        Configuration::set('wordpress.absolute_path', '');
        $this->assertFalse(WordPressRepository::hasWordPressInstance());

        Configuration::set('wordpress.absolute_path', '/dummy/path');
        $this->assertTrue(WordPressRepository::hasWordPressInstance());
    }

    /**
     * Tests that an exception is thrown if the WordPress instance is not found.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::requireWordPressInstance()
     * @throws Exception
     */
    public function testExceptionIsThrownWhenRequireWordPressInstance()
    {
        $this->mockWordPressTransients();

        Configuration::set('wordpress.absolute_path', 'dummy/path');

        $this->expectException(Exception::class);

        Configuration::set('wordpress.absolute_path', '');

        WordPressRepository::requireWordPressInstance();
    }

    /**
     * Tests that it can throw an error when WordPress Filesystem Instance is not found.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getFilesystem()
     * @throws Exception
     */
    public function testCanThrowExceptionWhenFilesystemInstanceNotFound()
    {
        $this->expectException(Exception::class);

        $GLOBALS['wp_filesystem'] = null;
        WordPressRepository::getFilesystem();
    }

    /**
     * Tests that it can throw an error when WordPress Filesystem Instance returns an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getFilesystem()
     * @throws Exception
     */
    public function testCanThrowExceptionWhenFilesystemInstanceReturnsError()
    {
        // {JO 2021-02-15} - Come back and write the mocks for this test
        $this->assertConditionsMet();
    }

    /**
     * Tests that it can get the WordPress version.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getVersion()
     * @throws Exception
     */
    public function testCanGetVersion()
    {
        $this->mockWordPressTransients();

        Configuration::set('wordpress.version', null);
        $this->assertNull(WordPressRepository::getVersion());

        Configuration::set('wordpress.version', '1.2.3');
        $this->assertEquals('1.2.3', WordPressRepository::getVersion());
    }

    /**
     * Tests that it can get the WordPress version.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::isCliMode()
     * @throws Exception
     */
    public function testCanDetermineIsCliMode()
    {
        $this->mockWordPressTransients();

        Configuration::set('mwc.mode', 'http');

        $this->assertFalse(WordPressRepository::isCliMode());

        Configuration::set('mwc.mode', 'cli');

        $this->assertTrue(WordPressRepository::isCliMode());
    }

    /**
     * Tests that it can get the WordPress version.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::isDebugMode()
     * @throws Exception
     */
    public function testCanDetermineIsDebugMode()
    {
        $this->mockWordPressTransients();

        Configuration::set('wordpress.debug', false);

        $this->assertFalse(WordPressRepository::isDebugMode());

        Configuration::set('wordpress.debug', true);

        $this->assertTrue(WordPressRepository::isDebugMode());
    }

    /**
     * Tests that it can determine whether the current request is a REST API request.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::isApiRequest()
     */
    public function testCanDetermineIsRestApiRequest()
    {
        WP_Mock::userFunction('rest_get_url_prefix')->andReturn('http://www.testing.com/');
        WP_Mock::expectFilter('woocommerce_is_rest_api_request', true);

        $_SERVER['REQUEST_URI'] = null;

        $this->assertFalse(WordPressRepository::isApiRequest());

        $_SERVER['REQUEST_URI'] = 'http://www.testing.com/';

        $this->assertTrue(WordPressRepository::isApiRequest());
    }

    /**
     * Tests that can get the current users id
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getCurrentUserId()
     * @dataProvider providerCanGetCurrentUserId
     *
     * @param $expected
     * @param $searchReturn
     * @throws Exception
     */
    public function testCanGetCurrentUserId($expected, $searchReturn)
    {
        WP_Mock::userFunction('wp_get_current_user')->andReturn($searchReturn);

        $this->assertEquals($expected, WordPressRepository::getCurrentUserId());
    }

    /** @see testCanGetCurrentUserId() */
    public function providerCanGetCurrentUserId() : array
    {
        Mockery::mock('WP_User');

        $user1 = new \WP_User;
        $user1->ID = 0;
        $user2 = new \WP_User;
        $user2->ID = 1;

        return [
            [0, $user1],
            [1, $user2],
            [null, null],
        ];
    }

    /**
     * Tests that can get a user by their email.
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getUserByEmail()
     * @dataProvider providerCanGetUserByEmail
     *
     * @param $email
     * @param $expected
     * @param $searchReturn
     * @throws Exception
     */
    public function testCanGetUserByEmail($email, $expected, $searchReturn)
    {
        WP_Mock::userFunction('get_user_by')->withArgs(['email', $email])->andReturn($searchReturn);

        $this->assertEquals($expected, WordPressRepository::getUserByEmail($email));
    }

    /** @see testCanGetUserByEmail() */
    public function providerCanGetUserByEmail() : array
    {
        return [
            ['test123@gmail.com', null, false],
            ['test123@gmail.com', (object) ['id' => 1], (object) ['id' => 1]],
        ];
    }

    /**
     * Tests that can get a user by their login.
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getUserByLogin()
     * @dataProvider providerCanGetUserByLogin
     *
     * @param $login
     * @param $expected
     * @param $searchReturn
     * @throws Exception
     */
    public function testCanGetUserByLogin($login, $expected, $searchReturn)
    {
        WP_Mock::userFunction('get_user_by')->withArgs(['login', $login])->andReturn($searchReturn);

        $this->assertEquals($expected, WordPressRepository::getUserByLogin($login));
    }

    /** @see testCanGetUserByLogin() */
    public function providerCanGetUserByLogin() : array
    {
        return [
            ['test123@gmail.com', null, false],
            ['test123@gmail.com', (object) ['id' => 1], (object) ['id' => 1]],
            ['test123', null, false],
            ['test123', (object) ['id' => 1], (object) ['id' => 1]],
        ];
    }

    /**
     * Tests that can get a user by their id.
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getUserById()
     * @dataProvider providerCanGetUserById
     *
     * @param $id
     * @param $expected
     * @param $searchReturn
     * @throws Exception
     */
    public function testCanGetUserById($id, $expected, $searchReturn)
    {
        WP_Mock::userFunction('get_user_by')->withArgs(['id', $id])->andReturn($searchReturn);

        $this->assertEquals($expected, WordPressRepository::getUserById($id));
    }

    /** @see testCanGetUserById() */
    public function providerCanGetUserById() : array
    {
        return [
            [100, null, false],
            [1, (object) ['id' => 1], (object) ['id' => 1]],
        ];
    }

    /**
     * Tests that can get converted data from current WordPress screen.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository::getCurrentScreen()
     */
    public function testCanGetCurrentScreen()
    {
        WP_Mock::userFunction('get_current_screen')->once()->andReturnNull();
        $this->assertNull(WordPressRepository::getCurrentScreen(), 'No WP Screen found');

        $mockWPScreen = Mockery::mock('WP_Screen');
        $mockWPScreen->base = 'page';

        WP_Mock::userFunction('get_current_screen')->once()->andReturn($mockWPScreen);
        $this->assertInstanceOf(Screen::class, WordPressRepository::getCurrentScreen(), 'WP Screen found and converted');
    }
}
