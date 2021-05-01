<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Message;

use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Message\Message;
use GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus;
use WP_Mock;

class MessageStatusTest extends WPTestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus::__construct
     *
     * @throws Exception
     */
    public function testConstructor()
    {
        WP_Mock::userFunction('metadata_exists', ['times' => 1]);

        $userIdProperty = TestHelpers::getInaccessibleProperty(MessageStatus::class, 'userId');
        $messageIdProperty = TestHelpers::getInaccessibleProperty(MessageStatus::class, 'messageId');

        $userId = 200;
        $messageId = 'another-test';
        $messageStatus = $this->getInstance($userId, $messageId);

        $this->assertSame($userId, $userIdProperty->getValue($messageStatus));
        $this->assertSame($messageId, $messageIdProperty->getValue($messageStatus));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus::isDeleted
     *
     * @throws Exception
     */
    public function testIsDeleted()
    {
        WP_Mock::userFunction('metadata_exists', ['times' => 1, 'return' => false]);

        $this->assertFalse($this->getInstance()->isDeleted());

        WP_Mock::userFunction('metadata_exists', ['times' => 1, 'return' => true]);
        WP_Mock::userFunction('get_user_meta', ['times' => 1, 'return' => MessageStatus::STATUS_DELETED]);

        $this->assertTrue($this->getInstance()->isDeleted());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus::getStatus
     *
     * @throws Exception
     */
    public function testCanGetStatus()
    {
        WP_Mock::userFunction('metadata_exists', ['times' => 1, 'return' => false]);

        $this->assertEquals(MessageStatus::STATUS_UNREAD, $this->getInstance()->getStatus());

        WP_Mock::userFunction('metadata_exists', ['times' => 1, 'return' => true]);
        WP_Mock::userFunction('get_user_meta', ['times' => 1, 'return' => MessageStatus::STATUS_DELETED]);

        $this->assertEquals(MessageStatus::STATUS_DELETED, $this->getInstance()->getStatus());
    }

    /**
     * @param int    $userId
     * @param string $messageId
     *
     * @return MessageStatus
     * @throws Exception
     */
    private function getInstance($userId = 100, $messageId = 'test') : MessageStatus
    {
        return new MessageStatus(new Message([
            'id' => $messageId,
            'subject' => 'Test Message Subject',
            'body' => 'Test message body',
            'publishedAt' => new DateTime(),
            'expiredAt' => null,
            'actions' => [],
            'rules' => [],
            'links' => [],
        ]), $userId);
    }
}
