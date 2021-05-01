---
id: is-singleton
title: IsSingleton
---

The `IsSingleton` trait declares a class to be treated as a singleton and provides the common execution for that behavior across all projects.  The class is loaded into memory and stored within a static class variable upon initial construction.

## Load

To load the parent class as a singleton you will need to use the `load()` method.

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait;

class MyClass
{
  use IsSingletonTrait;

  /**
   * Class constructor.
   */
  public function __construct() {
    // Load in-memory
  	$this->load();
  }
}
```

## Reset

To reset the singleton instance so that it is reloaded from scratch on the next instantiation, use the `reset()` method.

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait;

class MyClass
{
  use IsSingletonTrait;

  /**
   * Class constructor.
   */
  public function __construct() {
    // Load in-memory
    $this->load();

    // Clear in-memory instance
  	$this->reset();
  }
}
```
