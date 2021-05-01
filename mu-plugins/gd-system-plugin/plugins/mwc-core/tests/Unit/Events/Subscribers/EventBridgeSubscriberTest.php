<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Subscribers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Events\ProductCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber;
use GoDaddy\WordPress\MWC\Core\Exceptions\EventBridgeEventSendFailedException;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;
use function Patchwork\redefine;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber
 *
 * @throws Exception
 */
class EventBridgeSubscriberTest extends WPTestCase
{
    /**
     * @covers GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber::handle()
     */
    public function testHandleReturnsEarlyIfSendingLocalEventsIsDisabled()
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isProductionEnvironment')->andReturnFalse();

        Configuration::set('events.send_local_events', false);

        $subscriber = Mockery::mock(EventBridgeSubscriber::class)->makePartial();

        $subscriber->shouldAllowMockingProtectedMethods();
        $subscriber->shouldNotReceive('sendEvent');

        $subscriber->handle(new ProductCreatedEvent());

        $this->assertConditionsMet();
    }

    /**
     * @covers GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber::handle()
     */
    public function testHandleReturnEarlyForOtherTypeOfEvents()
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isProductionEnvironment')->andReturnTrue();

        $subscriber = Mockery::mock(EventBridgeSubscriber::class)->makePartial();

        $subscriber->shouldAllowMockingProtectedMethods();
        $subscriber->shouldNotReceive('sendEvent');

        $subscriber->handle($this->getEventContractInstance());

        $this->assertConditionsMet();
    }

    /**
     * @covers GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber::handle()
     */
    public function testHandleCanCatchException()
    {
        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isProductionEnvironment')->andReturnTrue();

        $sendEventCalled = false;

        redefine(EventBridgeSubscriber::class.'::sendEvent', function () use (&$sendEventCalled) {
            $sendEventCalled = true;

            throw new EventBridgeEventSendFailedException(EventBridgeSubscriber::class."::handle() didn't catch the exception.");
        });

        (new EventBridgeSubscriber())->handle(new ProductCreatedEvent());

        $this->assertTrue($sendEventCalled, 'sendEvent() was not called');
    }

    /**
     * @covers GoDaddy\WordPress\MWC\Core\Events\Subscribers\EventBridgeSubscriber::sendEvent()
     * @throws Exception
     */
    public function testCanSendEvent()
    {
        RequestHelper::fake();

        Configuration::set('godaddy.site.id', 'https://foobar.com');
        Configuration::set('mwc.events.api.url', 'https://example.com');
        Configuration::set('events.auth.type', 'Bearer');
        Configuration::set('events.auth.token', '12345');

        $subscriber = new EventBridgeSubscriber();
        $method = TestHelpers::getInaccessibleMethod($subscriber, 'sendEvent');
        $user = Mockery::mock('WP_User')->shouldReceive('to_array')->andReturn(['id' => 1]);
        $user->ID = 1;

        WP_Mock::userFunction('wp_get_current_user')->andReturn($user);

        $method->invoke($subscriber, new ProductCreatedEvent());

        RequestHelper::assertSentTo('https://example.com');
        RequestHelper::assertSentTimes(1);
        RequestHelper::assertHasHeaders([
            'X-Site-ID'    => 'https://foobar.com',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer 12345',
        ]);
        RequestHelper::assertBodyContains(addslashes('createEvent(input: { userId: 1, resource: "product", action: "create", data: "[]" })'));
    }

    /**
     * Gets an instance of an anonymous class that implements EventContract.
     *
     * @return EventContract
     */
    private function getEventContractInstance() : EventContract
    {
        return new class() implements EventContract {
        };
    }
}
