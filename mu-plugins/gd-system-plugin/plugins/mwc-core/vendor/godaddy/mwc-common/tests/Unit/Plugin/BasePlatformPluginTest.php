<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Plugin;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\Unit\Plugin\Dummy\DummyAnyModeClass;
use GoDaddy\WordPress\MWC\Common\Tests\Unit\Plugin\Dummy\DummyCliModeClass;
use GoDaddy\WordPress\MWC\Common\Tests\Unit\Plugin\Dummy\DummyWebModeClass;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionClass;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin
 */
final class BasePlatformPluginTest extends WPTestCase
{
    /** @var ReflectionClass instance */
    protected $pluginReflection;

    /** @var BasePlatformPlugin instance */
    protected $plugin;

    /**
     * Sets up the tests.
     */
    public function setUp() : void
    {
        parent::setUp();

        WP_Mock::userFunction('get_transient', [
            'return' => ['wordpress.absolute_path' => 'foo/bar'],
        ]);

        WP_Mock::userFunction('set_transient');

        Configuration::set('wordpress.absolute_path', 'foo/bar');
    }

    /**
     * Tests that can get the classes to instantiate.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::getClassesToInstantiate()
     * @throws ReflectionException
     */
    public function testCanGetPlatformBasePluginClassesToInstantiate()
    {
        $plugin = new BasePlatformPlugin();
        $method = TestHelpers::getInaccessibleMethod(BasePlatformPlugin::class, 'getClassesToInstantiate');
        $property = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'classesToInstantiate');

        $property->setValue($plugin, ['test' => 'test']);
        $this->assertEquals(['test' => 'test'], $method->invoke($plugin));
    }

    /**
     * Tests that can get configuration values.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::getConfigurationValues()
     * @throws ReflectionException
     */
    public function testCanGetPlatformBasePluginConfigurationValues()
    {
        $plugin = new BasePlatformPlugin();
        $method = TestHelpers::getInaccessibleMethod(BasePlatformPlugin::class, 'getConfigurationValues');
        $property = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'configurationValues');

        $property->setValue($plugin, ['test' => 'test']);
        $this->assertEquals(['test' => 'test'], $method->invoke($plugin));
    }

    /**
     * Tests that can get configuration directories.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::getConfigurationDirectories()
     * @throws ReflectionException
     */
    public function testCanGetPlatformBasePluginConfigurationDirectories()
    {
        $plugin = new BasePlatformPlugin();
        $method = TestHelpers::getInaccessibleMethod(BasePlatformPlugin::class, 'getConfigurationDirectories');
        $property = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'configurationDirectories');
        $baseDirectory = StringHelper::before(__DIR__, 'tests');

        $property->setValue($plugin, ['test', 'test2']);

        $this->assertEquals([
            StringHelper::trailingSlash("{$baseDirectory}test"),
            StringHelper::trailingSlash("{$baseDirectory}test2"),
        ], $method->invoke($plugin));
    }

    /**
     * Tests that can get plugin prefix.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::getPluginPrefix()
     * @throws ReflectionException
     */
    public function testCanGetPlatformBasePluginPluginPrefix()
    {
        $plugin = new BasePlatformPlugin();
        $method = TestHelpers::getInaccessibleMethod(BasePlatformPlugin::class, 'getPluginPrefix');
        $property = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'name');

        $property->setValue($plugin, 'tesT_name');
        $this->assertEquals('TEST_NAME', $method->invoke($plugin));

        Configuration::set('wordpress.absolute_path', 'foo/bar');
        $property->setValue($plugin, null);
        $this->assertEquals('BAR', $method->invoke($plugin));
    }

    /**
     * Tests that can instantiate configuration values.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::instantiateConfigurationValues()
     * @throws ReflectionException
     */
    public function testCanPlatformBasePluginInstantiateConfigurationValues()
    {
        $plugin = new BasePlatformPlugin();
        $method = TestHelpers::getInaccessibleMethod(BasePlatformPlugin::class, 'instantiateConfigurationValues');
        $property = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'name');
        $values = TestHelpers::getInaccessibleProperty(BasePlatformPlugin::class, 'configurationValues');

        $property->setValue($plugin, 'prefix');
        $values->setValue($plugin, ['KEY' => 'value']);
        $method->invoke($plugin);

        $this->assertTrue(defined('PREFIX_KEY'));
        $this->assertEquals('value', PREFIX_KEY);
    }

    /**
     * Tests than can instantiate plugin classes.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin::instantiatePluginClasses()
     * @throws \Exception
     */
    public function testCanInstantiatePluginClasses()
    {
        $plugin = $this->getTestPluginWithClasses();

        Configuration::reload();
        Configuration::set('mwc.mode', 'cli');

        $method = TestHelpers::getInaccessibleMethod(get_class($plugin), 'instantiatePluginClasses');

        $method->invoke($plugin);

        $this->assertEquals(1, DummyCliModeClass::$loadCount, 'CLI Class instantiated');
        $this->assertEquals(0, DummyWebModeClass::$loadCount, 'Web Class NOT instantiated');
        $this->assertEquals(1, DummyAnyModeClass::$loadCount, 'Any Class instantiated');

        // reset loading statue
        DummyAnyModeClass::$loadCount = DummyCliModeClass::$loadCount = DummyWebModeClass::$loadCount = 0;

        Configuration::reload();
        Configuration::set('mwc.mode', 'web');

        $method->invoke($plugin);

        $this->assertEquals(0, DummyCliModeClass::$loadCount, 'CLI Class NOT instantiated');
        $this->assertEquals(1, DummyWebModeClass::$loadCount, 'Web Class instantiated');
        $this->assertEquals(1, DummyAnyModeClass::$loadCount, 'Any Class instantiated');
    }

    protected function getTestPluginWithClasses() : BasePlatformPlugin
    {
        return new class extends BasePlatformPlugin {
            protected $classesToInstantiate = [
                DummyAnyModeClass::class => true,
                DummyWebModeClass::class => 'web',
                DummyCliModeClass::class => 'cli',
            ];

            public function __construct()
            {
                // @NOTE: Load configurations so that they are cached - Should always be called first
                $this->initializeConfiguration();

                WordPressRepository::requireWordPressInstance();

                // @NOTE: Make sure all PHP constants are set
                $this->instantiateConfigurationValues();
            }
        };
    }

    protected function getTestPlugin() : BasePlatformPlugin
    {
        return new class extends BasePlatformPlugin {
            protected $configurationDirectories = ['configs1', 'configs2'];
        };
    }
}
