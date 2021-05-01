---
id: object
title: Object
---

The `ObjectHelper` class provides a common set of functionality for interacting with objects.  The functionality ensures performant and best practice approaches for some more common object manipulation use cases.  One of the goals of this helper class is to ensure common use cases are guaranteed to avoid edge case bugs, warnings, or native php issues which ultimately cause unexpected behavior or performance penalties.

To use the `ObjectHelper` class import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ObjectHelper;
```
### toArray

The `ArrayHelper::toArray` method converts an object to a valid array.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$object = new stdClass;

$object->key = 'value';

$array  = ObjectHelper::toArray($object));

// ['key' => 'value']
```
