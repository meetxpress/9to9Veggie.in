---
id: request
title: Request
---

The `Request` class provides chaining methods to set up and send an HTTP request, and to get a [response object](/communication/response).

## Methods

### body

Sets the body of the request.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$response = $request->body(['key' => 'value'])
    ->url('https://foo/bar')
    ->send();
```

### headers

Sets Request headers.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$request->headers(['test' => 'header']);
```

### query

Sets query parameters.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$response = $request->query(['key' => 'value']);
```

### send

Sends the request and returns a [Response](/communication/response) object.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$response = $request->body(['key' => 'value'])
    ->url('https://foo/bar')
    ->send();
```

### sslVerify

Sets the ssl verify property.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$request->sslVerify(true);
```

### timeout

Sets the request timeout property.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$request->timeout(10);
```

### url

Sets the request URL property.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Request;

$request = new Request;
$response = $request->body(['key' => 'value'])
    ->url('https://foo/bar')
    ->send();
```
