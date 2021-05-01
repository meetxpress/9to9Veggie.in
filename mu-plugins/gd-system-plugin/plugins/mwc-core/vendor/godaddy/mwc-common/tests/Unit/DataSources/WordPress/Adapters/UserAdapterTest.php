<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\DataSources\WordPress\Adapters;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\UserAdapter;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Users\User;
use Mockery;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\UserAdapter
 */
final class UserAdapterTest extends WPTestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\UserAdapter::__construct()
     * @dataProvider providerCanStoreDataAsArray
     *
     * @param mixed $data
     * @param array $expected
     * @throws ReflectionException|Exception
     */
    public function testCanStoreDataAsArray($data, array $expected)
    {
        $adapter = new UserAdapter($data);
        $data    = TestHelpers::getInaccessibleProperty($adapter, 'data')->getValue($adapter);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        foreach ($expected as $prop => $value ) {
            $this->assertArrayHasKey($prop, $data);
            $this->assertEquals($value, $data[ $prop ]);
        }
    }

    /** @see testCanStoreDataAsArray */
    public function providerCanStoreDataAsArray() : array
    {
        $nativeUserData = $this->getNativeUserData();

        $wordpressUser = Mockery::mock('WP_User');
        $wordpressUser->data = $this->getWordPressUserData();
        $wordpressUser->user_firstname = $wordpressUser->data['user_firstname'];
        $wordpressUser->user_lastname = $wordpressUser->data['user_lastname'];
        $wordpressUser->nickname = $wordpressUser->data['nickname'];

        $wordpressUser->shouldReceive('to_array')
            ->andReturn(get_object_vars((object) $wordpressUser->data));

        return [
            [$wordpressUser, $wordpressUser->data],
            [User::seed($nativeUserData), $nativeUserData],
            [$nativeUserData, $nativeUserData],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\UserAdapter::convertFromSource()
     */
    public function testCanConvertFromSource()
    {
        $adapter = new UserAdapter($this->getWordPressUserData());

        $this->assertEquals($this->getNativeUserData(), $adapter->convertFromSource());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\UserAdapter::convertToSource()
     */
    public function testCanConvertToSource()
    {
        $adapter = new UserAdapter($this->getNativeUserData());

        $this->assertEquals($this->getWordPressUserData(), $adapter->convertToSource());
    }

    /**
     * Gets a set of WordPress user data.
     *
     * @return array
     */
    private function getWordPressUserData() : array
    {
        return [
            'ID'             => 123,
            'user_email'     => 'foo@example.com',
            'user_login'     => 'foo',
            'user_firstname' => 'Foo',
            'user_lastname'  => 'Bar',
            'nickname'       => 'Baz',
            'user_nicename'  => 'Baz',
        ];
    }

    /**
     * Gets a set of native user data.
     *
     * @return array
     */
    private function getNativeUserData() : array
    {
        return [
            'id'          => 123,
            'email'       => 'foo@example.com',
            'handle'      => 'foo',
            'firstName'   => 'Foo',
            'lastName'    => 'Bar',
            'nickname'    => 'Baz',
            'displayName' => 'Baz',
        ];
    }
}
