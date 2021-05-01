---
id: has-user-meta
title: HasUserMeta
---

The `HasUserMeta` trait provides a common functionality for interacting with classes that store user metadata.

:::danger WordPress specific
The contents of this trait are specific to WordPress functionality and expectations.  Specifically they interact with the way that WordPress stores Many to Many relationships.  WordPress stores all relationship information in a single column versus the more common column for each data point.  Be sure to confirm your expected data structures before using this trait!
:::

## Load User Meta

Loads the metadata information into the parent object so that it is available for use.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

class MyClass
{
  use HasUserMetaTrait;

  /**
   * Some function
   */
  public function someFunction() {
    $this->loadUserMeta('defaultValue');
  }
}
```

## Get User Meta

Gets the currently loaded metadata beyond the current parent class instance.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

class MyClass
{
  use HasUserMetaTrait;

  /**
   * Some function
   */
  public function someFunction() {
    $this->loadUserMeta('defaultValue');
    
    echo $this->getUserMeta();
  }
}
```

## Set User Meta

Sets the given metadata in the parent class.

:::note Setting and Saving Are Different
Setting and saving data are two different operations.  If you are ready to persist the information permanently, be sure to call [save](/traits/has-user-meta#save-user-meta).
:::

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

class MyClass
{
  use HasUserMetaTrait;

  /**
   * Some function
   */
  public function someFunction() {
    $this->setUserMeta('myValue')
        ->loadUserMeta();

    // myValue
  }
}
```

## Save User Meta

Saves the metadata to persist beyond the current parent class instance.

```php
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

class MyClass
{
  use HasUserMetaTrait;

  /**
   * Some function
   */
  public function someFunction() {
    // Persisted beyond the class instance
    $this->setUserMeta('myValue')
        ->saveUserMeta()
        ->loadUserMeta();
    
    // myValue
  }
}
```