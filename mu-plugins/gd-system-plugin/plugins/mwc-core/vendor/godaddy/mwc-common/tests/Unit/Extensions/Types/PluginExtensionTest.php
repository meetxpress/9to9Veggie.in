<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Extensions\Types;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionActivationFailedException;
use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionDeactivationFailedException;
use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionDownloadFailedException;
use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionInstallFailedException;
use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionUninstallFailedException;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension
 */
final class PluginExtensionTest extends WPTestCase
{
    /**
     * Runs before each test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();
    }

    /**
     * Gets a Plugin instance for testing.
     *
     * @return PluginExtension
     */
    private function getInstance() : PluginExtension
    {
        return new PluginExtension();
    }

    /**
     * Tests that can return the basename property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getBasename()
     */
    public function testCanGetBasename()
    {
        $plugin = $this->getInstance();
        $plugin->setBasename('test-basename');

        $this->assertEquals('test-basename', $plugin->getBasename());
    }

    /**
     * Tests that can return the basename property if no value has been defined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getBasename()
     */
    public function testGetBasenameWhenValueIsNull()
    {
        $this->assertNull((new PluginExtension())->getBasename());
    }

    /**
     * Tests that can return the install path property
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getInstallPath()
     * @throws ReflectionException
     */
    public function testCanGetInstallPath()
    {
        $plugin = $this->getInstance();
        $property = TestHelpers::getInaccessibleProperty($plugin, 'installPath');

        $property->setValue($plugin, 'test-path');

        $this->assertEquals('test-path', $plugin->getInstallPath());
    }

    /**
     * Tests that can return the installed version
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getInstalledVersion()
     */
    public function testCanGetInstalledVersion()
    {
        Configuration::set('wordpress.plugins_directory', '/path/to/plugins');

        WP_Mock::userFunction('get_plugin_data')->withArgs(['/path/to/plugins/test-plugin/test-plugin.php'])->andReturn(['Version' => '1.2.3']);

        $plugin = $this->getInstallablePlugin(false);

        $this->assertNull($plugin->getInstalledVersion());

        $plugin = $this->getInstallablePlugin(true);

        $this->assertEquals('1.2.3', $plugin->getInstalledVersion());
    }

    /**
     * Anonymous function to return a plugin in different installed states
     *
     * @param bool $isInstalled
     *
     * @return PluginExtension
     */
    protected function getInstallablePlugin(bool $isInstalled = false) : PluginExtension
    {
        return new class($isInstalled) extends PluginExtension {

            protected $internalIsInstalled;

            public function __construct(bool $isInstalled)
            {
                $this->basename = 'test-plugin/test-plugin.php';
                $this->internalIsInstalled = $isInstalled;

                parent::__construct();
            }

            public function isInstalled() : bool
            {
                return $this->internalIsInstalled;
            }
        };
    }

    /**
     * Tests that can return the image URLs property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getImageUrls()
     */
    public function testCanGetImageUrls()
    {
        $instance = $this->getInstance();

        $this->assertIsArray($instance->getImageUrls());

        $image_urls = [
            '1x' => 'url1',
            '2x' => 'url2',
        ];

        $instance->setImageUrls($image_urls);

        $this->assertEquals($image_urls, $instance->getImageUrls());
    }

    /**
     * Tests that can set the image URLs property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::setImageUrls()
     */
    public function testCanSetImageUrls()
    {
        $instance = $this->getInstance();

        $this->assertInstanceOf(PluginExtension::class, $instance->setImageUrls([]));
        $this->assertEmpty($instance->getImageUrls());

        $instance->setImageUrls(['test']);

        $this->assertEquals(['test'], $instance->getImageUrls());
    }

    /**
     * Tests that can get modified plugin name
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::getName()
     * @throws ReflectionException
     *
     * @dataProvider providerPluginNames
     */
    public function testCanGetPluginName(string $name, string $modifiedResult)
    {
        $plugin = $this->getInstance();
        $property = TestHelpers::getInaccessibleProperty($plugin, 'name');

        $property->setValue($plugin, $name);

        $this->assertSame($modifiedResult, $plugin->getName(), "Expected {$modifiedResult} but got {$plugin->getName()}");
    }

    /** @see testCanGetPluginName */
    public function providerPluginNames() : array
    {
        return [
            ['My Name', 'My Name'],
            ['WooCommerce My Name', 'My Name'],
            ['My Name for WooCommerce', 'My Name for WooCommerce'],
            ['WooCommerce My Name for WooCommerce', 'My Name for WooCommerce'],
        ];
    }

    /**
     * Tests that can set the basename property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::setBasename()
     */
    public function testCanSetBasename()
    {
        $this->assertInstanceOf(PluginExtension::class, $this->getInstance()->setBasename('test-basename'));
    }

    /**
     * Tests that can install the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::install()
     */
    public function testCanInstall()
    {
        // test responsibility is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that an error while installing the extension throws the correct exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::install()
     *
     * @throws ExtensionInstallFailedException
     */
    public function testInstallFailed()
    {
        WP_Mock::userFunction('download_url')->once()->andThrow(new ExtensionDownloadFailedException('Test'));

        $this->expectException(ExtensionInstallFailedException::class);

        $this->getInstance()->install();
    }

    /**
     * Tests that can determine whether the plugin is installed.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::isInstalled()
     */
    public function testCanDetermineIfInstalled()
    {
        $plugin = $this->getInstance();

        // no basename returns false
        $plugin->setBasename('');
        $this->assertFalse($plugin->isInstalled());

        // the plugin isn't installed
        $plugin->setBasename('foo/bar.php');
        $this->assertFalse($plugin->isInstalled());

        // to have a positive result a file should be placed in the plugins directory,
        // testing responsibility for this case is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that can activate the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::activate()
     */
    public function testCanActivate()
    {
        // test responsibility is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that an error while activating the extension throws the correct exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::activate()
     *
     * @throws ExtensionActivationFailedException
     */
    public function testActivationFailed()
    {
        $this->expectException(ExtensionActivationFailedException::class);

        $this->getInstance()->activate();
    }

    /**
     * Tests that can determine whether the plugin is active.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::isActive()
     *
     * @param string $basename basename to test
     * @param string[] $activePlugins list of active plugins
     * @param bool $expected outcome
     *
     * @dataProvider providerCanDetermineIfActive
     */
    public function testCanDetermineIfActive(string $basename, array $activePlugins, bool $expected)
    {
        WP_Mock::userFunction('is_plugin_active')->withArgs([$basename])->andReturn($expected);
        WP_Mock::userFunction('is_plugin_active_for_network')->withArgs([$basename])->andReturn($expected);
        WP_Mock::userFunction('is_multisite')->andReturn(false);

        $this->mockWordPressGetOption('active_plugins', $activePlugins);
        $this->mockWordPressGetOption('active_sitewide_plugins', []);

        $plugin = $this->getInstance();
        $plugin->setBasename($basename);
        $this->assertEquals($expected, $plugin->isActive());
    }

    /** @see testCanDetermineIfActive */
    public function providerCanDetermineIfActive() : array
    {
        return [
            ['', ['foo/bar.php'], false],
            ['bar/baz.php', ['foo/bar.php'], false],
            ['foo/bar.php', ['foo/bar.php'], true],
        ];
    }

    /**
     * Tests that can deactivate the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::deactivate()
     */
    public function testCanDeactivate()
    {
        // test responsibility is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that an error while deactivating the extension throws the correct exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::deactivate()
     *
     * @throws ExtensionDeactivationFailedException
     */
    public function testDeactivationFailed()
    {
        WP_Mock::userFunction('deactivate_plugins')->once();

        $extension = $this->getMockBuilder(PluginExtension::class)
                          ->onlyMethods(['getBasename', 'isActive'])
                          ->getMock();

        $extension->expects($this->once())
                  ->method('getBasename')
                  ->willReturn('');

        $extension->expects($this->once())
                  ->method('isActive')
                  ->willReturn(true);

        $this->expectException(ExtensionDeactivationFailedException::class);

        $extension->deactivate();
    }

    /**
     * Tests that can uninstall the plugin.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::uninstall()
     */
    public function testCanUninstall()
    {
        // test responsibility is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that an error while uninstalling the extension throws the correct exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension::uninstall()
     *
     * @throws ExtensionUninstallFailedException
     */
    public function testUninstallFailed()
    {
        $error = Mockery::mock('WP_Error');
        $error->shouldReceive('get_error_message')->andReturn('error');

        WP_Mock::userFunction('delete_plugins')->once()->andReturn($error);

        $extension = $this->getMockBuilder(PluginExtension::class)
                          ->onlyMethods(['getBasename', 'isActive', 'isInstalled'])
                          ->getMock();

        $extension->expects($this->once())
                  ->method('getBasename')
                  ->willReturn('');

        $extension->expects($this->once())
                  ->method('isActive')
                  ->willReturn(false);

        $extension->expects($this->any())
                  ->method('isInstalled')
                  ->willReturn(true);

        $this->expectException(ExtensionUninstallFailedException::class);

        $extension->uninstall();
    }
}
