<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;
use ReflectionException;
use WP_Mock;
use WP_Mock\Functions;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait
 */
final class HasUserMetaTraitTest extends WPTestCase
{
    /**
     * Tests that can get user meta default value if meta doesn't exist.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait::loadUserMeta()
     * @dataProvider userMetaDataProvider
     */
    public function testCanLoadUserMetaReturnsDefaultValue(int $userId, string $metaKey, $metaValue, $defaultValue)
    {
        $mock = $this->getMockInstance($userId, $metaKey);

        WP_Mock::userFunction('metadata_exists', [
            'times' => 1,
            'return' => false,
        ]);

        $this->assertSame($defaultValue, $mock->loadUserMeta($defaultValue));
    }

    /**
     * Tests that can get user meta actual value if meta does exist.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait::loadUserMeta()
     * @dataProvider userMetaDataProvider
     */
    public function testCanLoadUserMetaReturnsActualValue(int $userId, string $metaKey, $metaValue, $defaultValue)
    {
        $userMetaMock = $this->getMockInstance($userId, $metaKey);

        // mock meta key exists
        WP_Mock::userFunction('metadata_exists', [
            'times' => 1,
            'return' => true,
        ]);

        // mock the meta value
        WP_Mock::userFunction('get_user_meta', [
            'times' => 1,
            'args' => [Functions::type('int'), $userMetaMock->getMetaKey(), true],
            'return' => $metaValue,
        ]);

        $this->assertEquals($metaValue, $userMetaMock->loadUserMeta($defaultValue));
    }

    /**
     * Tests returning loaded user meta value.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed $metaValue
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait::getUserMeta()
     * @dataProvider userMetaDataProvider
     */
    public function testCanGetUserMeta(int $userId, string $metaKey, $metaValue)
    {
        $mock = $this->getMockInstance($userId, $metaKey, $metaValue);

        $this->assertEquals($metaValue, $mock->getUserMeta());

        $mock = $this->getMockInstance($userId, $metaKey);

        $this->assertNull($mock->getUserMeta());
    }

    /**
     * Tests setting user meta value value.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed $metaValue
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait::setUserMeta()
     * @dataProvider userMetaDataProvider
     */
    public function testCanSetUserMeta(int $userId, string $metaKey, $metaValue)
    {
        $mock = $this->getMockInstance($userId, $metaKey);

        $this->assertSame($mock, $mock->setUserMeta($metaValue));

        $this->assertEquals($metaValue, $mock->getUserMeta());
    }

    /**
     * Tests saving user meta value value and return self.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed $metaValue
     * @throws ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait::saveUserMeta()
     * @dataProvider userMetaDataProvider
     */
    public function testCanSaveUserMeta(int $userId, string $metaKey, $metaValue)
    {
        $mock = $this->getMockInstance($userId, $metaKey, $metaValue);

        WP_Mock::userFunction('update_user_meta', [
            'times' => 1,
        ]);

        $this->assertSame($mock, $mock->saveUserMeta());
    }

    /**
     * Gets a mock instance implementing the trait.
     *
     * @param int $userId
     * @param string $metaKey
     * @param mixed|null $value
     * @return object|HasUserMetaTrait
     */
    private function getMockInstance(int $userId, string $metaKey, $value = null)
    {
        return new class($userId, $metaKey, $value) {
            use HasUserMetaTrait;

            public function __construct($userId, $metaKey, $value)
            {
                $this->userId = $userId;
                $this->metaKey = $metaKey;
                $this->value = $value;
            }

            public function getMetaKey() : string
            {
                return $this->metaKey;
            }
        };
    }

    /** @see HasUserMetaTraitTest tests */
    public function userMetaDataProvider() : array
    {
        return [
            [100, 'meta_key', 'value', 'default'],
            [100, 'meta_key', ['foo'], ['bar']],
            [100, 'meta_key', 5, 10],
            [100, 'meta_key', 2.71, 3.14],
            [100, 'meta_key', true, false],
            [100, 'meta_key', false, true],
        ];
    }
}
