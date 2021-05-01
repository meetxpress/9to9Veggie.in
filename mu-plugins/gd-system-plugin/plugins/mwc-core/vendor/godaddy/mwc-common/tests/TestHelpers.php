<?php

namespace GoDaddy\WordPress\MWC\Common\Tests;

use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use WP_Mock;
use WP_User;

/**
 * Test helpers.
 */
class TestHelpers
{
    /**
     * Allows for calling protected and private methods on a class.
     *
     * @param string|object $class the class name or instance
     * @param string        $methodName the method name
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public static function getInaccessibleMethod($class, string $methodName) : ReflectionMethod
    {
        $class = new ReflectionClass($class);

        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Allows for calling protected and private properties on a class.
     *
     * @param string|object $class the class name or instance
     * @param string        $propertyName the property name
     *
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    public static function getInaccessibleProperty($class, string $propertyName) : ReflectionProperty
    {
        $class = new ReflectionClass($class);

        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
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
     * Mocks a call to metadata_exists() with the given parameters to return true.
     *
     * @param int    $userId
     * @param string $metaKey
     */
    public static function mockUserMetaDoesExists(int $userId, string $metaKey)
    {
        WP_Mock::userFunction('metadata_exists')
            ->with('user', $userId, $metaKey)
            ->andReturnTrue();
    }
}
