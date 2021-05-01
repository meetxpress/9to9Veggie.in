<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Configuration;

use Composer\Config;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Exception;
use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration
 */
final class ConfigurationTest extends WPTestCase
{
    use ArraySubsetAsserts;

    /** @var string base (common) configuration directory */
    protected $baseConfigDirectory;

    /** @var string test configuration directory */
    protected $configDirectory;

    /**
     * Sets up for tests.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->baseConfigDirectory = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations');
        $this->configDirectory = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/Configurations');
    }

    /**
     * Tests that can clear the Configurations cache.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::clear()
     */
    public function testCanClearConfigurationCache()
    {
        $this->mockWordPressTransients();
        Configuration::clear();

        Configuration::initialize($this->configDirectory);
        $this->assertNotEmpty(Configuration::get('test_config.name'));

        Configuration::clear();
        $this->assertEmpty(Configuration::get('test-config.name'));
    }

    /**
     * Tests that can determine if the configuration is already cached.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::isCached()
     * @throws ReflectionException
     */
    public function testCanDetermineConfigurationCached()
    {
        Configuration::clear();
        $this->assertFalse(Configuration::isCached());

        $config = new Configuration();
        $loadConfigurations = TestHelpers::getInaccessibleMethod(Configuration::class, 'load');

        $config::initialize();
        $loadConfigurations->invokeArgs($config, [$this->configDirectory]);

        $this->assertTrue($config::isCached());
    }

    /**
     * Tests that can determine if the configuration was initialized.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::isInitialized()
     */
    public function testCanDetermineConfigurationInitialized()
    {
        Configuration::clear();
        $configurationDirectories = TestHelpers::getInaccessibleProperty(Configuration::class, 'configurationDirectories');
        $configurationDirectories->setValue([]);

        $this->assertFalse(Configuration::isInitialized());

        Configuration::initialize($this->configDirectory);

        $this->assertTrue(Configuration::isInitialized());
    }

    /**
     * Tests that can load Configurations from configuration files.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::load()
     * @throws ReflectionException
     */
    public function testCanLoadConfigurations()
    {
        Configuration::clear();

        $config = new Configuration();
        $loadConfigurations = TestHelpers::getInaccessibleMethod(Configuration::class, 'load');

        $result = $loadConfigurations->invokeArgs($config, ['']);

        $this->assertEmpty($result);

        $result = $loadConfigurations->invokeArgs($config, [$this->configDirectory]);

        $this->assertNotEmpty($result);
        $this->assertEquals('value', Configuration::get('nested.another_config.name.param'));
        $this->assertEquals('value', Configuration::get('test_config.name.param'));
    }

    /**
     * Tests that can reload configurations.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::reload()
     */
    public function testCanReloadConfigurationValues()
    {
        Configuration::clear();
        Configuration::initialize($this->configDirectory);

        $original = Configuration::get('test_config.name.param');

        $this->assertEquals($original, Configuration::get('test_config.name.param'));

        Configuration::set('test_config.name.param', 'another');

        $this->assertEquals('another', Configuration::get('test_config.name.param'));

        Configuration::reload();

        $this->assertEquals($original, Configuration::get('test_config.name.param'));
    }

    /**
     * Tests that can retrieve the configuration directories.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::directories()
     */
    public function testCanRetrieveConfigurationDirectories()
    {
        Configuration::clear();
        Configuration::initialize($this->configDirectory);

        $this->assertIsArray(Configuration::directories());
        $this->assertContains($this->baseConfigDirectory, Configuration::directories());
        $this->assertContains($this->configDirectory, Configuration::directories());
    }

    /**
     * Tests that can retrieve a configuration value.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::get()
     */
    public function testCanRetrieveConfigurationValue()
    {
        Configuration::clear();
        Configuration::initialize($this->configDirectory);

        $this->assertEquals('value', Configuration::get('test_config.name.param'));
    }

    /**
     * Tests that can set a configuration value.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::set()
     */
    public function testCanSetConfigurationValue()
    {
        Configuration::clear();
        Configuration::initialize($this->configDirectory);

        Configuration::set('nested.another_config.new', 'value');
        Configuration::set('test_config.new', 'value');

        $this->assertEquals('value', Configuration::get('nested.another_config.new'));
        $this->assertEquals('value', Configuration::get('test_config.new'));
    }

    /**
     * Tests that can add the base configuration directory.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::addBaseConfigurationDirectory()
     * @throws ReflectionException
     */
    public function testCanAddBaseConfigurationDirectory()
    {
        $configuration = new Configuration();
        $configurationDirectories = TestHelpers::getInaccessibleProperty($configuration, 'configurationDirectories');
        $configurationDirectories->setValue([]);

        $addBaseConfigurationDirectory = TestHelpers::getInaccessibleMethod(Configuration::class, 'addBaseConfigurationDirectory');

        $addBaseConfigurationDirectory->invoke($configuration);
        $this->assertEquals([$this->baseConfigDirectory], $configuration::directories());
    }

    /**
     * Tests that can add configuration directories.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::addConfigurationDirectory()
     * @throws ReflectionException
     */
    public function testCanAddConfigurationDirectory()
    {
        $configuration = new Configuration();
        $configurationDirectories = TestHelpers::getInaccessibleProperty($configuration, 'configurationDirectories');
        $configurationDirectories->setValue([]);

        $addConfigurationDirectory = TestHelpers::getInaccessibleMethod(Configuration::class, 'addConfigurationDirectory');

        $addConfigurationDirectory->invokeArgs($configuration, [$this->configDirectory]);
        $this->assertEquals([$this->configDirectory], $configuration::directories());
    }

    /**
     * Tests that can merge configuration properties in the same dot notation path
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::addConfigurationDirectory()
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCanMergeConfigurationProperties()
    {
        $alternativeDirectory = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/AltConfigs');
        $configuration = new Configuration();
        $configurationDirectories = TestHelpers::getInaccessibleProperty($configuration, 'configurationDirectories');
        $configurationDirectories->setValue([$this->configDirectory, $alternativeDirectory]);

        $this->assertCount(2, array_count_values($configuration::directories()));
        $this->assertArraySubset(['name' => ['param' => 'value', 'param2' => 'value']], $configuration::get('test_config'));
    }

    /**
     * Tests that can overwrite configuration properties in the same dot notation path
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::addConfigurationDirectory()
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCanOverwriteConfigurationProperties()
    {
        $alternativeDirectory = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/AltConfigs');

        $configuration            = new Configuration();
        $configurationDirectories = TestHelpers::getInaccessibleProperty($configuration, 'configurationDirectories');

        $configurationDirectories->setValue([$this->configDirectory]);

        $this->assertArraySubset(['overwrite' => 'original-value'], $configuration::get('test_config'));

        $configurationDirectories->setValue([$this->configDirectory, $alternativeDirectory]);

        $configuration->reload();

        $this->assertCount(2, array_count_values($configuration::directories()));
        $this->assertArraySubset(['overwrite' => 'new-value'], $configuration::get('test_config'));
    }

    /**
     * Tests that can initialize.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::initialize()
     * @throws ReflectionException
     */
    public function testCanInitialize()
    {
        $configurationDirectories = TestHelpers::getInaccessibleProperty(Configuration::class, 'configurationDirectories');
        $configurationDirectories->setValue([]);

        Configuration::initialize();

        $this->assertEquals([$this->baseConfigDirectory], Configuration::directories());
    }

    /**
     * Tests that can initialize with additional directories.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::initialize()
     * @throws ReflectionException
     */
    public function testCanInitializeWithDirectories()
    {
        $configurationDirectories = TestHelpers::getInaccessibleProperty(Configuration::class, 'configurationDirectories');
        $configurationDirectories->setValue([]);

        Configuration::initialize([$this->configDirectory, '/another/directory']);

        $this->assertEquals([$this->baseConfigDirectory, $this->configDirectory, '/another/directory'], Configuration::directories());
    }

    /**
     * Tests that a configuration value can be set even if the configuration directory doesn't exists.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::set()
     * @covers \GoDaddy\WordPress\MWC\Common\Configuration\Configuration::get()
     */
    public function testCanSetConfigurationValueIfConfigurationDirDoesNotExist()
    {
        Configuration::clear();
        Configuration::initialize('/incorrect/path/to/Configurations');

        Configuration::set('name', 'value');

        $this->assertSame('value', Configuration::get('name'));
    }

    /**
     * Tests the existence of critical configurations keys.
     *
     * @dataProvider criticalConfigurationKeys
     *
     * @param string $key
     *
     * @throws Exception
     */
    public function testHasCriticalConfigurationKeys(string $key)
    {
        Configuration::clear();
        Configuration::initialize($this->configDirectory);
        WP_Mock::userFunction('get_option');

        $this->assertTrue(Configuration::hasKey($key));
    }

    public function criticalConfigurationKeys() : array
    {
        return [
            'WordPress Absolute Path' => ['wordpress.absolute_path'],
            'WordPress Plugins Path' => ['wordpress.plugins_directory'],
            'WordPress Version' => ['wordpress.version'],
            'WordPress Debug Mode' => ['wordpress.debug'],
            'WooCommerce Version' => ['woocommerce.version'],
            'MWC URL' => ['mwc.url'],
            'MWC Version' => ['mwc.version'],
            'MWC Debug Mode' => ['mwc.debug'],
            'MWC Mode' => ['mwc.mode'],
            'MWC Extensions API URL' => ['mwc.extensions.api.url'],
            'MWC Plan Name' => ['mwc.plan_name'],
            'MWP Settings File' => ['mwc.mwp_settings'],
            'MWP Events API URL' => ['mwc.events.api.url'],
            'GoDaddy Account UID' => ['godaddy.account.uid'],
            'GoDaddy Site Token' => ['godaddy.site.token'],
            'GoDaddy Site ID' => ['godaddy.site.id'],
            'GoDaddy Account Plan Name' => ['godaddy.account.plan.name'],
            'GoDaddy Reseller ID' => ['godaddy.reseller'],
            'GoDaddy Storefront API URL' => ['godaddy.storefront.api.url'],
            'GoDaddy CDN' => ['godaddy.cdn'],
            'GoDaddy Temporary Domain' => ['godaddy.temporary_domain'],
        ];
    }
}
