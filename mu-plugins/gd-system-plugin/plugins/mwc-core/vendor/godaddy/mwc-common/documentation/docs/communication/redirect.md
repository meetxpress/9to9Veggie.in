---
id: redirect
title: Redirect
---

The `Redirect` class provides chaining methods to set up and send an valid HTTP Redirect request.

## Methods

### setPath

Sets redirect base path where the final execution of the redirect should send the user.  This can be either an absolute or relative address.

```php
use GoDaddy\WordPress\MWC\Common\Http\Redirect;

$redirect = new Redirect;

$redirect->setPath('http:://google.com')
    ->execute();

// http://google.com
```

### setQueryParameters

Sets query parameters.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\Redirect;

$redirect = new Redirect;

$redirect->setPath('http:://google.com')
    ->setQueryParameters(['key' => 'value'])
    ->execute();

// http://google.com?key=value
```

### execute

Executes the redirect based on the given states that have been set within the Redirect object.

:::error
Redirect methods are simply building the state of the redirect to be executed.  Until `execute` is called, the redirect will not occur.
:::

```php
use GoDaddy\WordPress\MWC\Common\Http\Redirect;

$redirect = new Redirect;

$redirect->setPath('http:://google.com')
    ->execute();
```
