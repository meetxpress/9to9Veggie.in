---
id: woocommerce
title: General
---

The `WooCommerceRepository` class provides an abstraction layer for common interactions with WooCommerce within a project.  

To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
```

## Is WooCommerce Active

Check if the WooCommerce plugin is active on the site.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;

WooCommerceRepository::isWooCommerceActive();
```

## Is WooCommerce Connected

Check if the site is actively connected to WooCommerce.com.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;

WooCommerceRepository::isWooCommerceConnected();
```
