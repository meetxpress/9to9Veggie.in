---
id: has-woocommerce-meta
title: HasWooCommerceMeta
---

The `HasWooCommerceMeta` trait provides a common functionality for interacting with classes that store WooCommerce metadata.

## Load WooCommerce Meta

Loads the metadata information into the parent object so that it is available for use.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;

class MyClass
{
  use HasWooCommerceMetaTrait;

  public function someFunction() {
    $this->loadWooCommerceMeta('defaultValue');
  }
}
```

## Get WooCommerce Meta

Gets the currently loaded metadata beyond the current parent class instance.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;

class MyClass
{
  use HasWooCommerceMetaTrait;

  public function someFunction() {
    $this->loadWooCommerceMeta('defaultValue');

    // defaultValue
    return $this->getWooCommerceMeta();
  }
}
```

## Set WooCommerce Meta

Sets the given metadata in the parent class.

:::note Setting and Saving Are Different
Setting and saving data are two different operations. If you are ready to persist the information permanently, be sure to call [save](/traits/has-woocommerce-meta#save-woocommerce-meta).
:::

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;

class MyClass
{
  use HasWooCommerceMetaTrait;

  public function someFunction() {
    $this->setWooCommerceMeta('myValue');

    // myValue
    return $this->getWooCommerceMeta();
  }
}
```

## Save WooCommerce Meta

Saves the metadata to persist beyond the current parent class instance.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;

class MyClass
{
  use HasWooCommerceMetaTrait;

  public function someFunction() {
    // persisted beyond the class instance
    $this->setWooCommerceMeta('myValue')
         ->saveWooCommerceMeta()
         ->loadWooCommerceMeta();

    // myValue
    return $this->getWooCommerceMeta();     
  }
}
```

## Delete WooCommerce Meta

Deletes the metadata beyond the current parent class instance.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;

class MyClass
{
  use HasWooCommerceMetaTrait;

  public function someFunction() {
    // persisted beyond the class instance
    $this->setWooCommerceMeta('myValue')
         ->saveWooCommerceMeta()
         ->deleteWooCommerceMeta()
         ->loadWooCommerceMeta('defaultValue');

    // defaultValue
    return $this->getWooCommerceMeta();
  }
}
```