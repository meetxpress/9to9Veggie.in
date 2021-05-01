<?php

namespace GoDaddy\WordPress\MWC\Common\Cache;

use GoDaddy\WordPress\MWC\Common\Cache\Types\CacheConfigurations;
use GoDaddy\WordPress\MWC\Common\Cache\Types\CacheExtensions;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

/**
 * Main cache handler.
 *
 * @since 1.0.0
 */
class Cache
{
    /**
     * The current static cache instance.
     *
     * @NOTE: This is always checked first before checking for the persistent database cache.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * How long in seconds should the cache be kept for.
     *
     * Static caches are reset on each page change and will not have an expiry set.
     * Databases will respect the expiry.
     *
     * @var int
     */
    protected $expires;

    /** @var string the cache key */
    protected $key = 'system';

    /** @var string the cache key prefix applied to subclass keys */
    protected $keyPrefix = 'gd_';

    /** @var string the type of object we are caching */
    protected $type;

    /**
     * Creates an instance for caching configurations.
     *
     * @since 1.0.0
     *
     * @return CacheConfigurations
     */
    public static function configurations() : CacheConfigurations
    {
        return new CacheConfigurations();
    }

    /**
     * Creates an instance for caching extensions.
     *
     * @since 1.0.0
     *
     * @return CacheExtensions
     */
    public static function extensions(): CacheExtensions
    {
        return new CacheExtensions();
    }

    /**
     * Clears the current cache.
     *
     * @NOTE: The persisted stores may rely on configurations so be sure to clear them first before their dependencies
     *
     * @since 1.0.0
     *
     * @param bool $persisted
     */
    public function clear(bool $persisted = true)
    {
        if ($persisted) {
            $this->clearPersisted();
        }

        ArrayHelper::remove(self::$cache, $this->getKey());
    }

    /**
     * Clears the persisted store.
     *
     * @since 1.0.0
     */
    protected function clearPersisted()
    {
        if (WordPressRepository::hasWordPressInstance()) {
            delete_transient($this->getKey());
        }
    }

    /**
     * Sets when the cache should expire.
     *
     * @since 1.0.0
     *
     * @param int $seconds
     * @return Cache
     */
    public function expires(int $seconds) : self
    {
        $this->expires = $seconds;

        return $this;
    }

    /**
     * Gets a cached value from the static store.
     *
     * @since 1.0.0
     *
     * @param $default
     * @return mixed|null
     */
    public function get($default = null)
    {
        if (ArrayHelper::has(self::$cache, $this->getKey())) {
            return ArrayHelper::get(self::$cache, $this->getKey(), $default);
        }

        return $this->getPersisted() ?: $default;
    }

    /**
     * Gets a cached value from the persisted store.
     *
     * @since 1.0.0
     *
     * @return mixed|null
     */
    public function getPersisted()
    {
        if ($value = get_transient($this->getKey())) {
            $this->set($value, false);
        }

        return $value;
    }

    /**
     * Get the full key string.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getKey() : string
    {
        return "{$this->keyPrefix}{$this->key}";
    }

    /**
     * Sets what key the data will be stored in within the cache.
     *
     * @since 1.0.0
     *
     * @param string $key
     * @return Cache
     */
    public function key(string $key) : self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Sets a value in the cache.
     *
     * @since 1.0.0
     *
     * @param mixed $value
     * @param bool $persisted
     */
    public function set($value, bool $persisted = true)
    {
        ArrayHelper::set(self::$cache, $this->getKey(), $value);

        if ($persisted) {
            $this->setPersisted($value);
        }
    }

    /**
     * Sets a value in the persisted store.
     *
     * @since 1.0.0
     *
     * @param mixed $value
     */
    protected function setPersisted($value)
    {
        if (WordPressRepository::hasWordPressInstance()) {
            set_transient($this->getKey(), $value, $this->expires);
        }
    }

    /**
     * Sets the type of data being cached.
     *
     * @since 1.0.0
     *
     * @param string $type
     * @return Cache
     */
    public function type(string $type) : self
    {
        $this->type = $type;

        return $this;
    }
}
