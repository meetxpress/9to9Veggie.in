<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Support;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository;
use GoDaddy\WordPress\MWC\Dashboard\Support\Support;
use GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest;
use ReflectionException;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest
 */
final class SupportRequestTest extends WPTestCase
{
    /**
     * Tests that the getters and setters are working properly
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getFrom()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::setFrom()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getMessage()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::setMessage()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getReason()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::setReason()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getSubject()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::setSubject()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getSubjectExtension()
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::setSubjectExtension()
     *
     * @param string $propertyName
     * @param mixed $setValue
     * @param mixed $expectedValue
     *
     * @throws ReflectionException|Exception
     * @dataProvider providerCanUseGettersAndSetters
     */
    public function testCanUseGettersAndSetters(string $propertyName, $setValue, $expectedValue)
    {
        $request  = new SupportRequest();
        $property = TestHelpers::getInaccessibleProperty($request, $propertyName);
        $getter   = 'get' . ucfirst($propertyName);
        $setter   = 'set' . ucfirst($propertyName);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getPluginDataBySlug')
            ->withArgs([$setValue])
            ->andReturn($expectedValue);

        $property->setValue($request, null);

        $this->assertNull($property->getValue($request));

        $request->{$setter}($setValue);

        $this->assertEquals($expectedValue, $request->{$getter}());
    }

    /** @see testCanUseGettersAndSetters */
    public function providerCanUseGettersAndSetters() : array
    {
        return [
            ['from', 'foo@bar.com', 'foo@bar.com'],
            ['message', 'some long support message', 'some long support message'],
            ['reason', 'some fake reason', 'some fake reason'],
            ['subject', 'my subject', 'my subject'],
            ['subjectExtension', 'extension_slug', ['Name' => 'Some Plugin', 'Version' => '1.0.0']],
        ];
    }

    /**
     * Tests that can get the requesting user
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getRequestingUser()
     */
    public function testCanGetRequestingUser()
    {
        \Mockery::mock('\WP_User');

        $request  = new SupportRequest();
        $user1    = (new \WP_User);
        $user2    = (new \WP_User);
        $property = TestHelpers::getInaccessibleProperty($request, 'from');

        $user1->ID = 1;
        $user2->ID = 2;

        $this->mockStaticMethod(WordPressRepository::class, 'getUserByEmail')
            ->withArgs(['test@email.com'])
            ->andReturn($user1);

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')
            ->andReturn($user2);

        $request->setFrom('');

        $this->assertEquals($user2, $request->getRequestingUser());

        $request->setFrom('test@email.com');

        $this->assertEquals($user1, $request->getRequestingUser());

        $property->setValue($request, null);

        $this->assertEquals($user2, $request->getRequestingUser(), 'fallback to getUser when user not found by email');
    }

    /**
     * Tests that can get formatted data for the request
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Support\SupportRequest::getFormattedRequestData()
     *
     * @throws Exception
     */
    public function testCanGetFormattedRequestData()
    {
        $request  = new SupportRequest();
        $user     = $this->getMockBuilder('\WP_User')
            ->disableOriginalConstructor()
            ->getMock();

        $user->ID = 2;

        $method   = TestHelpers::getInaccessibleMethod($request, 'getFormattedRequestData');

        $this->mockWordPressTransients();
        $this->mockStaticMethod(WordPressRepository::class, 'getUserByEmail')
            ->andReturn($user);
        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getPluginDataBySlug')
            ->andReturn(['Name' => 'My Plugin', 'Version' => '1.0.0']);
        $this->mockStaticMethod(UserRepository::class, 'getUserName')
            ->andReturn('user_name');
        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSubscriptionEnd')
            ->andReturn('2030-01-01');
        $this->mockStaticMethod(Support::class, 'getConnectType')
            ->andReturn('godaddy');
        $this->mockStaticMethod(UserRepository::class, 'getPasswordResetUrl')->andReturn('foo');
        \Patchwork\redefine(SupportRequest::class.'::getSystemStatus', function() {
           return null;
        });

        Configuration::set('support.support_user.email', 'support@email.com');
        Configuration::set('support.support_user.login', 'support_login');

        $request->setFrom('test@email.com')
            ->setSubject('Request subject')
            ->setReason('some special reason')
            ->setMessage('help, I need support')
            ->setSubjectExtension('plugin_slug');


        $data = $method->invoke($request);

        $this->assertEquals([
            'ticket'               => ['subject' => 'Request subject', 'description' => 'help, I need support'],
            'customer'             => ['name'  => 'user_name', 'email' => 'test@email.com'],
            'reason'               => 'some special reason',
            'plugin'               => [
                'name'             => 'My Plugin',
                'version'          => '1.0.0',
                'support_end_date' => '2030-01-01',
            ],
            'support_bot_context'  => 'godaddy',
            'system_status_report' => null,
            'support_user'         => [
              'user_id'            => 2,
              'password_reset_url' => 'foo',
            ],
        ], $data);
    }
}
