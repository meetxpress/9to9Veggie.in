<?php

namespace GoDaddy\WordPress\MWC\Core\Events\Subscribers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\SubscriberContract;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Users\User;
use GoDaddy\WordPress\MWC\Core\Exceptions\EventBridgeEventSendFailedException;
use GoDaddy\WordPress\MWC\Core\Http\EventBridgeRequest;

class EventBridgeSubscriber implements SubscriberContract
{
    /**
     * @param EventContract $event
     * @throws Exception
     */
    public function handle(EventContract $event)
    {
        if (! $this->shouldSendEvent($event)) {
            return;
        }

        try {
            $this->sendEvent($event);
        } catch (EventBridgeEventSendFailedException $e) {
            // If an EventBridgeEventSendFailedException exception is thrown, it
            // will automatically report itself to sentry when PHP destructs the
            // object, even if it’s caught in the try-catch above.
        }
    }

    /**
     * Determines whether the given event should be sent.
     *
     * @param EventContract $event event object
     *
     * @return bool
     */
    protected function shouldSendEvent(EventContract $event) : bool
    {
        // don't send if this is not the production environment and the plugin is not configured to send local events
        if (! ManagedWooCommerceRepository::isProductionEnvironment() && ! Configuration::get('events.send_local_events')) {
            return false;
        }

        // only send events that are an EventBridgeEventContract
        return $event instanceof EventBridgeEventContract;
    }

    /**
     * @param EventContract $event
     * @return Response|void
     * @throws Exception
     */
    protected function sendEvent(EventContract $event)
    {
        $response = (new EventBridgeRequest())
            ->url(Configuration::get('mwc.events.api.url'))
            ->setMethod('POST')
            ->headers([
                'Authorization' => Configuration::get('events.auth.type', 'Bearer').' '.Configuration::get('events.auth.token'),
            ])
            ->setSiteId(Configuration::get('godaddy.site.id'))
            ->body([
                'query' => $this->getQuery($event, User::getCurrent()),
            ])
            ->send();

        // TODO: handle HTTP status code that indicate an error as well -- right now it fails only when a WP_Error is produced internally {WV 2021-03-30}
        if ($response->isError()) {
            throw new EventBridgeEventSendFailedException($response->getErrorMessage());
        }

        return $response;
    }

    /**
     * Gets the content for the query parameter.
     *
     * @param EventBridgeEventContract $event event object
     * @param User|null $user current user
     *
     * @return string
     */
    private function getQuery(EventBridgeEventContract $event, User $user = null)
    {
        $userId = $user ? $user->getId() : null;
        $data = json_encode(json_encode($event->getData()));

        return <<<GQL
mutation {
  createEvent(input: { userId: {$userId}, resource: "{$event->getResource()}", action: "{$event->getAction()}", data: {$data} }) {
    statusCode
    message
  }
}
GQL;
    }
}
