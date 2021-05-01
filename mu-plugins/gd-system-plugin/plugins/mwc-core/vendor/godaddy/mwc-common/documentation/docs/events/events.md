---
id: events
title: Events
---

TBD

## Events

The events class provides a central entry point to broadcast standard events implementing an `EventContract` to interested subscribers.  The class is basic, but designed for extensibility to support item like queued processing, conditional process, prioritization, and external processing / handling.  When dealing with events, subscribers, and providers there are a few main methods to note on the event broadcasting side.

### Firing an event

Firing an event is the action that sets all things in motion.  It broadcasts to the system that an event has occurred so that the system may notify interested subscribers.  You may broadcast either a single event or an array of events implementing `EventContract`.

```php
<?php

use GoDaddy\WordPress\MWC\Common\Events\Events;

Events::broadcast(new MyEvent($data));

Events::broadcast([
    new MyEvent($data),
    new AnotherEvent($moreData),
]);
```

### Checking the list of subscribers for an event

In some cases you may want to get a list of all current subscribers for a given event.

```php
<?php

use GoDaddy\WordPress\MWC\Common\Events\Events;

Events::getSubscribers(MyEvent::class);
```

### Check if a subscriber is subscribed to a given event

If you need to confirm a given subscriber is actively subscribed to a given event use the `hasSubscriber()` method.

```php
<?php

use GoDaddy\WordPress\MWC\Common\Events\Events;

Events::hasSubscriber(MyEvent::class, MySubscriber::class);

// true
```

### Available Events

TBD

## Providers

TBD

## Listeners / Subscribers

Listeners, also known as subscribers, are classes which are notified when a given event occurs within the code base.  They are the interested parties an event and have requested to be notified when it occurs.  Subscribers may handle the event however they choose, providing out of the box separation of concerns and standard componetization of functionality.

### Creating a subscriber

Subscribers are classes which implement the `SubscriberContract`.  Each subscriber is required to implement a handler which will receive the triggering event object by default.  Once the event has been received, the subscriber is able to take any action it deems appropriate after being notified of the event.

A basic subscriber should look like the following:

```php
<?php

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\SubscriberContract;

class MySubscriber implements SubscriberContract 
{
    public function handle(EventContract $event)
    {
         // TODO: Implement handle() method.
    }
}
```

### Registering a subscriber to an event

Though the design lends itself to allow dynamic registration, only static registration of a subscriber to an event is allowed at the present moment.  The registrations are declared in the `events` configuration file under the `listeners` key.  Because these are configuration files, the relationships are cached for performance and are overwritable / extendable by other implementing libraries.

Registeration of subscribers is done by listing the class in an array for a given event.

```php
<?php

use GoDaddy\WordPress\MWC\Common\Events\AnotherSubscriber;
use GoDaddy\WordPress\MWC\Common\Events\MySubscriber;

return [
    'listeners' => [
        'GoDaddy\WordPress\MWC\Common\Events\MyEvent' => [
            MySubscriber::class,
            AnotherSubscriber::class,
        ],
    ]
];
```

Note that subscribers will be passed the triggering event to their handler by default.