---
id: string
title: String
---

The `StringHelper` class provides a common set of functionality for interacting with strings.  The functionality ensures performant and best practice approaches for some more common string manipulation use cases.  One of the goals of this helper class is to ensure common use cases are guaranteed to avoid edge case bugs, warnings, or native php issues which ultimately cause unexpected behavior or performance penalties.

To use the `StringHelper` class import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
```

### after

The `StringHelper::after` method returns the string following the first occurrence of a given character.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = 'hello@person@fail-';

$after  = StringHelper::after($string, '@');

// person@fail-

$after  = StringHelper::after($string, 'missing');

// 'hello@person@fail-'
```

### afterLast

The `StringHelper::afterLast` method returns the string following the last occurrence of a given character.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string    = 'hello@person@fail-';

$afterLast = StringHelper::afterLast($string, '@');

// fail-

$afterLast = StringHelper::afterLast($string, '-');

// ''

$afterLast = StringHelper::afterLast($string, 'missing');

// 'hello@person@fail-'
```

### before

The `StringHelper::before` method returns the string preceeding the first occurrence of a given character.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = 'hello@person@fail-';

$before = StringHelper::before($string, '@');

// hello

$before = StringHelper::before($string, 'h');

// ''

$before = StringHelper::before($string, 'missing');

// 'hello@person@fail-'
```

### beforeLast

The `StringHelper::beforeLast` method returns the string proceeding the last occurrence of a given character.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string     = 'hello@person@fail-';

$beforeLast = StringHelper::beforeLast($string, '@');

// hello@person

$beforeLast = StringHelper::beforeLast($string, 'h');

// ''

$beforeLast = StringHelper::beforeLast($string, '-');

// 'hello@person@fail'

$beforeLast = StringHelper::beforeLast($string, 'missing');

// 'hello@person@fail-'
```

### contains

The `StringHelper::contains` method checks if a given string contains a character/string or set of characters/strings.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = 'my crazy string may contain';

StringHelper::contains($string, null);

// false

StringHelper::contains($string, '');

// false

StringHelper::contains($string, 'contain');

// true

StringHelper::contains($string, 'false');

// false

StringHelper::contains($string, ['false', 'string']);

// true

StringHelper::contains($string, ['may', 'string']);

// true
```

### endWith

The `StringHelper::endWith` method returns a given string ending with a given character.  If the character is already present it will not be repeated.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = 'this/is/a/test';

$ending = StringHelper::end_with($string, 'test');

// this/is/a/test

$ending = StringHelper::end_with($string, '-hello');

// this/is/a/test-hello

$ending = StringHelper::end_with("{$string}    ", '/');

// 'this/is/a/test/'
```

### replaceFirst

The `StringHelper::replaceFirst` method returns the string with the given `$search` argument replaced with the given `$replace` argument.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string    = 'this {thing} needs to be replaced';

$replaced = StringHelper::replaceFirst('{thing}', 'tire', $string);

// this tire needs to be replaced
```

### sanitize

The `StringHelper::santize` method santizes a given string to make it no longer contain HTML tags.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = '<h2>sanitize me</h2>';

$sanitizedString = StringHelper::sanitize($string);

// $sanitizedString is now 'sanitize me' without any HTML tags
```

### snakeCase

The `StringHelper::snakeCase` method returns a given string in snake_case.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$snakeCase = StringHelper::snake_case('HelloMyFriend');

// hello_my_friend

$snakeCase = StringHelper::snake_case('Welcome!To#This');

// welcome_to_this

$snakeCase = StringHelper::snake_case('hi lo');

// hi_lo

$snakeCase = StringHelper::snake_case('helper_method upper');

// helper_method_upper
```

### trailingSlash

The `StringHelper::trailingSlash` method returns a given string with a trailing slash if one is not present.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$slash = StringHelper::trailing_slash('this/is/a/test');

// this/is/a/test/

$slash = StringHelper::trailing_slash('this/is/a/test/');

// this/is/a/test/

$slash = StringHelper::trailing_slash('this/is/a/test/    ');

// this/is/a/test/
```

### startsWith

The `StringHelper::startsWith` method checks if a string starts with a given string.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;

$string = 'my silly string';

$sanitizedString = StringHelper::startsWith($string, 'silly');

// false

$sanitizedString = StringHelper::startsWith($string, 'my');

// true
```
