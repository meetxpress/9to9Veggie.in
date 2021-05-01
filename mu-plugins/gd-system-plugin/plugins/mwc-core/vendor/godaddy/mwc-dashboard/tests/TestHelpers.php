<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests;

use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterAction;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use function Patchwork\always;
use function Patchwork\redefine;
use WP_Mock;
use WP_User;

/**
 * Test helpers.
 */
class TestHelpers
{
    /**
     * Redefines the RegisterAction::execute() method to avoid calling add_action() without setting an expectation.
     */
    public static function mockRegisterActionCalls()
    {
        redefine(RegisterAction::class.'::execute', always(null));
    }

    /**
     * Redefines the RegisterFilter::execute() method to avoid calling add_filter() without setting an expectation.
     */
    public static function mockRegisterFilterCalls()
    {
        redefine(RegisterFilter::class.'::execute', always(null));
    }

    /**
     * Gets a mock of a WP_User instance with the given ID.
     *
     * @param int $userId user ID
     *
     * @return WP_User|MockInterface|LegacyMockInterface
     */
    public static function getMockWordPressUser(int $userId)
    {
        /** @var WP_User|MockInterface|LegacyMockInterface */
        $user = Mockery::mock('WP_User');

        $user->ID = $userId;

        $user->shouldReceive('to_array')->andReturn(['ID' => $userId]);

        return $user;
    }

    /**
     * Sets the return value for WordPressRepository::getUser().
     *
     * @param WP_User $user a WordPress user to be used as the current user
     */
    public static function mockWordPressRepositoryUser(WP_User $user)
    {
        WP_Mock::userFunction('wp_get_current_user')->andReturn($user);
    }

    /**
     * Sets the expectation that update_user_meta() is called at least once with the given parameters.
     *
     * @param int    $userId
     * @param string $metaKey
     * @param mixed  $value
     */
    public static function expectUserMetaSaved(int $userId, string $metaKey, $value)
    {
        // 'times' needs to be specified as a parameter for WP_Mock::userFunction()
        // once(), atLeast(), etc, don't seem to work if called after
        WP_Mock::userFunction('update_user_meta', ['times' => '1+'])
            ->with($userId, $metaKey, $value)
            ->andReturnTrue();
    }

    /**
     * Mocks a call to metadata_exists() with the given parameters to return false.
     *
     * @param int    $userId
     * @param string $metaKey
     */
    public static function mockUserMetaDoesNotExists(int $userId, string $metaKey)
    {
        WP_Mock::userFunction('metadata_exists')
            ->with('user', $userId, $metaKey)
            ->andReturnFalse();
    }

    /**
     * Mocks a call to the WordPress __ function.
     *
     * @param string $string
     * @param string $key
     */
    public static function mockWordPressTranslation(string $string, $key = 'my-key')
    {
        WP_Mock::userFunction('__')
            ->with($string, $key)
            ->andReturn($string);
    }
}
