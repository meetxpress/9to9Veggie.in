---
id: can-bulk-assign-properties
title: CanBulkAssignProperties
---

The `CanBulkAssignProperties` trait provides common functionality to set the properties of an object from an array of data.

## Methods

### setProperties

Loops though the list of properties of the class that have a setter method defined and calls that setter if the given array of data includes a value for that property.

The name of setter methods should match the `set<NameOfProperty>` pattern in order to be detected by `setProperties()`. Properties that have no setter method are not updated even if the data array includes a matching entry.

`setProperties()` is useful to quickly update an object using the data provided by an adapter.

```php
use GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait;

class MyClass
{
  use CanBulkAssignPropertiesTrait;

  private $propertyWithsetter;

  public function setPropertyWithSetter($value) {
    $this->properWithSetter = $value;

    return $this;
  }
}

$object = new MyClass();

$object->setProperties(['propertyWithSetter' => 'some-value']);
```
