---
id: woocommerceCoupons
title: Coupons
---

The `CouponsRepository` subclass class provides an abstraction layer for common interactions with WooCommerce coupons within a project.  

To use the methods within this class you must import the following:


```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository;
```

## Get a Coupon

Get a WooCommerce coupon from a coupon identifier, such as an ID or coupon code.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository;

CouponsRepository::get(123);
CouponsRepository::get('test-coupon');
```

