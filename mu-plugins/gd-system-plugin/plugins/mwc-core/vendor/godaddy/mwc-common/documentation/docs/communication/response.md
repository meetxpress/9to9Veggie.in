---
id: response
title: Response
---

The `Response` class provides methods to handle a response coming from a [request object](/communication/request) or to [send](/communication/request#send) a response externally.  It should be consider a rare dual class -- both an action and data class.

## Methods

### body

Sets the response body.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;

$response = new Response();
$response->body(['key' => 'value']);
```

### error

Sets the response as an error response.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;

$response = new Response();
$response->error(['error']);
```

### getBody

Gets the response body.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

$request = new Request();

// request set up...

$response = $request->send();
$response->getBody();
```

### getErrorMessage

Gets the error message.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

$request = new Request();

// request set up...

$response = $request->send();
$response->getErrorMessage();
```

### getStatus

Returns the response status code.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

$request = new Request();

// request set up...

$response = $request->send();
$response->getStatus();
```

### isError

Checks if the response is an error response.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

$request = new Request();

// request set up...

$response = $request->send();

if ($response->isError()) {
    // ...
}
```

### isSuccess

Checks if the response is a success response.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;
use GoDaddy\WordPress\MWC\Common\Http\Response;

$request = new Request();

// request set up...

$response = $request->send();

if ($response->isSuccess()) {
    // ...
}
```

### send

Sends a response.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;

$response = new Response();
$response->send();
```

### status

Sets the response status code.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;

$response = new Response();
$response->status(200);
```

### success

Sets the response as a successful response.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Response;

$response = new Response();
$response->success();
```
