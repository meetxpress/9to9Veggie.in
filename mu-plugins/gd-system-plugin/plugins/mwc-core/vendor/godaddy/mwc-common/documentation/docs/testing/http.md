---
id: http
title: HTTP Faking
---

Unit testing Responses, Requests, Redirects, and other HTTP related items can be complicated and often involves mocking a bunch of data.  We realize maintenance of these sort of tests is not ideal for long term scalability.  As a result the Common library provides some test helpers for dynamically faking these items with the actual data they produce so that no hard coded mocking is needed.  

In addition, the helpers strive to provide the engineer using them the ability to modify the data before and after execution so that scenarios can be easily unit tested out of the box.

## Base Offering

The base offering provides a set of shared items available to all HTTP test helpers.

### Including the base offering

To include the base offering with shared assertions, simply include the `HasHttpAssertionsTrait`.

```php
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\HasHttpAssertionsTrait;

class MyClassTest
{    
    use HasHttpAssertionsTrait;
}
```

### Assertions

#### assertSent

`assertSent` allows the engineer to asset that a given http item has been executed and completed.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

RequestHelper::assertSent();

// false

(new Request())->send();

RequestHelper::assertSent();

// true
```

#### assertNotSent

`assertSent` allows for asserting a http item has not been sent.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

RequestHelper::assertNotSent();

// true

(new Request())->send();

RequestHelper::assertNotSent();

// false
```

#### assertSentTimes

`assertSentTimes` allows for asserting a http item has a specific number of occurrences.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())->send();

RequestHelper::assertSentTimes(1);

// true

(new Request())->send();

RequestHelper::assertSentTimes(2);

// true

RequestHelper::assertSentTimes(5);

// false
```

## Requests

The requests faker intercepts requests execution methods to fake as though the request has been built and executed in the exact way it would in the active code base. 

### Faking Requests

When faking requests, the original Request object is recorded to allow for comparison of the built Request object.  The request returns a response object, by default, containing the data expected by the current code base.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

$response = (new Request())
                ->url('http://foo.bar')
                ->send();

echo print_r($response, true);

// Response Class [...]
```

There will commonly be scenarios where you want to test the reactive behavior of a method to different response datasets from a given request.  You also can force mock the Response returned when needed.  To force a response set you must provide `fake()` with a Closure.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake(function() {
    return (new Response);
});

$response = (new Request())
                ->url('http://foo.bar')
                ->send();

echo print_r($response, true);

// Response Class [...]
```

### Assertions

#### assertSentTo

`assertSentTo` allows for checking if a request is being sent to a specific address.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->url('http://foo.bar')
    ->send();

RequestHelper::assertSentTo('http://foo.bar');

// true

RequestHelper::assertSentTo('http://google.com');

// false
```

#### assertHasQueryParams

`assertHasQueryParams` checks if the url query parameters match a given set of parameters / values.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->query(['foo' => 'bar'])
    ->send();

RequestHelper::assertHasQueryParams(['foo' => 'bar']);

// true

RequestHelper::assertHasQueryParams(['foo' => 'test']);

// false
```

#### assertHasHeaders

`assertHasHeaders` checks if the headers are a subset of the given set of header / values.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->headers(['foo' => 'bar'])
    ->send();

RequestHelper::assertHasAllHeaders(['foo' => 'bar', 'Content-Type' => 'application/json']);

// true

RequestHelper::assertHasAllHeaders(['foo' => 'bar']);

// true

RequestHelper::assertHasAllHeaders(['foo' => 'test']);

// false
```

#### assertHasAllHeaders

`assertHasAllHeaders` checks if the all headers match a given set of header / values.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->headers(['foo' => 'bar'])
    ->send();

RequestHelper::assertHasAllHeaders(['foo' => 'bar', 'Content-Type' => 'application/json']);

// true

RequestHelper::assertHasAllHeaders(['foo' => 'bar']);

// false
```

#### assertBodyContains

`assertBodyContains` checks whether the body of the request contains the given string.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->headers(['foo' => 'bar'])
    ->send();

RequestHelper::assertBodyContains('foo')

// true

RequestHelper::assertBodyContains('zip')

// false
```

#### assertBodyEquals

`assertBodyContains` checks whether the body of the request is the same as the given string.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\RequestHelper;

RequestHelper::fake();

(new Request())
    ->headers(['foo' => 'bar'])
    ->send();

RequestHelper::assertBodyEquals('{"foo":"bar"}')

// true

RequestHelper::assertBodyEquals('foo')

// false
```

## Responses

The response faker intercepts a returned response's execution methods to fake as though the return response has been built and executed in the exact way it would in the active code base. 

### Faking Responses

When faking responses, the original Response object is recorded to allow for comparison of the expected versus actual.  The response returns the json encoded response, by default, containing the data expected by the current code base.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;

ResponseHelper::fake();

$response = (new Response())
                ->body(['foo' => 'bar'])
                ->send();

echo print_r($response, true);

// { foo: 'bar' }
```

There will commonly be scenarios where you want to test the reactive behavior of a method for different response datasets.  You also can force mock the Response returned when needed.  To force a response set you must provide `fake()` with a Closure.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;

ResponseHelper::fake(function ($responseInstance) {
    return $responseInstance->getBody() ?? json_encode(['another' => 'value']);
});

$response = (new Response())
                ->body(['foo' => 'bar'])
                ->send();

echo print_r($response, true);

// { another: 'value' }
```

### Assertions

#### assertStatusCode

`assertStatusCode` checks the response code to see if it matches a given integer.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;

ResponseHelper::fake();

(new Response())
    ->status(500)
    ->send();

ResponseHelper::assertStatusCode(200);

// false

ResponseHelper::assertStatusCode(500);

// true
```

#### assertNotStatusCode

`assertNotStatusCode` checks the response code to see if it differs from a given integer.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Tests\Helpers\Http\ResponseHelper;

ResponseHelper::fake();

(new Response())
    ->status(500)
    ->send();

ResponseHelper::assertNotStatusCode(200);

// true

ResponseHelper::assertNotStatusCode(500);

// false
```
