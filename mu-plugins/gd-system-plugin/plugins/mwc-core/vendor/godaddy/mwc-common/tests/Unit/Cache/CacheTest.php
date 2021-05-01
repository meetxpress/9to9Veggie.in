<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Cache;

use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Cache\Types\CacheConfigurations;
use GoDaddy\WordPress\MWC\Common\Cache\Types\CacheExtensions;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionClass;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache
 */
final class CacheTest extends WPTestCase
{
    /**
     * Set up for tests.
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        Configuration::initialize(StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/Configurations'));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::clear()
     */
    public function testCanClearCache()
    {
        $this->mockStaticMethod(WordPressRepository::class, 'hasWordPressInstance')
            ->andReturnTrue();

        $value = ['test' => 'value'];

        WP_Mock::userFunction('set_transient', ['times' => 1])
            ->with('gd_system', $value, 3600)
            ->andReturnTrue();

        $cache = (new Cache())->expires(3600);
        $cache->set($value);

        $this->assertNotEmpty($cache->get());

        WP_Mock::userFunction('delete_transient', ['times' => 1])->with('gd_system');

        $cache->clear();

        WP_Mock::userFunction('get_transient')->with('gd_system')->andReturnFalse();

        // get() will return the default value if there is no cached value
        $this->assertSame('default-1', $cache->get('default-1'));
        $this->assertSame('default-2', $cache->get('default-2'));
    }

    /**
     * Tests that can get a cache from static store by key.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::get()
     */
    public function testCanGetCacheByKey()
    {
        $this->mockStaticMethod(WordPressRepository::class, 'hasWordPressInstance')
            ->andReturnTrue();

        $cache = (new Cache())->expires(1800);
        $value = ['test' => 'value'];

        WP_Mock::userFunction('set_transient', ['times' => 1])
            ->with('gd_system', $value, 1800)
            ->andReturnTrue();

        $cache->set($value);

        $this->assertEquals($value, $cache->get());
    }

    /**
     * Tests that can get the full cache key.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::getKey()
     */
    public function testCanGetCacheKey()
    {
        $cache = new Cache();
        $reflection = new ReflectionClass($cache);
        $key = $reflection->getProperty('key');
        $prefix = $reflection->getProperty('keyPrefix');

        $key->setAccessible(true);
        $prefix->setAccessible(true);

        $expected = "{$prefix->getValue($cache)}{$key->getValue($cache)}";

        $this->assertEquals($expected, $cache->getKey());
    }

    /**
     * Tests that can set the cache object expiry.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::expires()
     */
    public function testCanSetCacheExpiry()
    {
        $cache = new Cache();
        $reflection = new ReflectionClass($cache);
        $property = $reflection->getProperty('expires');

        $property->setAccessible(true);

        $cache->expires(500);
        $this->assertEquals(500, $property->getValue($cache));
    }

    /**
     * Tests that can set the cache object key.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::key()
     */
    public function testCanSetCacheKey()
    {
        $cache = new Cache();
        $reflection = new ReflectionClass($cache);
        $property = $reflection->getProperty('key');

        $property->setAccessible(true);

        $cache->key('my-key');
        $this->assertEquals('my-key', $property->getValue($cache));
    }

    /**
     * Tests that can set the cache object type.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::type()
     */
    public function testCanSetCacheType()
    {
        $cache = new Cache();
        $reflection = new ReflectionClass($cache);
        $property = $reflection->getProperty('type');

        $property->setAccessible(true);

        $cache->type('plugins');
        $this->assertEquals('plugins', $property->getValue($cache));
    }

    /**
     * Tests that enqueued can initialize an instance for configurations.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::configurations()
     */
    public function testCanSetCacheToConfigurationsInstance()
    {
        $cache = Cache::configurations();

        $this->assertTrue($cache instanceof CacheConfigurations);
    }

    /**
     * Tests that it can get an instance of the extensions cache.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::extensions()
     */
    public function testCanGetExtensionsCache()
    {
        $cache = Cache::extensions();

        $this->assertTrue($cache instanceof CacheExtensions);
    }

    /**
     * Tests that can set a cache value.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Cache\Cache::set()
     */
    public function testCanSetCacheValue()
    {
        $cache = new Cache();
        $value = ['test' => 'value'];

        $cache->set($value);

        $this->assertEquals($value, $cache->get());
    }
}
