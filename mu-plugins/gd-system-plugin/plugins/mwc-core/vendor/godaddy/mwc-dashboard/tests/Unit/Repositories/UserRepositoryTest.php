<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Repositories;

use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository;
use Mockery;
use WP_Mock;

if (! class_exists('WP_User')) {
    class WP_User
    {
        public $user_nicename;
        public $first_name;
        public $last_name;
    }
}

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository
 */
final class UserRepositoryTest extends WPTestCase
{
    /**
     * Tests the getUserName() method.
     *
     * @param string $login
     * @param string $firstName
     * @param string $lastName
     * @param string $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::getUserName()
     * @dataProvider providerGetUserName
     */
    public function testGetUserName(string $login, string $firstName, string $lastName, string $expected)
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);

        $user = Mockery::mock('\WP_User');
        $user->user_nicename = $login;
        $user->shouldReceive('get')
             ->with('first_name')
             ->andReturn($firstName);
        $user->shouldReceive('get')
             ->with('last_name')
             ->andReturn($lastName);

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
             ->andReturn($user);

        $this->assertSame($expected, UserRepository::getUserName());
    }

    /**
     * Tests the getUserName() method with the user param.
     *
     * @param string $login
     * @param string $firstName
     * @param string $lastName
     * @param string $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::getUserName()
     * @dataProvider providerGetUserName
     */
    public function testGetUserNameWithParam(string $login, string $firstName, string $lastName, string $expected)
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);

        $user = Mockery::mock('\WP_User');
        $user->user_nicename = $login;
        $user->shouldReceive('get')
             ->with('first_name')
             ->andReturn($firstName);
        $user->shouldReceive('get')
             ->with('last_name')
             ->andReturn($lastName);

        $this->assertSame($expected, UserRepository::getUserName($user));
    }

    /**
     * @see testGetUserName()
     * @see testGetUserNameWithParam()
     */
    public function providerGetUserName()
    {
        return [
            'full name' => ['johndoe', 'John', 'Doe', 'John Doe'],
            'first name only' => ['johndoe', 'John', '', 'John'],
            'last name only' => ['johndoe', '', 'Doe', 'Doe'],
            'login' => ['johndoe', '', '', 'johndoe'],
        ];
    }

    /**
     * Tests the getPasswordResetUrl() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::getPasswordResetUrl()
     */
    public function testGetPasswordResetUrl()
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);

        $user = Mockery::mock('\WP_User');
        $user->user_login = 'johndoe';

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
             ->andReturn($user);

        \WP_Mock::userFunction('get_password_reset_key')
                ->withArgs([$user])
                ->andReturn('fake-key');

        WP_Mock::userFunction('is_wp_error')->with('fake-key')->andReturn(false);

        $expectedUrl = 'https://example.test/wp-login.php?action=rp&key=fake-key&login=johndoe';
        \WP_Mock::userFunction('network_site_url')
                ->withArgs(['wp-login.php?action=rp&key=fake-key&login=johndoe', 'login'])
                ->andReturn($expectedUrl);

        $this->assertSame($expectedUrl, UserRepository::getPasswordResetUrl());
    }

    /**
     * Tests the getPasswordResetUrl() method with the user param.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::getPasswordResetUrl()
     */
    public function testGetPasswordResetUrlWithParam()
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);

        $user = Mockery::mock('\WP_User');
        $user->user_login = 'johndoe';

        \WP_Mock::userFunction('get_password_reset_key')
                ->withArgs([$user])
                ->andReturn('fake-key');

        WP_Mock::userFunction('is_wp_error')->with('fake-key')->andReturn(false);

        $expectedUrl = 'https://example.test/wp-login.php?action=rp&key=fake-key&login=johndoe';
        \WP_Mock::userFunction('network_site_url')
                ->withArgs(['wp-login.php?action=rp&key=fake-key&login=johndoe', 'login'])
                ->andReturn($expectedUrl);

        $this->assertSame($expectedUrl, UserRepository::getPasswordResetUrl($user));
    }

    /**
     * Tests the getPasswordResetUrl() method when get_password_reset_key() returns an error.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::getPasswordResetUrl()
     */
    public function testGetPasswordResetUrlError()
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);

        $user = Mockery::mock('\WP_User');
        $user->user_login = 'johndoe';

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
             ->andReturn($user);

        $errorMessage = 'Error!';
        $error = Mockery::mock('\WP_Error');
        $error->shouldReceive('get_error_message')
             ->once()
             ->andReturn($errorMessage);
        \WP_Mock::userFunction('get_password_reset_key')
                ->withArgs([$user])
                ->andReturn($error);

        WP_Mock::userFunction('is_wp_error')->with($error)->andReturn(true);
        WP_Mock::userFunction('get_transient');

        $this->expectException(BaseException::class);
        UserRepository::getPasswordResetUrl();
    }

    /**
     * Tests the userOptedInForDashboardMessages() method.
     *
     * @param string $metaValue
     * @param bool $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository::userOptedInForDashboardMessages()
     * @dataProvider providerUserOptedInForDashboardMessages
     */
    public function testUserOptedInForDashboardMessages(string $metaValue, bool $expected)
    {
        WP_Mock::userFunction('metadata_exists')->andReturn('' !== $metaValue);
        if ('' !== $metaValue) {
            WP_Mock::userFunction('get_user_meta')->andReturn($metaValue);
        }

        $user = Mockery::mock('\WP_User');
        $user->ID = 1;

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
             ->andReturn($user);

        $this->assertSame($expected, UserRepository::userOptedInForDashboardMessages());
    }

    /** @see testUserOptedInForDashboardMessages() */
    public function providerUserOptedInForDashboardMessages()
    {
        return [
            'opted in' => ['1', true],
            'opted out' => ['0', false],
            'default' => ['', false],
        ];
    }
}
