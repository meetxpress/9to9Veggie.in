---
id: godaddy-request
title: GoDaddyRequest
---

The `GoDaddyRequest` is an extension of [request](/communication/request) and wraps a Managed WooCommerce Site Token required by GoDaddy requests.

## Methods

See [request](/communication/request) methods.

### siteToken

Sets the current site API request token.

This method returns the object's self instance.

```php
use GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest;

$request = new GoDaddyRequest();
$request->siteToken('ABCD1234');
```