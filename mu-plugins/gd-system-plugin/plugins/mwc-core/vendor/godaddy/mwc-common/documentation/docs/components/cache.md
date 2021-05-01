---
id: cache
title: Cache
---

The `Cache` component provides a standardized, agnostic, performant, and flexible way to cache information in projects.  It offers the ability to cache items both temporarily for the scope of a given operation or in a persisted manner.

The component abstracts away the concern for an engineer to remember where every item is cached.  You are able to simple ask for a given cache or item and set it without the need to know the inner workings of where that is taking place.

## Base Class
The base class should generally not be used to cache items, but is available for the edge cases where it may make sense.  Generally speaking the base cache class offer a common entry point into all caching with a shared interface for each [cache type](/components/cache#available-types) to inherit or overwrite.

### Clearing the Cache
When clearing a given cache, you have the option of clearing only the temporary cache stored in memory or the underlying persisted caching mechanism for that cache type.  There are times when you may not want to persist cache values such as in loops, during a live customer preview, or if you are giving live feedback to a customer whom later wishes to cancel the item.  In these types of circumstances you may want to clear only the in-memory temporary cache by setting the persisted parameter to false.  Otherwise clearing the cache will clear the permanent persisted cache as well as if it has expired.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

// Clears both the in-memory and persisted cache
Cache::clear();

// You may also clear only the persisted
Cache::clearPersisted();

// Clears only the in-memory cache
Cache::clear(false);
```

### Set the Expiry
Cache [available types](/components/cache#available-types) generally set a default expiry for their type.  You may however override that default by explicitly setting the expiry for a set of information.  All expiries are set in seconds.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

// Sets the cache to expire in 60 seconds for the current instance
Cache::expires(60);
```

### Set The Cache Key
The cache base class and [available types](/components/cache#available-types) have a cache key provided by default.  If you wish to specify a key for a specific instance of a given cache you may override the default key or key prefix.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

/**
 * The cache key prefix applied to subclass keys
 *
 * @var string
 */
protected $key_prefix = 'gd_';

// Sets the cache instance key to store the values under
Cache::key('new-key');

// Returns the current instance cache key including the prefix -- gd_new-key
Cache::getKey();
```

### Get Value
You may retrieve the cache value of either the in-memory or persisted cache specifically without needing to provide the cache location or key.  The returned value will default to the in-memory value if it exists to protect the persisted stores from being called multiple times in high frequency functions like loops.  If no in-memory value is set then the persisted value will be retrieved and store in-memory.

You may also provide a default value should there be no in-memory or persisted value available.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

// Returns the cache values
Cache::get();

// Returns the given value when the cache is not found
Cache::get('no-value')

// Force retrieval of the persisted value
Cache::getPersisted();
```

### Set Value
You may set the cache value of either the in-memory or persisted cache specifically without needing to provide the cache location or key.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

// Sets the in-memory and persisted values
Cache::set('some-value');

// Sets only the in-memory value
Cache::get(['key' => 'value'], false);

// Set only the persisted value
Cache::setPersisted('persisted-only');
```

## Available Types
:::note IDE Help
IDEs with Intellisense will provide the available Cache types without checking this documentation by starting the static declaration `Cache::` providing a seamless dev experience and abstracting out the requirement of knowledge of individual class type operations.
:::

| Type           	| Key            	| Expiry 	|
|----------------	|----------------	|--------	|
| Configurations 	| configurations 	| 3600   	|
| Extensions       	| extensions       	| 900    	|

### Creating a Type
While the general cache base operations provide use case coverage for most situations, individual cache types may need to customize behavior for simple items like the cache key and expiry to more advanced behavior like where to save the persisted cache.  Types should inherit the base class and overwrite those items which are specific to the individual type by extending the base [Cache](/components/cache#base-class) class.

In addition, all types should be an implementation of the CacheableContract to ensure the expected functionality is present.

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Cache\Contracts\CacheableContract;

final class Cache{MyCacheType} extends Cache implements CacheableContract
{
  //
}
```

### Static Instantiation
The Cache component is a **action class** meaning we typically perform the same end actions when an instance is present regardless of the state held within the class itself.  This is as opposed to a Data class which is very specific to each own instance and each use case influences how it will be used.  As a result, we strive to provide a common entry point and interface for the Cache component such that engineers do not need to keep up with the specifics of every type given the specifics of that type are not relevant to usage.

This means you will see types statically instantiated from the base class entry point:

```php
use GoDaddy\WordPress\MWC\Common\Cache\Cache;

// Instantiate an instance of the configurations cache
Cache::configurations();

// Instantiate an instance of the extensions cache
Cache::extensions();
```
