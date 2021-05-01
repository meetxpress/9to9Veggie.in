---
id: comparison
title: Comparison
---

The `ComparisonHelper` class provides a way to compare values when the operator is unknown during the development stage. An example of that are requests with filter parameters.

Although the class presents constants to represent operators, giving the nature of this helper, variations of them may be passed to `setOperator`. Operators like `equals` and `equal` will be normalized to `eq` which is the default equals operator, and so on.

To use the `ComparisonHelper` class, import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;
```

### create

The `ComparisonHelper::create` will create a simple instance of `ComparisonHelper` and return it so it can be used along with its chaining setters to perform a comparison.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;

$comparator = ComparisonHelper::create();
```

### setCaseSensitive

Case-sensitive comparison for strings or string arrays is `true` by default, but can be turned off by calling `setCaseSensitive(false)`.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;

$comparator = ComparisonHelper::create()
    ->setCaseSensitive(false);
```

### setters (value, operator, with)

The `ComparisonHelper::set` methods are used to prepare the comparison and can be chained.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;

$comparator = ComparisonHelper::create()
    ->setValue('a')
    ->setOperator(ComparisonHelper::EQUALS)
    ->setWith('b');
```

### compare

The `ComparisonHelper::compare` method will perform the comparison by checking if the value set by `setValue` matches another value set by `setWith` using the operator set by `setOperator`.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\ComparisonHelper;

ComparisonHelper::create()
    ->setValue('a')
    ->setOperator(ComparisonHelper::EQUALS)
    ->setWith('b')
    ->compare();

// returns false

ComparisonHelper::create()
    ->setValue('a')
    ->setOperator(ComparisonHelper::EQUALS)
    ->setWith('a')
    ->compare();

// returns true

ComparisonHelper::create()
    ->setValue('a')
    ->setOperator(ComparisonHelper::IN)
    ->setWith(['a', 'b'])
    ->compare();

// returns true
```