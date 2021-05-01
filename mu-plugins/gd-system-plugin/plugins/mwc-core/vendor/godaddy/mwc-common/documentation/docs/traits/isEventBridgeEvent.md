---
id: is-event-bridge-event
title: IsEventBridgeEvent
---

The `IsEventBridgeEvent` trait provides common properties and methods for bridge events that used the `EventBridgeEventContract` interface. 

## Get Resource

The method gets the name of the resource for the event

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

class MyEvent
{
  use IsEventBridgeEventTrait;

  public function __construct() {
    $this->resource = 'my-resource';
  }
}

$event = new MyEvent();
$event->getResource(); // 'my-resource'
```

## Get Action

The method gets the name of the action for the event.

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

class MyEvent
{
  use IsEventBridgeEventTrait;

  public function __construct() {
    $this->action = 'my-action';
  }
}

$event = new MyEvent();
$event->getAction(); // 'my-action'
```