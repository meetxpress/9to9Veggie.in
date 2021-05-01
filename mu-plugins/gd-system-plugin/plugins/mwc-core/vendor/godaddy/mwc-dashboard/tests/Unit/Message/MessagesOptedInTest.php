<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Message;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn;
use GoDaddy\WordPress\MWC\Dashboard\Tests\TestHelpers as DashboardTestHelpers;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn
 */
final class MessagesOptedInTest extends WPTestCase
{
    /**
     * Tests the constructor method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $userId = 1;

        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser($userId));

        $mock = $this->getMockBuilder(MessagesOptedIn::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$userId])
            ->onlyMethods(['loadUserMeta'])
            ->getMock();
        $mock->expects($this->once())->method('loadUserMeta');

        $mock->__construct($userId);

        $userIdProperty = TestHelpers::getInaccessibleProperty($mock, 'userId');
        $metaKeyProperty = TestHelpers::getInaccessibleProperty($mock, 'metaKey');

        $this->assertSame($userId, $userIdProperty->getValue($mock));
        $this->assertSame('_mwc_dashboard_messages_opted_in', $metaKeyProperty->getValue($mock));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn::optIn
     */
    public function testCanOptIn()
    {
        WP_Mock::userFunction('metadata_exists', ['times' => 1]);

        $userId = 1;

        $optIn = new MessagesOptedIn($userId);

        DashboardTestHelpers::expectUserMetaSaved($userId, '_mwc_dashboard_messages_opted_in', true);

        $optIn->optIn();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn::optOut
     */
    public function testCanOptOut()
    {
        $userId = 1;
        $this->mockOptInUserWithMeta($userId);

        $optIn = new MessagesOptedIn($userId);

        DashboardTestHelpers::expectUserMetaSaved($userId, '_mwc_dashboard_messages_opted_in', false);

        $optIn->optOut();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn::getUserId
     */
    public function testCanGetUserId()
    {
        $userId = 1;
        $this->mockOptInUserWithMeta($userId);

        $optIn = new MessagesOptedIn($userId);

        $this->assertSame($userId, $optIn->getUserId());
    }

    /**
     * @param int $userId
     */
    private function mockOptInUserWithMeta(int $userId)
    {
        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser($userId));
        DashboardTestHelpers::mockUserMetaDoesNotExists(1, '_mwc_dashboard_messages_opted_in');
    }
}
