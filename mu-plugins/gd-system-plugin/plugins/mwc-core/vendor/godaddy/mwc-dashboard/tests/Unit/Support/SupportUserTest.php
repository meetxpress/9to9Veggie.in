<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Support;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Support\SupportUser;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\SupportUser
 */
final class SupportUserTest extends WPTestCase
{
    /**
     * Tests the createSupportUser() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\SupportUser::create()
     *
     * @param $expected
     * @param $userId
     *
     * @throws Exception
     * @dataProvider providerCanGetConnectUrl
     */
    public function testCreateSupportUser($expected, $userId)
    {
        $userLogin = 'support-user';
        $userEmail = 'support-user@example.test';
        $userPassword = 'fake-password';

        Configuration::set('support.support_user.login', $userLogin);
        Configuration::set('support.support_user.email', $userEmail);

        WP_Mock::userFunction('wp_generate_password')
            ->once()
            ->andReturn($userPassword);

        WP_Mock::userFunction('wp_create_user')
            ->once()
            ->withArgs([$userLogin, $userPassword, $userEmail])
            ->andReturn($userId);

        WP_Mock::userFunction('is_wp_error')
            ->once()
            ->withArgs([$userId])
            ->andReturn(is_int($userId) ? false : true);

        if ($expected) {
            $expected->shouldReceive('add_role')->with('administrator');

            WP_Mock::userFunction('get_user_by')
                ->once()
                ->withArgs(['id', $userId])
                ->andReturn($expected);
        } else {
            WP_Mock::userFunction('get_user_by')
                ->never();
        }

        $this->assertEquals($expected, SupportUser::create());
    }

    /** @see testCreateSupportUser */
    public function providerCanGetConnectUrl() : array
    {
        return [
            [false, Mockery::mock('\WP_Error')],
            [Mockery::mock('WP_User'), 123],
        ];
    }
}
