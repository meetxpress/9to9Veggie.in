<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories;

use ErrorException;
use Exception;
use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository
 */
final class ManagedExtensionsRepositoryTest extends WPTestCase
{
    /**
     * Runs before each test.
     */
    public function setUp(): void
    {
        parent::setUp();

        WP_Mock::userFunction('get_transient')->with('gd_extensions')->andReturnFalse();

        Cache::extensions()->clear();

        // ensure deprecated notices are thrown
        Configuration::set('mwc.debug', true);
    }

    /**
     * Tests that it can get the Managed Extensions.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedExtensions()
     * @throws Exception
     */
    public function testCanGetManagedExtensions()
    {
        $this->mockWooCommerceExtensionsRequestFunctions();
        $this->mockSkyVergeExtensionsRequestFunctions();

        $extensions = ManagedExtensionsRepository::getManagedExtensions();

        $this->assertIsArray($extensions);
        $this->assertCount(2, $extensions);
    }

    /**
     * Tests that it can Get the Managed Plugins.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedPlugins()
     * @throws Exception
     */
    public function testCanGetManagedPlugins()
    {
        $this->mockWooCommerceExtensionsRequestFunctions();
        $this->mockSkyVergeExtensionsRequestFunctions();

        $managedPlugins = ManagedExtensionsRepository::getManagedPlugins();

        $this->assertIsArray($managedPlugins);
        $this->assertContainsOnlyInstancesOf(PluginExtension::class, $managedPlugins);
    }

    /**
     * Tests that it can Get the Installed Managed Plugins.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getInstalledManagedPlugins()
     * @throws Exception
     */
    public function testCanGetInstalledManagedPlugins()
    {
        $this->mockWooCommerceExtensionsRequestFunctions();
        $this->mockSkyVergeExtensionsRequestFunctions();
        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        WP_Mock::userFunction('get_plugins')
            ->andReturn(['test-plugin/test-plugin.php' => ['name' => 'Test Plugin']]);

        $baseNames = [];

        foreach(ManagedExtensionsRepository::getInstalledManagedPlugins() as $plugin) {
            $baseNames[] = $plugin->getBasename();
        }

        $this->assertEquals(['test-plugin/test-plugin.php'], $baseNames);
    }

    /**
     * Tests that it can Get the Installed Managed Themes.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getInstalledManagedThemes()
     * @throws Exception
     */
    public function testCanGetInstalledManagedThemes()
    {
        $this->mockWooCommerceExtensionsRequestFunctions();
        $this->mockSkyVergeExtensionsRequestFunctions();
        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        WP_Mock::userFunction('wp_get_themes')
            ->andReturn(['test-theme' => ['name' => 'Test Theme']]);

        $baseNames = [];

        foreach(ManagedExtensionsRepository::getInstalledManagedThemes() as $theme) {
            $baseNames[] = $theme->getSlug();
        }

        $this->assertEquals(['test-theme'], $baseNames);
    }

    /**
     * Mocks WordPress request functions to return SkyVerge extensions data.
     */
    protected function mockSkyVergeExtensionsRequestFunctions()
    {
        Configuration::initialize(StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/Configurations'));
        Configuration::set('mwc.extensions.api.url', 'https://example.org/skyverge/v1/');

        $this->mockWordPressRequestFunctionsWithArgs([
            'url'      => 'https://example.org/skyverge/v1/extensions/',
            'response' => [
                'code' => 200,
                'body' => [
                    'data' => [
                        [
                            'extensionId'      => '1001',
                            'slug'             => 'test-plugin',
                            'label'            => 'Test Plugin',
                            'shortDescription' => 'Test Plugin description',
                            'type'             => 'PLUGIN',
                            'category'         => null,
                            'version'          => [
                                'version'                   => '1.2.3',
                                'minimumPhpVersion'         => '7.0',
                                'minimumWordPressVersion'   => '5.2',
                                'minimumWooCommerceVersion' => '3.5',
                                'releasedAt'                => '2021-01-09T00:13:01.000000Z',
                                'links'                     => [
                                    'package' => [
                                        'href' => 'https://example.org/1001/package',
                                    ],
                                ],
                            ],
                            'links'             => [
                                'homepage'      => [
                                    'href' => 'https://example.org/1001/homepage',
                                ],
                                'documentation' => [
                                    'href' => 'https://example.org/1001/documentation',
                                ],
                            ],
                        ],
                        [
                            'extensionId'      => '1002',
                            'slug'             => 'test-theme',
                            'label'            => 'Test Theme',
                            'shortDescription' => 'Test Theme description',
                            'type'             => 'THEME',
                            'category'         => null,
                            'version'          => [
                                'version'                   => '1.4.3',
                                'minimumPhpVersion'         => '7.0',
                                'minimumWordPressVersion'   => '5.2',
                                'minimumWooCommerceVersion' => '3.5',
                                'releasedAt'                => '2021-01-09T00:13:01.000000Z',
                                'links'                     => [
                                    'package' => [
                                        'href' => 'https://example.org/1002/package',
                                    ],
                                ],
                            ],
                            'links'             => [
                                'homepage'      => [
                                    'href' => 'https://example.org/1002/homepage',
                                ],
                                'documentation' => [
                                    'href' => 'https://example.org/1002/documentation',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Tests that it can build managed SkyVerge extensions.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::buildManagedSkyVergeExtension()
     * @param string $class expected type for the generated extension
     * @param string $type  value for the type data entry
     * @dataProvider provideBuildManagedSkyVergeExtensionsData
     * @throws ReflectionException
     */
    public function testCanBuildManagedSkyVergeExtensions(string $class, string $type)
    {
        $repository = new ManagedExtensionsRepository();

        // TODO: find out why we need to handle deprecations differently {WV 2021-03-29}
        if (version_compare(PHP_VERSION, '8.0', '>')) {
            $this->expectDeprecation();
        } else {
            $this->expectException(ErrorException::class);
            $this->expectErrorMessageMatches('/is deprecated since version/');
        }

        $extension = TestHelpers::getInaccessibleMethod($repository, 'buildManagedSkyVergeExtension')
            ->invoke($repository, ['type' => $type]);

        $this->assertInstanceOf($class, $extension);
    }

    /** @see testCanBuildManagedSkyVergeExtensions() */
    public function provideBuildManagedSkyVergeExtensionsData() : array
    {
        return [
            [ThemeExtension::class, ThemeExtension::TYPE],
            [PluginExtension::class, PluginExtension::TYPE],
        ];
    }

    /**
     * Tests that it can get managed extensions from the cache.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedExtensionsFromCache()
     * @throws Exception
     */
    public function testCanGetManagedExtensionsFromCache()
    {
        $value = [new PluginExtension()];

        Cache::extensions()->set(['key' => $value]);

        $method = TestHelpers::getInaccessibleMethod(ManagedExtensionsRepository::class, 'getManagedExtensionsFromCache');
        $method->setAccessible(true);

        $extensions = $method->invoke(null, 'key', function () {
            return [];
        });

        $this->assertSame($value, $extensions);
    }

    /**
     * Tests that when it gets managed extensions from the cache, it updates the cache.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedExtensionsFromCache()
     * @throws Exception
     */
    public function testGetManagedExtensionsFromCacheUpdatesCache()
    {
        Cache::extensions()->clear();

        $method = TestHelpers::getInaccessibleMethod(ManagedExtensionsRepository::class, 'getManagedExtensionsFromCache');
        $method->setAccessible(true);

        $extensions = $method->invoke(null, 'key', function () {
            return [new PluginExtension()];
        });

        $this->assertSame(['key'=> $extensions], Cache::extensions()->get());
    }

    /**
     * Tests that can get the managed themes.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedThemes()
     * @throws Exception
     */
    public function testCanGetManagedThemes()
    {
        $this->mockWooCommerceExtensionsRequestFunctions();
        $this->mockSkyVergeExtensionsRequestFunctions();

        $managedThemes = ManagedExtensionsRepository::getManagedThemes();

        $this->assertIsArray($managedThemes);
        $this->assertContainsOnlyInstancesOf(ThemeExtension::class, $managedThemes);
    }

    /**
     * Tests that extensions are merged into the cache as expected.
     *
     * @throws ReflectionException
     */
    public function testCanMergeExtensionsIntoCacheFromMultipleSources()
    {
        $repository = new ManagedExtensionsRepository();
        $method = TestHelpers::getInaccessibleMethod($repository, 'getManagedExtensionsFromCache');

        $method->invoke($repository, 'woocommerce', function () {
            return ['test' => 'values'];
        });

        $this->assertEquals(['woocommerce' => ['test' => 'values']], Cache::extensions()->get([]));

        // Make sure that if the API doesn't respond, the customer doesn't lose the ability to get the information for the duration of the cache
        $method->invoke($repository, 'skyverge', function () {
            return [];
        });

        $method->invoke($repository, 'skyverge', function () {
            return ['test' => 'values'];
        });

        $this->assertEquals(['skyverge' => ['test' => 'values'], 'woocommerce' => ['test' => 'values']], Cache::extensions()->get([]));
    }

    /**
     * Tests that it can get available versions for a given extension.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository::getManagedExtensionVersions()
     */
    public function testCanGetManagedExtensionVersions()
    {
        $this->mockExtensionVersionsRequestFunctions();

        $plugin = new PluginExtension();

        $plugin->setId(1);

        $versions = ManagedExtensionsRepository::getManagedExtensionVersions($plugin);

        $this->assertCount(2, $versions);
        $this->assertContainsOnlyInstancesOf(PluginExtension::class, $versions);

        $this->assertSame($plugin->getId(), $versions[0]->getId());
        $this->assertSame($plugin->getId(), $versions[1]->getId());

        $this->assertTrue(version_compare($versions[0]->getVersion(), $versions[1]->getVersion(), '<'));
    }

    /**
     * Mocks WordPress request functions to return WooCommerce extensions data.
     */
    protected function mockWooCommerceExtensionsRequestFunctions()
    {
        Configuration::initialize(StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests/Configurations'));
        Configuration::set('godaddy.extensions.api.url', 'https://example.org/woocommerce/v1/extensions');

        $this->mockWordPressRequestFunctionsWithArgs([
            'url'      => 'https://example.org/woocommerce/v1/extensions',
            'response' => [
                'code' => 200,
                'body' => [
                    'name'        => 'godaddy_ecommerce',
                    'description' => 'GoDaddy eCommerce Plan',
                    'products'    => [
                        [
                            'download_link'         => 'https://example.org/first/package',
                            'homepage'              => 'https://example.org/first/homepage',
                            'icons'                 => [
                                '1x' => 'https://example.org/icons-128x128.png',
                                '2x' => 'https://example.org/icons-256x256.png',
                            ],
                            'last_updated'          => '2020-11-23', // 1606089600
                            'name'                  => 'First Plugin',
                            'short_description'     => 'First Plugin description',
                            'slug'                  => 'first-plugin',
                            'support_documentation' => 'https://example.org/first/documentation',
                            'type'                  => PluginExtension::TYPE,
                            'version'               => '1.2.3',
                        ],
                        [
                            'download_link'         => 'https://example.org/second/package',
                            'homepage'              => 'https://example.org/second/homepage',
                            'icons'                 => [
                                '1x' => 'https://example.org/icons-128x128.png',
                                '2x' => 'https://example.org/icons-256x256.png',
                            ],
                            'last_updated'          => '2020-10-16',
                            'name'                  => 'Second Plugin',
                            'short_description'     => 'Second Plugin description',
                            'slug'                  => 'second-plugin',
                            'support_documentation' => 'https://example.org/second/documentation',
                            'type'                  => PluginExtension::TYPE,
                            'version'               => '1.4.3',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Mock WordPress request functions to return SkyVerge extensions version data.
     */
    protected function mockExtensionVersionsRequestFunctions()
    {
        Configuration::set('mwc.extensions.api.url', 'https://api.mwc.secureserver.net/v1/');

        $this->mockWordPressRequestFunctionsWithArgs([
            'url' => 'https://api.mwc.secureserver.net/v1/extensions/1/versions',
            'response' => [
                'code' => 200,
                'body' => [
                    'data' => [
                        [
                            'extensionVersionId' => 12,
                            'version' => '3.10.1',
                            'minimumPhpVersion' => '7.0',
                            'minimumWordPressVersion' => '5.2',
                            'minimumWooCommerceVersion' => '3.5',
                            'releasedAt' => '2021-02-14T21:22:34.000000Z',
                            'links' => [
                                'self' => [
                                    'href' => 'https://api.mwc.secureserver.net/v1/extensions/1/versions/3.10.1',
                                ],
                                'package' => [
                                    'href' => 'https://api.mwc.secureserver.net/v1/extensions/1/versions/3.10.1/package',
                                ],
                            ],
                        ],
                        [
                            'extensionVersionId' => 13,
                            'version' => '10.0.1',
                            'minimumPhpVersion' => '7.0',
                            'minimumWordPressVersion' => '5.2',
                            'minimumWooCommerceVersion' => '3.5',
                            'releasedAt' => '2021-02-14T22:22:34.000000Z',
                            'links' => [
                                'self' => [
                                    'href' => 'https://api.mwc.secureserver.net/v1/extensions/1/versions/10.10.1',
                                ],
                                'package' => [
                                    'href' => 'https://api.mwc.secureserver.net/v1/extensions/1/versions/10.10.1/package',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
