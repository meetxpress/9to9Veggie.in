<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController;
use GoDaddy\WordPress\MWC\Dashboard\Message\Message;
use GoDaddy\WordPress\MWC\Dashboard\Message\MessageStatus;
use GoDaddy\WordPress\MWC\Dashboard\Tests\TestHelpers as DashboardTestHelpers;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use ReflectionException;
use WP_Mock;
use WP_REST_Request;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController
 */
final class MessagesTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new MessagesController();
        $route = TestHelpers::getInaccessibleProperty($controller, 'route');
        $this->assertSame('messages', $route->getValue($controller));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::registerRoutes()
     */
    public function testCanRegisterRoutes()
    {
        WP_Mock::userFunction('__');

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/messages', Mockery::any());

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/messages/bulk', Mockery::any());

        WP_Mock::userFunction('register_rest_route', ['times' => 2])
            ->with('godaddy/mwc/v1', '/messages/(?P<id>[a-zA-Z0-9-]+)', Mockery::any());

        WP_Mock::userFunction('register_rest_route', ['times' => 2])
            ->with('godaddy/mwc/v1', '/messages/opt-in', Mockery::any());

        (new MessagesController)->registerRoutes();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::optIn()
     */
    public function testCanOptIn()
    {
        $userId = 1;
        WP_Mock::userFunction('sanitize_text_field')->andReturnArg(0);

        // set the current user for the test
        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser($userId));

        // let's make sure the method returns the result of calling rest_ensure_response()
        $response = (object) ['response' => true];

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'userId' => $userId,
                'optedIn' => true,
            ])
            ->andReturn($response);

        DashboardTestHelpers::mockUserMetaDoesNotExists($userId, '_mwc_dashboard_messages_opted_in');
        DashboardTestHelpers::expectUserMetaSaved($userId, '_mwc_dashboard_messages_opted_in', true);

        $this->assertSame($response, (new MessagesController())->optIn());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getItems()
     */
    public function testCanGetItems()
    {
        $message = Mockery::mock(Message::class);
        $expiredMessage = Mockery::mock(Message::class);
        $deletedMessage = Mockery::mock(Message::class);
        $deletedMessageStatus = Mockery::mock(MessageStatus::class);
        $messageStatus = Mockery::mock(MessageStatus::class);

        $messageStatus->shouldReceive('isDeleted')->andReturnFalse();
        $deletedMessageStatus->shouldReceive('isDeleted')->andReturnTrue();

        $message->shouldReceive([
            'getId' => 'test-message',
            'isExpired' => false,
            'status' => $messageStatus,
        ]);

        $expiredMessage->shouldReceive([
            'getId' => 'expired-message',
            'isExpired' => true,
            'status' => $messageStatus,
        ]);

        $deletedMessage->shouldReceive([
            'getId' => 'deleted-message',
            'isExpired' => false,
            'status' => $deletedMessageStatus,
        ]);

        /** @var MessagesController|MockInterface */
        $controller = Mockery::mock(MessagesController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('getItems')->once()->andReturn(
            ['messages' => [$message, $expiredMessage, $deletedMessage]]
        );

        // mocks the request parameters
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('query')->andReturn('');

        WP_Mock::passthruFunction('rest_ensure_response');
        /** @var Message[] because rest_ensure_response() will pass through the array of messages */
        $items = ArrayHelper::get($controller->getItems($request), 'messages');

        $this->assertIsArray($items);
        $this->assertCount(3, $items);
        $this->assertContainsOnlyInstancesOf(Message::class, $items);
        $this->assertSame($message->getId(), current($items)->getId());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getAllMessages()
     * @throws ReflectionException
     */
    public function testCanGetAllMessages()
    {
        /** @var MessagesController|MockInterface */
        $controller = Mockery::mock(MessagesController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('getMessagesData')
            ->once()
            ->andReturn($this->getTestMessagesData());

        $method = TestHelpers::getInaccessibleMethod(MessagesController::class, 'getAllMessages');

        $messages = $method->invoke($controller);

        $this->assertCount(1, $messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getMessagesData()
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCanGetMessagesData()
    {
        $url = 'https://example.org/messages';

        Configuration::set('messages.api.url', $url);

        $data = ['messages' => $this->getTestMessagesData()];

        $this->mockWordPressRequestFunctionsWithArgs([
            'url' => $url,
            'response' => [
                'code' => 200,
                'body' => $data,
            ],
        ]);

        $method = TestHelpers::getInaccessibleMethod(MessagesController::class, 'getMessagesData');

        $this->assertSame($data['messages'], $method->invoke((new MessagesController())));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getMessagesUrl()
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCanGetMessageUrl()
    {
        $url = 'https://example.org/messages';

        Configuration::set('messages.api.url', $url);

        $method = TestHelpers::getInaccessibleMethod(MessagesController::class, 'getMessagesUrl');

        $this->assertSame($url, $method->invoke(new MessagesController()));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::buildMessage()
     *
     * @param null|string|array $context
     * @param array $expectedContext
     *
     * @throws ReflectionException
     *
     * @dataProvider provideCanBuildMessage
     */
    public function testCanBuildMessage($context, array $expectedContext)
    {
        $method = TestHelpers::getInaccessibleMethod(MessagesController::class, 'buildMessage');

        $data = ArrayHelper::get($this->getTestMessagesData(), 0);

        if (isset($context)) {
            $data['contexts'] = $context;
        }

        $message = $method->invoke(new MessagesController(), $data);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($expectedContext, $message->getContexts());
    }

    /** @see testCanBuildMessage */
    public function provideCanBuildMessage() : array
    {
        return [
            'with no context'           => [null, ['global']],
            'with a given context'      => ['context', ['context']],
            'with an array of contexts' => [['context-1', 'context-2'], ['context-1', 'context-2']],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getMatchingMessageById()
     * @throws ReflectionException
     */
    public function testCanGetMatchingMessageById()
    {
        $messages = new MessagesController();
        $buildMessageMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'buildMessage');
        $getMatchMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'getMatchingMessageById');

        $message = $buildMessageMethod->invoke($messages, ArrayHelper::get($this->getTestMessagesData(), 0));

        $this->assertInstanceOf(Message::class, $message);

        $this->assertSame($message, $getMatchMethod->invoke($messages, [$message], 'test-message'));
        $this->assertNull($getMatchMethod->invoke($messages, [$message], 'another-message'));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::prepareItem()
     * @throws ReflectionException
     */
    public function testCanPrepareItem()
    {
        $messages = new MessagesController();
        $buildMessageMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'buildMessage');
        $prepareItemMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'prepareItem');

        $this->mockGetMessageStatus(1, 'test-message', MessageStatus::STATUS_DELETED);

        $message = $buildMessageMethod->invoke($messages, ArrayHelper::get($this->getTestMessagesData(), 0));
        $messageData = $prepareItemMethod->invoke($messages, $message);

        $this->assertIsArray($messageData);
        $this->assertArrayHasKey('id', $messageData);
        $this->assertArrayHasKey('status', $messageData);
    }

    /**
     * @param int    $userId
     * @param string $messageId
     * @param string $status
     */
    private function mockGetMessageStatus(int $userId, string $messageId, string $status)
    {
        TestHelpers::mockWordPressRepositoryUser(TestHelpers::getMockWordPressUser($userId));
        TestHelpers::mockUserMetaDoesExists($userId, '_mwc_dashboard_message_status_'.$messageId);
        WP_Mock::userFunction('get_user_meta')
            ->with($userId, '_mwc_dashboard_message_status_'.$messageId, true)
            ->andReturn($status);
    }

    /**
     * @param int    $userId
     * @param string $messageId
     * @param string $status
     */
    private function mockUpdateMessageStatus(int $userId, string $messageId, string $status)
    {
        DashboardTestHelpers::mockUserMetaDoesNotExists($userId, "_mwc_dashboard_message_status_{$messageId}");
        DashboardTestHelpers::expectUserMetaSaved($userId, "_mwc_dashboard_message_status_{$messageId}", $status);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::updateItem()
     * @throws ReflectionException
     */
    public function testCanUpdateItem()
    {
        WP_Mock::userFunction('sanitize_text_field')->andReturnArg(0);

        $userId = 1;
        $messageId = 'test-message';
        $status = MessageStatus::STATUS_DELETED;

        $this->mockGetMessageStatus($userId, $messageId, $status);

        // mock the request parameters
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('id')->andReturn($messageId);
        $request->shouldReceive('get_param')->with('status')->andReturn($status);

        Configuration::set('messages.api.url', 'https://example.org/messages');

        /** @var MessagesController|MockInterface */
        $controller = Mockery::mock(MessagesController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('getMessagesData')
            ->once()
            ->andReturn($this->getTestMessagesData());

        $this->mockUpdateMessageStatus($userId, $messageId, $status);

        $prepareItemMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'prepareItem');
        $buildMessageMethod = TestHelpers::getInaccessibleMethod(MessagesController::class, 'buildMessage');

        // let's make sure the method returns the result of calling rest_ensure_response()
        $message = $prepareItemMethod->invoke(
            $controller,
            $buildMessageMethod->invoke($controller, ArrayHelper::get($this->getTestMessagesData(), 0))
        );

        WP_Mock::userFunction('rest_ensure_response')
            ->with($message)
            ->andReturn($message);

        $this->assertSame($message, $controller->updateItem($request));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::deleteItem()
     */
    public function testCanDeleteItem()
    {
        $userId = '101';
        $messageId = '202';

        WP_Mock::userFunction('sanitize_text_field')->andReturnArg(0);

        // mocks the WordPress user
        $user = Mockery::mock('WP_User');
        $user->ID = $userId;

        DashboardTestHelpers::mockWordPressRepositoryUser($user);

        // mocks the request parameters
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('id')->andReturn($messageId);

        // mocks a WP_REST_Response object
        $expectedResponse = new class {
            public $status;

            public function set_status($status)
            {
                $this->status = $status;
            }
        };

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'id' => $messageId,
                'status' => MessageStatus::STATUS_DELETED,
            ])
            ->andReturn($expectedResponse);

        DashboardTestHelpers::mockUserMetaDoesNotExists($userId, "_mwc_dashboard_message_status_{$messageId}");
        DashboardTestHelpers::expectUserMetaSaved($userId, "_mwc_dashboard_message_status_{$messageId}",
            MessageStatus::STATUS_DELETED);

        $response = (new MessagesController())->deleteItem($request);

        $this->assertSame($expectedResponse, $response);
        $this->assertEquals(204, $response->status);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::optOut
     */
    public function testCanOptOut()
    {
        $userId = 1;

        // set the current user for the test
        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser($userId));

        // let's make sure the method returns the result of calling rest_ensure_response()
        $response = (object) ['response' => true];

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'userId' => $userId,
                'optedIn' => false,
            ])
            ->andReturn($response);

        DashboardTestHelpers::mockUserMetaDoesNotExists($userId, '_mwc_dashboard_messages_opted_in');
        DashboardTestHelpers::expectUserMetaSaved($userId, '_mwc_dashboard_messages_opted_in', false);

        $this->assertSame($response, (new MessagesController())->optOut());
    }

    /**
     * Tests the getItemSchema() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::getItemSchema()
     */
    public function testGetItemSchema()
    {
        WP_Mock::userFunction('__');

        $controller = new MessagesController();

        $this->assertIsArray($controller->getItemSchema());
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController::updateItems()
     *
     * @param string $userId user ID
     * @param array  $messageIds message IDs for the messages to update
     * @param string $status new message status
     *
     * @dataProvider provideUpdateItemsTestData
     */
    public function testCanUpdateItems(string $userId, array $messageIds, string $status)
    {
        WP_Mock::userFunction('sanitize_text_field')->andReturnArg(0);

        // set the current user for the test
        $user = Mockery::mock('WP_User');
        $user->ID = $userId;

        DashboardTestHelpers::mockWordPressRepositoryUser($user);

        // mock the request parameters
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('ids')->andReturn($messageIds);
        $request->shouldReceive('get_param')->with('status')->andReturn($status);

        // let's make sure the method returns the result of calling rest_ensure_response()
        $response = (object) ['response' => true];

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'ids' => $messageIds,
                'status' => $status,
            ])
            ->andReturn($response);

        foreach ($messageIds as $messageId) {
            DashboardTestHelpers::mockUserMetaDoesNotExists($userId, "_mwc_dashboard_message_status_{$messageId}");
            DashboardTestHelpers::expectUserMetaSaved($userId, "_mwc_dashboard_message_status_{$messageId}", $status);
        }

        $this->assertSame($response, (new MessagesController())->updateItems($request));
    }

    /**
     * @see testCanUpdateItems()
     */
    public function provideUpdateItemsTestData() : array
    {
        return [
            ['101', ['123', '456'], MessageStatus::STATUS_UNREAD],
            ['101', ['123', '456'], MessageStatus::STATUS_READ],
            ['101', ['123', '456'], MessageStatus::STATUS_DELETED],
        ];
    }

    /**
     * Gets an array of messages data for testing.
     *
     * @return array
     */
    private function getTestMessagesData() : array
    {
        return [
            [
                'id' => 'test-message',
                'subject' => 'Test Message',
                'body' => 'This is clearly a test.',
                'publishedAt' => (new DateTime('yesterday'))->format(DATE_ATOM),
                'expiredAt' => (new DateTime('+1 week'))->format(DATE_ATOM),
                'actions' => [
                    [
                        'text' => 'Sign me up!',
                        'href' => 'https://example.org/sign-in',
                        'type' => 'button',
                    ],
                ],
                'rules' => [
                    [
                        'label' => 'WooCommerce version',
                        'name' => 'wcVersion',
                        'type' => 'version',
                        'rel' => 'system',
                        'comparator' => 'environment/version',
                        'operator' => 'greaterThan',
                        'value' => '4.2.0',
                    ],
                ],
                'links' => [],
                'contextStatus' => 'testStatus',
            ],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\Messages::filterMessages()
     * @throws ReflectionException
     *
     * @dataProvider provideCanFilterMessages
     */
    public function testCanFilterMessages(array $allMessages, $filters, array $expected)
    {
        /** @var Messages|MockInterface */
        $controller = Mockery::mock(MessagesController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $method = TestHelpers::getInaccessibleMethod(MessagesController::class, 'filterMessages');

        $messages = $method->invokeArgs($controller, [$allMessages, $filters]);

        $this->assertIsArray($messages);
        $this->assertContainsOnlyInstancesOf(Message::class, $messages);
        $this->assertEquals($expected, $messages);
    }

    /** @see testCanFilterMessages */
    public function provideCanFilterMessages() : array
    {
        $message = Mockery::mock(Message::class);
        $expiredMessage = Mockery::mock(Message::class);
        $deletedMessage = Mockery::mock(Message::class);

        $deletedMessageStatus = Mockery::mock(MessageStatus::class);
        $messageStatus = Mockery::mock(MessageStatus::class);

        $messageStatus->shouldReceive('isDeleted')->andReturnFalse();
        $deletedMessageStatus->shouldReceive('isDeleted')->andReturnTrue();

        $message->shouldReceive([
            'getId'       => 'test-message',
            'isExpired'   => false,
            'status'      => $messageStatus,
            'getContexts' => ['context-1', 'another-context'],
        ]);

        $expiredMessage->shouldReceive([
            'getId'       => 'expired-message',
            'isExpired'   => true,
            'status'      => $messageStatus,
            'getContexts' => ['context-2', 'another-context'],
        ]);

        $deletedMessage->shouldReceive([
            'getId'       => 'deleted-message',
            'isExpired'   => false,
            'status'      => $deletedMessageStatus,
            'getContexts' => ['context-2', 'another-context'],
        ]);

        $allMessages = [$message, $expiredMessage, $deletedMessage];

        return [
            'empty message list'                                                   => [[], [], []],
            'message list with expired and deleted messages with no filter'        => [$allMessages, [], [$message]],
            'message list with expired and deleted messages with no filter match'  => [$allMessages, [['context' => ['eq' => 'any-context']]], []],
            'message list with expired and deleted messages with eq filter match'  => [$allMessages, [['context' => ['eq' => 'context-1']]], [$message]],
            'message list with expired and deleted messages with in filter match'  => [$allMessages, [['context' => ['in' => ['context-1', 'context-2']]]], [$message]],
            'message list with expired and deleted messages with invalid operator' => [$allMessages, [['context' => ['invalid' => ['context-1', 'context-2']]]], []],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\Messages::getContextComparator()
     *
     * @param mixed $query
     * @param string $expectedOperator
     * @param string|array $expectedWith
     *
     * @throws ReflectionException
     *
     * @dataProvider dataProviderCanGetContextComparator
     */
    public function testCanGetContextComparator($query, string $expectedOperator, $expectedWith)
    {
        $messages = new MessagesController();

        $reflection = new ReflectionClass($messages);

        $getContextComparator = $reflection->getMethod('getContextComparator');
        $getContextComparator->setAccessible(true);

        $comparator = $getContextComparator->invoke($messages, $query);

        $reflectionComparator = new ReflectionClass($comparator);

        $operatorProperty = $reflectionComparator->getProperty('operator');
        $withProperty = $reflectionComparator->getProperty('with');

        $operatorProperty->setAccessible(true);
        $withProperty->setAccessible(true);

        $this->assertEquals($expectedOperator, $operatorProperty->getValue($comparator));
        $this->assertEquals($expectedWith, $withProperty->getValue($comparator));
    }

    /** @see testCanGetContextComparator */
    public function dataProviderCanGetContextComparator() : array
    {
        $validEqFilter = json_decode('[{ "context": { "eq": "order" } }]', true);
        $validInFilter = json_decode('[{ "context": { "in": ["global", "order"] } }]', true);

        return [
            'With a valid equals filter' => [$validEqFilter, 'eq', 'order'],
            'WIth a valid in filter'     => [$validInFilter, 'in', ['global', 'order']],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\Messages::getQueryFilterParam()
     *
     * @param string|null $query
     * @param mixed $expected
     *
     * @throws ReflectionException
     *
     * @dataProvider dataProviderCanGetQueryFilterParam
     */
    public function testCanGetQueryFilterParam($query, $expected)
    {
        WP_Mock::userFunction('sanitize_text_field')->andReturnArg(0);

        $messages = new MessagesController();

        $reflection = new ReflectionClass($messages);

        $getQueryFilterParam = $reflection->getMethod('getQueryFilterParam');
        $getQueryFilterParam->setAccessible(true);

        // mocks the request parameters
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_param')->with('query')->andReturn($query);

        $this->assertEquals($expected, $getQueryFilterParam->invoke($messages, $request));
    }

    /** @see testCanGetQueryFilterParam */
    public function dataProviderCanGetQueryFilterParam() : array
    {
        return [
            'With a null filter'           => [null, []],
            'With an empty filter'         => ['', []],
            'With a valid query filter'    => ['{ "filters": [{ "context": { "eq": "order" } }] }', json_decode('[{ "context": { "eq": "order" } }]', true)],
            'With an invalid query filter' => ['invalid json', []],
        ];
    }
}
