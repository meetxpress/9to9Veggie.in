---
id: deprecation
title: Deprecation
---

The `DeprecationHelper` class provides helper methods to handle code deprecations.

If debug mode is enabled, `E_USER_DEPRECATED` errors will be output; in any case, they will be logged. 

To use the `DeprecationHelper` class import it into the class or file it is intended to be used in via a typical import statement:

```php
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;
```

### deprecatedClass

Flags a class as deprecated. 

```php
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;

// you can indicate a replacement class as substitution (optional)
DeprecationHelper::deprecatedClass(__CLASS__, '1.2.3', MyReplacementClass::class);
```

### deprecatedFunction

Flags a function or method as deprecated.

```php
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;

// you can indicate a replacement function or method as substitution (optional)
DeprecationHelper::deprecatedFunction(__METHOD__, '1.2.3', MyReplacementClass::class.'::method()');
```