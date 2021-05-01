<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Message;

use DateInterval;
use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Message\Message;

class MessageTest extends WPTestCase
{
    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::__construct
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testConstructor(array $messageData)
    {
        $message = new Message($messageData);

        $messageId = ArrayHelper::get($messageData, 'id');

        $this->assertSame($messageId, $message->getId());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::isExpired
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testIsExpired(array $messageData)
    {
        $messageData['expiredAt'] = null;
        $message = new Message($messageData);

        // Message not expired yet
        $this->assertFalse($message->isExpired());

        // change expiration date to yesterday
        $message->setExpiredAt(new DateTime(date('Y-m-d H:i:s', strtotime('-1 day'))));

        // Message expired
        $this->assertTrue($message->isExpired());

        // instantiate a new Message (to reset expiration date) and set published date to over 30 days ago
        $message = new Message($messageData);
        $message->setPublishedAt((new DateTime())->sub(new DateInterval('P31D')));

        // Message older than 30 days (expired)
        $this->assertTrue($message->isExpired());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setId
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getId
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageIdSetterGetter(array $messageData)
    {
        $id = ArrayHelper::get($messageData, 'id');

        $message = new Message([]);
        $message->setId($id);

        $this->assertSame($id, $message->getId());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setSubject
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getSubject
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageSubjectSetterGetter(array $messageData)
    {
        $subject = ArrayHelper::get($messageData, 'subject');

        $message = new Message([]);
        $message->setSubject($subject);

        $this->assertSame($subject, $message->getSubject());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setBody
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getBody
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageBodySetterGetter(array $messageData)
    {
        $body = ArrayHelper::get($messageData, 'body');

        $message = new Message([]);
        $message->setBody($body);

        $this->assertSame($body, $message->getBody());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setPublishedAt
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getPublishedAt
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessagePublishedAtSetterGetter(array $messageData)
    {
        $message = new Message([]);
        $publishedAt = ArrayHelper::get($messageData, 'publishedAt');

        // test passing the DateTime object
        $message->setPublishedAt($publishedAt);
        $this->assertInstanceOf(DateTime::class, $message->getPublishedAt());
        $this->assertEquals($publishedAt, $message->getPublishedAt());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setActions
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getActions
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageActionsSetterGetter(array $messageData)
    {
        $actions = ArrayHelper::get($messageData, 'actions');

        $message = new Message([]);
        $message->setActions($actions);

        $this->assertSame($actions, $message->getActions());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setRules
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getRules
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageRulesSetterGetter(array $messageData)
    {
        $rules = ArrayHelper::get($messageData, 'rules');

        $message = new Message([]);
        $message->setRules($rules);

        $this->assertSame($rules, $message->getRules());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setLinks
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getLinks
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageLinksSetterGetter(array $messageData)
    {
        $links = ArrayHelper::get($messageData, 'links');

        $message = new Message([]);
        $message->setLinks($links);

        $this->assertSame($links, $message->getLinks());
    }

    /**
     * @param array $messageData
     *
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setExpiredAt
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getExpiredAt
     * @dataProvider messagesDataProvider
     *
     * @throws Exception
     */
    public function testMessageExpiredAtSetterGetter(array $messageData)
    {
        $message = new Message([]);

        $expiredAt = ArrayHelper::get($messageData, 'expiredAt');

        // message has expiration date?
        if ($expiredAt) {
            // yes, test passing the DateTime object
            $message->setExpiredAt($expiredAt);
            $this->assertInstanceOf(DateTime::class, $message->getExpiredAt());
            $this->assertEquals($expiredAt, $message->getExpiredAt());
        } else {
            // no, check if null
            $this->assertNull($message->getExpiredAt());
        }
    }

    /**
     * Messages test cases and scenarios date provider.
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
                    'expiredAt' => (new DateTime())->add(new DateInterval('P1D')),
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
            [
                [
                    'id' => 'test_2',
                    'subject' => 'Test 2 Message Subject',
                    'body' => 'Test 2 message body',
                    'publishedAt' => new DateTime(),
                    'expiredAt' => null,
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
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\Message::setContexts
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Message\Message::getContexts
     */
    public function testCanSetGetContexts()
    {
        $message = new Message([]);

        $contexts = ['foo', 'bar'];

        $this->assertIsArray($message->getContexts());
        $this->assertEmpty($message->getContexts());

        $message->setContexts($contexts);

        $this->assertSame($contexts, $message->getContexts());
    }
}
