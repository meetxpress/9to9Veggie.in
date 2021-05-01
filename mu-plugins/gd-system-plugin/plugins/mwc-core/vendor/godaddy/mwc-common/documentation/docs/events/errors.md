---
id: errors
title: Errors
---

The `BaseExceptionHandler` is a basic handler to manage `Exception` and `ExceptionError` events. It is responsible for handling errors and exception event reporting to a defined logger.

The `BaseException` is the base exception class to be extended by any exceptions we want to be handled by the `BaseExceptionHandler`.

The `SentryException` exception class extends `BaseException` to provide a base class for exceptions that can be reported to Sentry.

## Extending the Handler

The `BaseExceptionHandler` can be extended to provide alternative loggers and handle more event types. Any exception classes wanting to use the extended handler can do so by overriding the `BaseException::getExceptionHandler` method to return an instance of new handler.

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;

class MyExceptionHandler extends BaseExceptionHandler {
}

class MyException extends BaseException {

    protected function getExceptionHandler() : BaseExceptionHandler 
    {
        return new MyExceptionHandler();
    }
}
```

## Callback

The `BaseException` does not provide additional handling for exceptions other than [reporting](errors#report-events) them. Child implementation can override the basic empty `callback` to include additional handling when an `Exception` is to be [reported](errors#report-events).

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;

class MyException extends BaseException {

    public function callback()
    {
        // do something
    }   
}
```

## Context

The `BaseExceptionHandler::getContext` internal method is intended to provide additional system configuration data when a `BaseException` event is logged.

Children implementations can extend this method to provide additional data, if necessary.

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;

class MyHandler extends BaseExceptionHandler {

    protected function getContext(BaseException $exception) : array
    {
        $context = parent::getContext($exception);
        $context['myKey'] = 'myValue';
        return $context;
    }   
}
```

## Error Handling

When the `BaseExceptionHandler` is instantiated, it will automatically convert PHP errors to `ErrorException` exceptions, via `handleError` method.

## Event Handling

When the `BaseExceptionHandler` is instantiated, it will automatically handle `Exception` events to be [reported](errors#report-events), via `handleException` method.

## Ignore events

The `BaseExceptionHandler::ignore` method can be used to define exceptions that should be ignored by the handler.

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;

$handler = new BaseExceptionHandler();
$handler->ignore('MyCustomException');

```

## Report events

The base `BaseExceptionHandler::report` method will invoke a [log](#alternative-loggers) the event.

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;

$handler = new BaseExceptionHandler();
$handler->report(new BaseException('some error'));
```

## Alternative loggers

The `BaseExceptionHandler::getLogger` method can be overridden by child implementations to define an alternative [logger](/components/logger) for custom handling of exception reporting.

```php
use GoDaddy\WordPress\MWC\Common\Exceptions\Handlers\BaseExceptionHandler;
use GoDaddy\WordPress\MWC\Common\Loggers\Logger;

class MyHandler extends BaseExceptionHandler {

    protected function getLogger() : Logger
    {
        return ( new class extends Logger { 
            // Logger implementation instance
        } );
    }   
}
```