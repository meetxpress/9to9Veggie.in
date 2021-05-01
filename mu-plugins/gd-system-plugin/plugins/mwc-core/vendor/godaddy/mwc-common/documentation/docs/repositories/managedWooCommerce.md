---
id: managed-woocommerce
title: ManagedWooCommerce
---

The `ManagedWooCommerce` repository provides an abstraction layer for common interactions with the Managed WooCommerce platform.  To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
```

## Completed WPNUX Onboarding

Determine if a user has completed the WPNUX onboarding.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::hasCompletedWPNuxOnboarding();
```

## Environment

Determine the current environment.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

// Returns the current env -- production | development
ManagedWooCommerceRepository::getEnvironment();
```

## Is Production

Determine if the site is in a production environment.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isProductionEnvironment();
```

## Is Staging

Determine if the site is in a staging environment.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isStagingEnvironment();
```

## Is Testing

Determine if the site is in a testing environment.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isTestingEnvironment();
```

## Has an eCommerce Plan

Determine if the current instance has an eCommerce plan.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::hasEcommercePlan();
```

## Is Hosted on Managed WordPress

Determine if the site is hosted on GoDaddy Managed WordPress.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isManagedWordPress();
```

## Is a Reseller

Determine if the site is tagged as a reseller account.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isReseller();
```

## Is a Reseller with Support Agreement

Determine if the site is tagged as a reseller account with a support agreement.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isResellerWithSupportAgreement();
```

## Reseller ID (Private label ID)

Gets the site's private label ID. 1 means the site is not a reseller site, but sold directly by GoDaddy.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::getResellerId();
```

## Is Temporary Domain

Determine if the site is currently using a temporary domain.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::isTemporaryDomain();
```

## XID

Every Managed WooCommerce site has a unique identifier available through the `XID` variable that can be retrieved as follows:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::getXid();
```

## Site ID

The site ID will be stored in the database on the `gd_mwc_site_id`  option and cached as a configuration value with key `godaddy.site.id`.

You can get the site ID using the `getSiteId()` method as follows:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

ManagedWooCommerceRepository::getSiteId();
```

If the `gd_mwc_site_id` option is not set when you call `getSiteId()`, the method will update the option in the database and update the configuration cache.
