---
id: can-convert-to-array-trait
title: CanConvertToArrayTrait
---

The `CanConvertToArrayTrait` trait allows an implementing class/object to convert the current state of its properties into an array.

## Methods

### toArray

Loops through all known properties which are set to accessible and includes them in a returned array.

```php
use GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait;

class MyClass
{
  use CanConvertToArrayTrait;

  public $name = 'MyProperty';
}

$object = new MyClass();

var_dump($object->toArray());

// ['name' => 'MyProperty']
```

## Determining What to Include

The trait allows you to override the designated property visibility types to include by setting class properties.

```php
use GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait;

class MyClass
{
  use CanConvertToArrayTrait;

    /** @var bool Convert Private Properties to Array Output */
    protected $toArrayIncludePrivate = false;

    /** @var bool Convert Protected Properties to Array Output */
    protected $toArrayIncludeProtected = true;

    /** @var bool Convert Public Properties to Array Output */
    protected $toArrayIncludePublic = true;
}
```
