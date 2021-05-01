<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Integration\Message;

use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Message\Message;
use GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use WP_Mock;
use WP_User;

class MessageTest extends WPTestCase
{
    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setExpiredAt
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getExpiredAt
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::status
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testStatus(array $messageData)
    {
        $message = new Message($messageData);

        WP_Mock::userFunction('metadata_exists', ['times' => 1]);

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')->andReturn($this->getMockWordPressUser(100));

        $this->assertInstanceOf(MessageStatus::class, $message->status());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::status()
     *
     * @param mixed $userId provided user ID
     *
     * @dataProvider provideStatusInvalidUserIds
     * @throws Exception
     */
    public function testCanGetStatusIfGivenUserIdIsInvalid($userId)
    {
        WP_Mock::userFunction('metadata_exists', ['times' => 1]);

        $this->mockStaticMethod(WordPressRepository::class, 'getUser')->andReturn($this->getMockWordPressUser(100));

        $message = new Message(['id' => 'test']);

        $this->assertInstanceOf(MessageStatus::class, $message->status($userId));
    }

    /**
     * MessagesController test cases and scenarios date provider.
     *
     * @return array
     * @throws Exception
     */
    public function messagesDataProvider() : array
    {
        return [
            [
                [
                    'id' => 'test',
                    'subject' => 'Test Message Subject',
                    'body' => 'Test message body',
                    'publishedAt' => new DateTime(),
                    // make sure it's always set to expire on the next day
                    'expiredAt' => new DateTime('+1 day'),
                    'actions' => [
                        ['text' => 'Visit Us', 'href' => 'https://godaddy.com', 'type' => 'link'],
                        ['text' => 'Okay', 'href' => '#', 'type' => 'button'],
                    ],
                    'rules' => [
                        [
                            'label' => 'WooCommerce Version',
                            'name' => 'wcVersion',
                            'type' => 'version',
                            'rel' => 'system',
                            'comparator' => 'environment/version',
                            'operator' => 'greaterThan',
                            'value' => '4.2.0',
                        ],
                    ],
                    'links' => [
                        ['href' => '"wc/v3/system_status', 'rel' => 'system', 'type' => 'GET'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @see testCanGetStatusIfGivenUserIdIsInvalid()
     *
     * @return array
     */
    public function provideStatusInvalidUserIds() : array
    {
        return [
            [null],
            [true],
            [false],
            ['not-a-number'],
            [0],
            [(object) []],
        ];
    }

    /**
     * Gets a mock of a WP_User instance with the given ID.
     *
     * @param int $userId user ID
     *
     * @return WP_User|MockInterface|LegacyMockInterface
     */
    protected function getMockWordPressUser(int $userId)
    {
        /** @var WP_User|MockInterface|LegacyMockInterface */
        $user = Mockery::mock('WP_User');

        $user->ID = $userId;

        return $user;
    }
}
