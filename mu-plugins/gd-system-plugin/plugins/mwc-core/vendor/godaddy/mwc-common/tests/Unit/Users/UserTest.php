<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Users;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Users\User;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Users\User
 */
final class UserTest extends WPTestCase
{
    /**
     * Tests that can get the user ID.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getId()
     */
    public function testCanGetId()
    {
        $user = new User();
        $user->setId(123);

        $this->assertEquals(123, $user->getId());
    }

    /**
     * Tests that can set the user ID.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setId()
     */
    public function testCanSetId()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setId(123));
    }

    /**
     * Tests that can get the user email.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getEmail()
     */
    public function testCanGetEmail()
    {
        $user = new User();
        $user->setEmail('foo@example.com');

        $this->assertEquals('foo@example.com', $user->getEmail());
    }

    /**
     * Tests that can set the user email.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setEmail()
     */
    public function testCanSetEmail()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setEmail('foo@example.com'));
    }

    /**
     * Tests that can get the user login handle.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getHandle()
     */
    public function testCanGetHandle()
    {
        $user = new User();
        $user->setHandle('foo');

        $this->assertEquals('foo', $user->getHandle());
    }

    /**
     * Tests that can set the user login handle.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setHandle()
     */
    public function testCanSetHandle()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setHandle('foo'));
    }

    /**
     * Tests that can get the user first name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getFirstName()
     */
    public function testCanGetFirstName()
    {
        $user = new User();
        $user->setFirstName('Foo');

        $this->assertEquals('Foo', $user->getFirstName());
    }

    /**
     * Tests that can set the user first name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setFirstName()
     */
    public function testCanSetFirstName()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setFirstName('Foo'));
    }

    /**
     * Tests that can get the user last name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getLastName()
     */
    public function testCanGetLastName()
    {
        $user = new User();
        $user->setLastName('Bar');

        $this->assertEquals('Bar', $user->getLastName());
    }

    /**
     * Tests that can set the user last name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setLastName()
     */
    public function testCanSetLastName()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setLastName('Bar'));
    }

    /**
     * Tests that can get the user nickname.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getNickname()
     */
    public function testCanGetNickname()
    {
        $user = new User();
        $user->setNickname('Baz');

        $this->assertEquals('Baz', $user->getNickname());
    }

    /**
     * Tests that can set the user last name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setNickname()
     */
    public function testCanSetNickname()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setNickname('Baz'));
    }

    /**
     * Tests that can get the user nickname.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getDisplayName()
     */
    public function testCanGetDisplayName()
    {
        $user = new User();
        $user->setDisplayName('Test');

        $this->assertEquals('Test', $user->getDisplayName());
    }

    /**
     * Tests that can set the user last name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::setDisplayName()
     */
    public function testCanSetDisplayName()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user->setDisplayName('Test'));
    }

    /**
     * Tests that can get a user full name.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getFullName()
     * @dataProvider providerCanGetFullName
     *
     * @param User $user
     * @param string $expectedResult
     */
    public function testCanGetFullName(User $user, string $expectedResult)
    {
        $this->assertEquals($expectedResult, $user->getFullName());
    }

    /** @see testCanGetFullName */
    public function providerCanGetFullName() : array
    {
        $fullName = (new User())
            ->setFirstName('Foo')
            ->setLastName('Bar');

        $noFirst = (new User())
            ->setLastName('Bar')
            ->setDisplayName('Baz');

        $noLast = (new User())
            ->setFirstName('Foo')
            ->setDisplayName('Baz');

        return [
            'Full name'     => [$fullName, 'Foo Bar'],
            'No first name' => [$noFirst, 'Baz'],
            'No last name'  => [$noLast, 'Baz'],
            'No name'       => [new User(), ''],
        ];
    }

    /**
     * Tests that it can get the password reset URL for a user.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getPasswordResetUrl()
     */
    public function testCanGetPasswordResetUrl()
    {
        $user = (new User)
            ->setId(123)
            ->setHandle('test-login');

        $mock = Mockery::mock('WP_User');
        $mock->ID = 123;
        $mock->user_login = $user->getHandle();

        WP_Mock::userFunction('get_user_by')->withArgs(['id', 123])->once()->andReturn($mock);
        WP_Mock::userFunction('get_password_reset_key')->withArgs([$mock])->once()->andReturn('test');

        $path = "wp-login.php?action=rp&key=test&login=".rawurlencode($mock->user_login);

        WP_Mock::userFunction('network_site_url')->withArgs([$path, 'login'])->once()->andReturn('https://example.com');

        $this->assertEquals('https://example.com', $user->getPasswordResetUrl());
    }

    /**
     * Tests it can determine if a user is logged in.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::isLoggedIn()
     * @dataProvider providerDetermineIfLoggedIn
     *
     * @param User|null $user
     * @param bool $expectedResult
     * @throws Exception
     */
    public function testCanDetermineIfLoggedIn($user, bool $expectedResult)
    {
        $this->mockStaticMethod(User::class.'::getCurrent')->andReturn($user);

        $this->assertEquals($expectedResult, (new User)->isLoggedIn());
    }

    /** @see testCanDetermineIfUserIsLoggedIn */
    public function providerDetermineIfLoggedIn() : array
    {
        return [
            'No current user'     => [null, false],
            'Current authed user' => [(new User()), true],
        ];
    }

    /**
     * Tests that can determine if a user is registered.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::isRegistered()
     * @dataProvider providerCanDetermineIfRegistered
     *
     * @param User $user
     * @param bool $expectedResult
     */
    public function testCanDetermineIfRegistered(User $user, bool $expectedResult)
    {
        WP_Mock::userFunction('username_exists')
               ->withArgs([$user->getHandle()])
               ->andReturn($expectedResult);

        $this->assertEquals($expectedResult, $user->isRegistered());
    }

    /** @see testCanDetermineIfRegistered */
    public function providerCanDetermineIfRegistered() : array
    {
        return [
            'User is not registered' => [(new User())->setId(0), false],
            'User is registered'     => [(new User())->setId(456), true],
        ];
    }

    /**
     * Tests can create a new user
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::create()
     * @throws Exception
     */
    public function testCanCreateUser()
    {
        WP_Mock::userFunction('wp_insert_user')->once()->andReturn(1);

        $data = ['firstName' => 'Joe', 'lastName' => 'Bob'];
        $user = User::create($data);

        Assert::assertArraySubset($data, $user->toArray());
    }

    /**
     * Tests can delete a current user
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::delete()
     * @throws Exception
     */
    public function testCanDeleteUser()
    {
        $user = (new User())->setId(1);

        WP_Mock::userFunction('wp_delete_user')->once()->andReturnTrue();

        $this->assertTrue($user->delete());

        $user->setId(0);

        $this->expectException(SentryException::class);

        $user->delete();
    }

    /**
     * Tests that when attempting to delete a user, if wordpress fails a sentry error is thrown
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::delete()
     * @throws Exception
     */
    public function testCanDeleteUserThrowsWhenWPFails()
    {
        $user = (new User())->setId(1);

        WP_Mock::userFunction('wp_delete_user')->once()->andReturnFalse();

        $this->expectException(SentryException::class);

        $user->delete();
    }

    /**
     * Tests can save a new User
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::save()
     * @throws Exception
     */
    public function testCanSaveUser()
    {
        WP_Mock::userFunction('wp_insert_user')->once()->andReturn(1);

        $error = Mockery::mock('\WP_Error');
        $user  = (new User())
            ->setId(1)
            ->setProperties(['firstName' => 'Joe', 'lastName' => 'Bob'])
            ->save();

        Assert::assertArraySubset(['id'=> 1, 'firstName' => 'Joe', 'lastName' => 'Bob'], $user->toArray());

        WP_Mock::userFunction('wp_insert_user')->once()->andReturn($error);

        $this->expectException(SentryException::class);

        $user->save();
    }

    /**
     * Tests can seed a User object.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::seed()
     * @throws Exception
     */
    public function testCanSeedUser()
    {
        $data = ['firstName' => 'Joe', 'lastName' => 'Bob'];

        Assert::assertArraySubset($data, User::seed($data)->toArray());
    }

    /**
     * Tests can get User by a given property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getByEmail()
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getByHandle()
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getById()
     * @dataProvider providerCanGetUserByProperty
     *
     * @param string $property
     * @param string $key
     * @param mixed  $value
     * @param string $method
     */
    public function testCanGetUserByProperty(string $property, string $key, $value, string $method)
    {
        $user = Mockery::mock('\WP_User')
            ->shouldReceive('to_array')
            ->andReturn([$key => $value]);

        $user->ID = 1;

        WP_Mock::userFunction('get_user_by')->withArgs([$property, $value])->once()->andReturn($user);

        $this->assertInstanceOf(User::class, User::$method($value));

        WP_Mock::userFunction('get_user_by')->withArgs([$property, $value])->once()->andReturnFalse();

        $this->assertNull(User::$method($value));
    }

    /** @see testCanGetUserByProperty */
    public function providerCanGetUserByProperty() : array
    {
        return [
            'Get by Email'  => ['email', 'user_email', 'foo@bar.com', 'getByEmail'],
            'Get by Handle' => ['login', 'user_login', 'foobar', 'getByHandle'],
            'Get by ID'     => ['id', 'ID', 1, 'getById'],
        ];
    }

    /**
     * Tests can get the current user.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Users\User::getCurrent()
     * @dataProvider providerCanGetCurrentUser
     *
     * @param int|null $id
     * @param $expected
     */
    public function testCanGetCurrentUser($expected, int $id = null)
    {
        $user = Mockery::mock('\WP_User')
            ->shouldReceive('to_array')
            ->andReturn([]);

        $user->ID = $id;

        WP_Mock::userFunction('wp_get_current_user')->once()->andReturn($user);

        $this->assertEquals($expected, User::getCurrent());
    }

    /** @see testCanGetCurrentUser */
    public function providerCanGetCurrentUser() : array
    {
        return [
            'No ID'          => [null, null],
            'Anonymous User' => [null, 0],
            'Valid User'     => [(new User)->setId(10), 10],
        ];
    }
}
