---
id: woocommerceProducts
title: Products
---

The `ProductsRepository` subclass class provides an abstraction layer for common interactions with WooCommerce products within a project.  

To use the methods within this class you must import the following:


```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
```

## Get a Product

Get a WooCommerce product from a Product ID.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;

ProductsRepository::get(123);
```

