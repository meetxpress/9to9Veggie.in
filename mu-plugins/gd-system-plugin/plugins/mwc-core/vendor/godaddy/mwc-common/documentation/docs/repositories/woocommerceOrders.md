---
id: woocommerceOrders
title: Orders
---

The `OrdersRepository` subclass class provides an abstraction layer for common interactions with WooCommerce orders within a project.  

To use the methods within this class you must import the following:


```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository;
```

## Get an Order

Get a WooCommerce order from an order ID.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\OrdersRepository;

OrdersRepository::get(123);
```

