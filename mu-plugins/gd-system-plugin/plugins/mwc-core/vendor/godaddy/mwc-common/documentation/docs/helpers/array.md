---
id: array
title: Array
---

The `ArrayHelper` class provides a common set of functionality for interacting with arrays.  The functionality ensures performant and best practice approaches for some more common array manipulation use cases.  One of the goals of this helper class is to ensure common use cases are guaranteed to avoid edge case bugs, warnings, or native php issues which ultimately cause unexpected behavior or performance penalties.

To use the `ArrayHelper` class import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
```

### accessible

The `ArrayHelper::accessible` method determines if an array is accessible.  An array is accessible if it is an array or an object like array extending `ArrayAccess` and using offsets.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$accessible = ArrayHelper::accessible(['key' => 'value']);

// true

$accessible = ArrayHelper::accessible('string');

// false

$accessible = ArrayHelper::accessible(new stdClass);

// false
```

### combine

The `ArrayHelper::combine` method merges two or more arrays while handling edge cases.

It throws an exception if any of the given parameters is not an array or implements `ArrayAccess`.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$combine = ArrayHelper::combine(['test' => 20], ['test' => 1]);

// ['test' => 1]

$combine = ArrayHelper::combine(['test' => 1], ['test' => 2, 'second' => ['nested' => 3]]);

// ['test' => 2, 'second' => ['nested' => 3]]

$combine = ArrayHelper::combine(['test' => 1], ['test' => 2, 'second' => ['nested' => 3]], ['test' => 3, 'third' => ['nested' => 4]])

// ['test' => 3, 'second' => ['nested' => 3], 'third' => ['nested' => 4]]

$combine = ArrayHelper::combine(null, ['test' => 1]);

// Exception: The array provided as the original array was not accessible!
```

### combineRecursive

The `ArrayHelper::combineRecursive` method merges two or more arrays while handling edge cases.  This method differs from [combine](#combine), by merging the children of keys versus overwriting them with the new values entirely.

It throws an exception if any of the given parameters is not an array or implements `ArrayAccess`.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$combine = ArrayHelper::combineRecursive(['foo' => ['bar' => 2]], ['foo' => ['zip' => 3]]);

// ['foo' => ['bar' => 2, 'zip' => 3]]
```

### contains

The `ArrayHelper::contains` method determines if a given array contains a value in a single or multi-dimensional array.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = [
    'first'  => 'first-level',
    'another' => [
        'second' => 'second-level',
        'another' => [
            'third' => 'third-level',
        ]
    ]
];

$contains = ArrayHelper::contains($array, 'third-level');

// true

$contains = ArrayHelper::contains($array, 'missing');

// false

$contains = ArrayHelper::contains($array, 'another');

// false
```

### except

The `ArrayHelper::except` method returns an array excluding the given key or array of keys.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$original = ['test' => 1];
$except   = ArrayHelper::except($original, 'test');

// []

$original = ['test' => 2, 'second' => ['nested' => 3], 'third' => 4];
$except   = ArrayHelper::except($original, ['second', 'third']);

// ['test' => 2]
```

### exists

The `ArrayHelper::exists` method determines if an array key exists in a given array or array like object.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array  = ['key' => 'value'];
$exists = ArrayHelper::exists($array, 'key');

// true

$array  = [];
$exists = ArrayHelper::exists($array, 'key');

// false
```

### flatten

The `ArrayHelper::flatten` returns a one dimensional flattened array from a given multi-dimensional array.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = [
    'first'  => 'first-level',
    'another' => [
        'second' => 'second-level',
        'another' => [
            'third' => 'third-level',
        ]
    ]
];

$flattened = ArrayHelper::flatten($array);

// ['first-level', 'second-level', 'third-level']
```

### get

The `ArrayHelper::get` method returns a value set from a dot notated string for a given array or array like object.  The get method accepts a `default` value as an `optional` third parameter.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array  = ['key' => true];
$nested = ['key' => ['nested' => ['deeply' => true]]];

$get    = ArrayHelper::get($array, 'key');

// true

$get    = ArrayHelper::get($nested, 'key.nested.deeply');

// true

$get    = ArrayHelper::get($array, 'missing');

// null

$get    = ArrayHelper::get($array, 'missing', 25);

// 25
```

### has

The `ArrayHelper::has` method determines if an array or array like object has a given key or keys.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = ['key' => 'value'];
$has   = ArrayHelper::has($array, 'key');

// true

$array = ['key' => 'value', 'test' => 'value'];
$has   = ArrayHelper::has($array, ['key', 'test']);

// true

$array = ['key' => 'value'];
$has   = ArrayHelper::has($array, ['key', 'test']);

// false

$array = ['key' => ['nested' => ['deeply' => true]]];
$has   = ArrayHelper::has($array, ['key.nested.deeply', 'key.nested']);

// true
```

### pluck

The `ArrayHelper::pluck` returns an array of values from a given array or array like object for a provide key.  The key may be given in dot notation.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = [
    'single' => 'first',
    'double' => [
        'first' => [
            'test' => 'idk',
        ],
        'second',
        'third',
    ],
    'another' => [
        'first'     => 'value',
        'second'    => 'hidden',
    ]
];

$plucked   = ArrayHelper::pluck($array, 'second');

// ['hidden']

$plucked   = ArrayHelper::pluck($array, 'first');

// [['test' => 'idk'], 'value']

$plucked   = ArrayHelper::pluck($array, 'fail');

// []
```

### query

The `ArrayHelper::query` converts a given array into valid query string parameters.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = ['first' => 'hello', 'second' => 'my', 'third' => ['working' => 'function']];

$plucked   = ArrayHelper::query($array);

// first=hello&second=my&third[working]=function
```

### remove

The `ArrayHelper::remove` removes a given key or keys from the original array.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$simple  = ['test' => 1];
$complex = ['test' => 2, 'second' => ['nested' => 3]];
$multi   = ['test' => 2, 'second' => ['nested' => 3], 'third' => 4];

ArrayHelper::remove($simple, 'test');

// []

ArrayHelper::remove($complex, 'second');

// ['test' => 2]

ArrayHelper::remove($multi, ['second', 'third']);

// ['test' => 2]
```

### set

The `ArrayHelper::set` set a given dot notated key on the original array for an array or array like object.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = ['first' => 'value', 'hello' => ['nested' => 'value']];

ArrayHelper::set($array, 'first', 'new');

// ['first' => 'new', 'hello' => ['nested' => 'value']]

ArrayHelper::set($array, 'hello.nested', 'new');

// ['first' => 'new', 'hello' => ['nested' => 'new']]

ArrayHelper::set($array, 'hello.nothing', 'new');

// ['first' => 'new', 'hello' => ['nested' => 'new', 'nothing' => 'new']]
```

### where

The `ArrayHelper::where` returns an array where a given condition within a closure is met.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = [15, 12, 3, 9];

ArrayHelper::where($array, function ($value) {
    return $value < 5;
});

// [3]
```

### wrap

The `ArrayHelper::wrap` converts the given value to an array if it is not already an array or array like object.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

$array = ['first' => 'value', 'hello' => ['nested' => 'value']];

ArrayHelper::wrap('key');

// ['key']

ArrayHelper::wrap(['key' => 'value']);

// ['key' => 'value']

ArrayHelper::wrap((new stdClass()));

// []

ArrayHelper::wrap(null);

// []
```
