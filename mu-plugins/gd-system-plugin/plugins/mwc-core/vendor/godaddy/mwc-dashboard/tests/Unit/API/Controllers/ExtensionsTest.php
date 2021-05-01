<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use GoDaddy\WordPress\MWC\Common\Cache\Cache;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController;
use Mockery;
use Patchwork;
use ReflectionException;
use WP_Mock;
use WP_REST_Request;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController
 */
final class ExtensionsTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new ExtensionsController();
        $routeProperty = TestHelpers::getInaccessibleProperty($controller, 'route');

        $this->assertSame('extensions', $routeProperty->getValue($controller));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::registerRoutes()
     */
    public function testCanRegisterRoutes()
    {
        WP_Mock::userFunction('__');

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
               ->with('godaddy/mwc/v1', '/extensions', Mockery::any());

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
               ->with('godaddy/mwc/v1', '/extensions/(?P<slug>[a-zA-Z0-9_-]+)', Mockery::any());

        (new ExtensionsController())->registerRoutes();

        $this->assertConditionsMet();
    }

    /**
     * Tests the ExtensionsController::getItems() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::getItems()
     *
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension[] $managedExtensions
     * @param string $query
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension[] $expectedExtensions
     *
     * @throws \Exception
     * @dataProvider dataProviderGetItems
     */
    public function testCanGetItems(array $managedExtensions, string $query, array $expectedExtensions)
    {
        $this->mockWordPressTransients();

        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        // causes brand to be woo for all extensions
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedSkyVergeExtensions')->andReturn([]);

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedExtensions')
           ->andReturn($managedExtensions);

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedExtensionVersions')
           ->andReturn([]);

        $controller = new ExtensionsController();
        $prepareMethod = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'prepareItem');

        $includingVersions = '{"includes": "versions"}' === $query;

        $expectedData = array_map(
            function ($extension) use ($controller, $prepareMethod, $includingVersions) {
                return $prepareMethod->invokeArgs($controller, [$extension, $includingVersions]);
            },
            $expectedExtensions
        );

        $expectedResponse = [
            'data' => $expectedData,
            'count' => count($expectedData),
        ];

        WP_Mock::userFunction('rest_ensure_response')
               ->withArgs([$expectedResponse])
               ->andReturn((object) $expectedResponse);

        // mock the request parameters
        $request = Mockery::mock(WP_REST_Request::class);
        $request->shouldReceive('get_param')->withArgs(['query'])->andReturn($query);

        $this->assertEquals((object) $expectedResponse, $controller->getItems($request));
    }

    /**
     * @see testCanGetItems
     *
     * @return array[]
     */
    public function dataProviderGetItems() : array
    {
        WP_Mock::userFunction('get_transient')->andReturn(null);

        $mockPluginExtension = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods(['isActive'])
                                    ->getMock();
        $mockPluginExtension->expects($this->any())->method('isActive')->willReturn(true);
        $mockPluginExtension
            ->setId('1')
            ->setName('Mock Plugin')
            ->setSlug('mock-plugin')
            ->setDocumentationUrl('https://mock-plugin.com');

        $mockThemeExtension = $this->getMockBuilder(ThemeExtension::class)
                                    ->onlyMethods(['isActive'])
                                    ->getMock();
        $mockThemeExtension->expects($this->any())->method('isActive')->willReturn(true);
        $mockThemeExtension
            ->setId('2')
            ->setName('Mock Theme')
            ->setSlug('mock-theme')
            ->setDocumentationUrl('https://mock-theme.com');

        $pluginFilter = '{"filters": {"type": {"eq": "plugin"}}}';
        $mixedFilter = '{"filters": {"type": {"eq": "theme"}, "id": {"eq": "2"}}}';

        $includeVersions = '{"includes": "versions"}';

        return [
            [[], '', []],
            [[$mockPluginExtension], '', [$mockPluginExtension]],
            [[$mockThemeExtension], '', [$mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], '', [$mockPluginExtension, $mockThemeExtension]],
            // with filters
            [[], $pluginFilter, []],
            [[$mockPluginExtension], $pluginFilter, [$mockPluginExtension]],
            [[$mockThemeExtension], $pluginFilter, []],
            [[$mockPluginExtension, $mockThemeExtension], $pluginFilter, [$mockPluginExtension]],
            [[], $mixedFilter, []],
            [[$mockPluginExtension], $mixedFilter, []],
            [[$mockThemeExtension], $mixedFilter, [$mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $mixedFilter, [$mockThemeExtension]],
            // including versions
            [[], $includeVersions, []],
            [[$mockPluginExtension], $includeVersions, [$mockPluginExtension]],
        ];
    }

    /**
     * Tests the ExtensionsController::filterExtensions() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::filterExtensions()
     *
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension[] $allExtensions
     * @param array $filters
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension[] $expectedExtensions
     *
     * @throws \Exception
     * @dataProvider dataProviderFilterExtensions
     */
    public function testCanFilterExtensions(array $allExtensions, array $filters, array $expectedExtensions)
    {
        // mark mock-plugin as featured
        Configuration::set('mwc_extensions.featured', ['mock-plugin' => true]);

        $method = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'filterExtensions');

        $this->assertEquals($expectedExtensions, $method->invokeArgs((new ExtensionsController()), [$allExtensions, $filters]));
    }

    /**
     * @see testCanFilterExtensions
     *
     * @return array[]
     */
    public function dataProviderFilterExtensions() : array
    {
        $mockPluginExtension = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods([])
                                    ->getMock();
        $mockPluginExtension
            ->setId('1')
            ->setName('Mock Plugin')
            ->setSlug('mock-plugin')
            ->setDocumentationUrl('https://mock-plugin.com');

        $mockThemeExtension = $this->getMockBuilder(ThemeExtension::class)
                                    ->onlyMethods([])
                                    ->getMock();
        $mockThemeExtension
            ->setId('2')
            ->setName('Mock Theme')
            ->setSlug('mock-theme')
            ->setDocumentationUrl('https://mock-theme.com');

        $pluginFilter = [
            'type' => [
                'eq' => 'plugin',
            ],
        ];

        $themeFilter = [
            'type' => [
                'eq' => 'theme',
            ],
        ];

        $slugFilter = [
            'slug' => [
                'eq' => 'mock-theme',
            ],
        ];

        $featuredFilter = [
            'featured' => [
                'eq' => true,
            ],
        ];

        $mixedFilter = array_merge($themeFilter, $slugFilter);

        return [
            [[], [], []],
            [[$mockPluginExtension, $mockThemeExtension], [], [$mockPluginExtension, $mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $pluginFilter, [$mockPluginExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $themeFilter, [$mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $slugFilter, [$mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $mixedFilter, [$mockThemeExtension]],
            [[$mockPluginExtension, $mockThemeExtension], $featuredFilter, [$mockPluginExtension]],
        ];
    }

    /**
     * Tests the ExtensionsController::prepareItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::prepareItem()
     *
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension $extension
     * @param \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension[] $versions
     * @param bool $includeVersions
     * @param array $expectedData
     *
     * @throws \Exception
     * @dataProvider dataProviderPrepareItem
     */
    public function testCanPrepareItem(AbstractExtension $extension, array $versions, bool $includeVersions, array $expectedData)
    {
        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        // causes brand to be woo for all extensions
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedSkyVergeExtensions')->andReturn([]);

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedExtensionVersions')
             ->withArgs([$extension])
             ->andReturn($versions);

        $method = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'prepareItem');

        // ignore the value in the version state property: we will test getExtensionVersionState() separately
        $preparedData = $method->invokeArgs((new ExtensionsController()), [$extension, $includeVersions]);

        // ensure installed and active extensions always have a version state
        if (in_array(ArrayHelper::get($preparedData, 'state'), [ExtensionsController::EXTENSION_STATE_INSTALLED, ExtensionsController::EXTENSION_STATE_ACTIVE], true)) {
            $this->assertContains($preparedData['installedVersion']['state'], [ExtensionsController::EXTENSION_VERSION_STATE_STALE, ExtensionsController::EXTENSION_VERSION_STATE_LATEST]);
        } else {
            $this->assertNull($preparedData['installedVersion']['state']);
        }

        // TODO: update to use ArrayHelper::except() when ArrayHelper::except($preparedData, 'installedVersion.state')
        // stops removing everything except installedVersion.state {WV 2021-02-16}
        unset($preparedData['installedVersion']['state']);

        $this->assertEquals($expectedData, $preparedData);
    }

    /**
     * @see testCanPrepareItem
     *
     * @return array[]
     */
    public function dataProviderPrepareItem() : array
    {
        $activePlugin = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods(['isActive', 'isInstalled', 'getInstalledVersion'])
                                    ->getMock();
        $activePlugin->expects($this->any())->method('isActive')->willReturn(true);
        $activePlugin->expects($this->any())->method('isInstalled')->willReturn(true);
        $activePlugin->expects($this->any())->method('getInstalledVersion')->willReturn('1.0.0');
        $activePlugin
            ->setId('1')
            ->setName('Mock Plugin')
            ->setSlug('mock-plugin')
            ->setShortDescription('This is the mock plugin')
            ->setDocumentationUrl('https://mock-plugin.com')
            ->setBrand('woo')
            ->setVersion('1.0.0');
        $activePluginExpectedData = [
            'id' => '1',
            'slug' => 'mock-plugin',
            'name' => 'Mock Plugin',
            'shortDescription' => 'This is the mock plugin',
            'type' => 'plugin',
            'category' => null,
            'brand' => 'woo',
            'installedVersion' => [
                'version' => '1.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            'featured' => false,
            'state' => ExtensionsController::EXTENSION_STATE_ACTIVE,
            'documentationUrl' => 'https://mock-plugin.com',
        ];
        $activePluginNewerVersion = clone $activePlugin;
        $activePluginNewerVersion->setVersion('2.0.0');
        $activePluginExpectedDataWithVersions = $activePluginExpectedData;
        $activePluginExpectedDataWithVersions['versions'] = [
            [
                'version' => '1.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            [
                'version' => '2.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
        ];

        $installedPlugin = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods(['isActive', 'isInstalled', 'getInstalledVersion'])
                                    ->getMock();
        $installedPlugin->expects($this->any())->method('isActive')->willReturn(false);
        $installedPlugin->expects($this->any())->method('isInstalled')->willReturn(true);
        $installedPlugin->expects($this->any())->method('getInstalledVersion')->willReturn('2.0.0');
        $installedPlugin
            ->setId('2')
            ->setName('Mock Plugin 2')
            ->setSlug('mock-plugin-2')
            ->setShortDescription('This is the mock plugin 2')
            ->setDocumentationUrl('https://mock-plugin-2.com')
            ->setBrand('woo')
            ->setVersion('2.0.0');
        $installedPluginExpectedData = [
            'id' => '2',
            'slug' => 'mock-plugin-2',
            'name' => 'Mock Plugin 2',
            'shortDescription' => 'This is the mock plugin 2',
            'type' => 'plugin',
            'category' => null,
            'brand' => 'woo',
            'installedVersion' => [
                'version' => '2.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            'featured' => false,
            'state' => ExtensionsController::EXTENSION_STATE_INSTALLED,
            'documentationUrl' => 'https://mock-plugin-2.com',
        ];
        $installedPluginNewerVersion = clone $installedPlugin;
        $installedPluginNewerVersion->setVersion('3.0.0');
        $installedPluginExpectedDataWithVersions = $installedPluginExpectedData;
        $installedPluginExpectedDataWithVersions['versions'] = [
            [
                'version' => '2.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            [
                'version' => '3.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
        ];

        $notInstalledPlugin = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods(['isActive', 'isInstalled', 'getInstalledVersion'])
                                    ->getMock();
        $notInstalledPlugin->expects($this->any())->method('isActive')->willReturn(false);
        $notInstalledPlugin->expects($this->any())->method('isInstalled')->willReturn(false);
        $notInstalledPlugin->expects($this->any())->method('getInstalledVersion')->willReturn(null);
        $notInstalledPlugin
            ->setId('3')
            ->setName('Mock Plugin 3')
            ->setSlug('mock-plugin-3')
            ->setShortDescription('This is the mock plugin 3')
            ->setDocumentationUrl('https://mock-plugin-3.com')
            ->setBrand('woo')
            ->setVersion('1.0.0');
        $notInstalledPluginExpectedData = [
            'id' => '3',
            'slug' => 'mock-plugin-3',
            'name' => 'Mock Plugin 3',
            'shortDescription' => 'This is the mock plugin 3',
            'type' => 'plugin',
            'category' => null,
            'brand' => 'woo',
            'installedVersion' => [
                'version' => null,
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            'featured' => false,
            'state' => ExtensionsController::EXTENSION_STATE_UNINSTALLED,
            'documentationUrl' => 'https://mock-plugin-3.com',
        ];
        $notInstalledPluginNewerVersion = clone $installedPlugin;
        $notInstalledPluginNewerVersion->setVersion('2.0.0');
        $notInstalledPluginExpectedDataWithVersions = $notInstalledPluginExpectedData;
        $notInstalledPluginExpectedDataWithVersions['versions'] = [
            [
                'version' => '1.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            [
                'version' => '2.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
        ];

        $activeTheme = $this->getMockBuilder(ThemeExtension::class)
                             ->onlyMethods(['isActive', 'isInstalled', 'getInstalledVersion'])
                             ->getMock();
        $activeTheme->expects($this->any())->method('isActive')->willReturn(true);
        $activeTheme->expects($this->any())->method('isInstalled')->willReturn(true);
        $activeTheme->expects($this->any())->method('getInstalledVersion')->willReturn('3.0.0');
        $activeTheme
            ->setId('4')
            ->setName('Mock Theme')
            ->setSlug('mock-theme')
            ->setShortDescription('This is the mock theme')
            ->setDocumentationUrl('https://mock-theme.com')
            ->setBrand('woo')
            ->setVersion('3.0.0');
        $activeThemeExpectedData = [
            'id' => '4',
            'slug' => 'mock-theme',
            'name' => 'Mock Theme',
            'shortDescription' => 'This is the mock theme',
            'type' => 'theme',
            'category' => null,
            'brand' => 'woo',
            'installedVersion' => [
                'version' => '3.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            'featured' => false,
            'state' => ExtensionsController::EXTENSION_STATE_ACTIVE,
            'documentationUrl' => 'https://mock-theme.com',
        ];
        $activeThemeNewerVersion = clone $activeTheme;
        $activeThemeNewerVersion->setVersion('4.0.0');
        $activeThemeExpectedDataWithVersions = $activeThemeExpectedData;
        $activeThemeExpectedDataWithVersions['versions'] = [
            [
                'version' => '3.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
            [
                'version' => '4.0.0',
                'minimumPhpVersion' => null,
                'minimumWordPressVersion' => null,
                'minimumWooCommerceVersion' => null,
                'releasedAt' => null,
                'package' => null,
            ],
        ];

        return [
            [$activePlugin, [], false, $activePluginExpectedData],
            [$activePlugin, [$activePlugin], false, $activePluginExpectedData],
            [$activePlugin, [$activePlugin, $activePluginNewerVersion], false, $activePluginExpectedData],
            [$activePlugin, [$activePlugin, $activePluginNewerVersion], true, $activePluginExpectedDataWithVersions],
            [$installedPlugin, [], false, $installedPluginExpectedData],
            [$installedPlugin, [$installedPlugin], false, $installedPluginExpectedData],
            [$installedPlugin, [$installedPlugin, $installedPluginNewerVersion], false, $installedPluginExpectedData],
            [$installedPlugin, [$installedPlugin, $installedPluginNewerVersion], true, $installedPluginExpectedDataWithVersions],
            [$notInstalledPlugin, [], false, $notInstalledPluginExpectedData],
            [$notInstalledPlugin, [$notInstalledPlugin], false, $notInstalledPluginExpectedData],
            [$notInstalledPlugin, [$notInstalledPlugin, $notInstalledPluginNewerVersion], false, $notInstalledPluginExpectedData],
            [$notInstalledPlugin, [$notInstalledPlugin, $notInstalledPluginNewerVersion], true, $notInstalledPluginExpectedDataWithVersions],
            [$activeTheme, [], false, $activeThemeExpectedData],
            [$activeTheme, [$activeTheme], false, $activeThemeExpectedData],
            [$activeTheme, [$activeTheme, $activeThemeNewerVersion], false, $activeThemeExpectedData],
            [$activeTheme, [$activeTheme, $activeThemeNewerVersion], true, $activeThemeExpectedDataWithVersions],
        ];
    }

    /**
     * Tests the ExtensionsController::getItemSchema() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::getItemSchema()
     */
    public function testCanGetItemSchema()
    {
        WP_Mock::userFunction('__')->andReturnArg(0);

        $schema = (new ExtensionsController())->getItemSchema();

        $this->assertSame('extension', $schema['title']);
        $this->assertSame('object', $schema['type']);

        // test the actual structure so a red flag is raised if it's ever changed accidentally
        $this->assertSame([
            'id' => [
                'description' => __('The extension ID.', 'mwc-dashboard'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'slug' => [
                'description' => __('The extension slug.', 'mwc-dashboard'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'name' => [
                'description' => __('The extension name.', 'mwc-dashboard'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'shortDescription' => [
                'description' => __('The extension short description.', 'mwc-dashboard'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'type' => [
                'description' => __('The extension type.', 'mwc-dashboard'),
                'type'        => 'string',
                'enum'        => ['plugin', 'theme'],
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'category' => [
                'description' => __('The extension category.', 'mwc-dashboard'),
                'type'        => 'string',
                'enum'        => [
                    'Cart and Checkout',
                    'Marketing and Messaging',
                    'Merchandizing',
                    'Payments',
                    'Product Type',
                    'Shipping',
                    'Store Management',
                ],
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'brand' => [
                'description' => __('The extension brand.', 'mwc-dashboard'),
                'type'        => 'string',
                'enum'        => ['godaddy', 'woo'],
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'installedVersion' => [
                'description' => __('Information about the extension installed version.', 'mwc-dashboard'),
                'type'        => 'object',
                'properties'  => [
                    'version' => [
                        'description' => __('The version number.', 'mwc-dashboard'),
                        'type'        => 'string',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'minimumPhpVersion' => [
                        'description' => __('The required PHP version.', 'mwc-dashboard'),
                        'type'        => 'string',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'minimumWordPressVersion' => [
                        'description' => __('The required WordPress version.', 'mwc-dashboard'),
                        'type'        => 'string',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'minimumWooCommerceVersion' => [
                        'description' => __('The required WooCommerce version.', 'mwc-dashboard'),
                        'type'        => 'string',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'releasedAt' => [
                        'description' => __('The timestamp in seconds when the version was released.', 'mwc-dashboard'),
                        'type'        => 'int',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'package' => [
                        'description' => __('The URL from where the package can be downloaded', 'mwc-dashboard'),
                        'type'        => 'string',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                    'state' => [
                        'description' => __('The state of the installed version (whether or not it is the latest version)', 'mwc-dashboard'),
                        'type'        => 'string',
                        'enum'        => [ExtensionsController::EXTENSION_VERSION_STATE_LATEST, ExtensionsController::EXTENSION_VERSION_STATE_STALE],
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                ],
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'documentationUrl' => [
                'description' => __('The extension documentation URL.', 'mwc-dashboard'),
                'type'        => 'string',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'featured' => [
                'description' => __('Whether or not the extension is featured.', 'mwc-dashboard'),
                'type'        => 'bool',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'state' => [
                'description' => __('The extension state.', 'mwc-dashboard'),
                'type'        => 'string',
                'enum'        => [ExtensionsController::EXTENSION_STATE_ACTIVE, ExtensionsController::EXTENSION_STATE_INSTALLED, ExtensionsController::EXTENSION_STATE_UNINSTALLED],
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'versions' => [
                'description' => __('Information about the versions available for the extension.', 'mwc-dashboard'),
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'version' => [
                            'description' => __('The version number.', 'mwc-dashboard'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                        'minimumPhpVersion' => [
                            'description' => __('The required PHP version.', 'mwc-dashboard'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                        'minimumWordPressVersion' => [
                            'description' => __('The required WordPress version.', 'mwc-dashboard'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                        'minimumWooCommerceVersion' => [
                            'description' => __('The required WooCommerce version.', 'mwc-dashboard'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                        'releasedAt' => [
                            'description' => __('The timestamp in seconds when the version was released.', 'mwc-dashboard'),
                            'type'        => 'int',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                        'package' => [
                            'description' => __('The URL from where the package can be downloaded', 'mwc-dashboard'),
                            'type'        => 'string',
                            'context'     => ['view', 'edit'],
                            'readonly'    => true,
                        ],
                    ],
                ],
                'context' => ['view', 'edit'],
                'readonly' => true,
            ],
        ], $schema['properties']);
    }

    /** @see testCanGetExtensionCategory() */
    public function provideGetExtensionsCategoryData()
    {
        return [
            ['Test Category', 'plugin-with-category', null],
            ['Property Value', 'test-plugin', 'Property Value'],
            [null, 'test-plugin', null],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::getExtensionVersionState()
     *
     * @dataProvider provideCanGetExtensionVersionStateData()
     */
    public function testCanGetExtensionVersionState($state, bool $installed, string $version, string $installedVersion)
    {
        // prepare main extension object
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('getVersion')->andReturn($version);
        $extension->shouldReceive('isInstalled')->andReturn($installed);

        // prepare object representing the installed version
        $installedExtension = Mockery::mock(PluginExtension::class);

        $installedExtension->shouldReceive('getVersion')->andReturn($installedVersion);

        $method = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'getExtensionVersionState');

        $this->assertSame($state, $method->invoke(new ExtensionsController(), $extension, $installedExtension));
    }

    /** @see testCanGetExtensionVersionState() */
    public function provideCanGetExtensionVersionStateData()
    {
        return [
            [ExtensionsController::EXTENSION_VERSION_STATE_STALE, true, '3.2.1', '2.5.6'],
            [ExtensionsController::EXTENSION_VERSION_STATE_LATEST, true, '3.2.1', '3.2.1'],

            [null, false, '3.2.1', '2.5.6'],
            [null, false, '3.2.1', '3.2.1'],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::getExtensionVersionState()
     *
     * @dataProvider provideCanGetExtensionVersionStateData()
     */
    public function testCanGetVersionStateForWooExtensions($state, bool $installed, string $version, string $installedVersion)
    {
        // prepare main extension object
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('getVersion')->andReturn($version);
        $extension->shouldReceive('isInstalled')->andReturn($installed);
        $extension->shouldReceive('getInstalledVersion')->andReturn($installedVersion);

        $method = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'getExtensionVersionState');

        $this->assertSame($state, $method->invoke(new ExtensionsController(), $extension));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::isFeaturedExtension()
     */
    public function testCanDetermineWhetherIsExtensionIsFeatured() {
        $extension = (new PluginExtension())->setSlug('test-plugin');

        Configuration::set('mwc_extensions.featured', ['test-plugin' => true]);

        $method = TestHelpers::getInaccessibleMethod(ExtensionsController::class, 'isFeaturedExtension');

        $this->assertTrue($method->invoke(new ExtensionsController(), $extension));

        Configuration::set('mwc_extensions.featured', []);

        $this->assertFalse($method->invoke(new ExtensionsController(), $extension));
    }

    /**
     * Tests the ExtensionsController::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::getItemSchema()
     */
    public function testUpdateItemCanUninstallPlugin()
    {
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('isInstalled')->andReturn(true);
        $extension->shouldReceive('uninstall')->times(1);

        $this->callUpdateItem($extension, [
            'state' => ExtensionsController::EXTENSION_STATE_UNINSTALLED,
        ]);
    }

    /**
     * Tests the ExtensionsController::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::updateItem()
     */
    public function testUpdateItemCanDeactivatePlugin()
    {
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('isInstalled')->andReturn(true);
        $extension->shouldReceive('isActive')->andReturn(true);
        $extension->shouldReceive('deactivate')->times(1);

        $this->callUpdateItem($extension, [
            'state' => ExtensionsController::EXTENSION_STATE_INSTALLED,
        ]);
    }

    /**
     * Tests the ExtensionsController::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::updateItem()
     */
    public function testUpdateItemCanSwitchExtensionVersion()
    {
        $currentVersion = Mockery::mock(PluginExtension::class);

        $currentVersion->shouldReceive('getInstalledVersion')->andReturn('1.0.0');
        $currentVersion->shouldReceive('isInstalled')->andReturn(true);
        $currentVersion->shouldReceive('isActive')->andReturn(true);
        $currentVersion->shouldReceive('deactivate')->times(1);

        $requestedVersion = Mockery::mock(PluginExtension::class);

        $requestedVersion->shouldReceive('isActive')->andReturn(true);
        $requestedVersion->shouldReceive('install')->times(1);
        $requestedVersion->shouldReceive('activate')->times(1);

        Patchwork\redefine(ExtensionsController::class.'::getManageExtensionVersion', Patchwork\always($requestedVersion));

        $this->callUpdateItem($currentVersion, [
            'state' => ExtensionsController::EXTENSION_STATE_ACTIVE,
            'version' => [
                'version' => '2.0.0',
            ],
        ]);
    }

    /**
     * Tests the ExtensionsController::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::updateItem()
     */
    public function testUpdateItemCanInstallExtension()
    {
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('getInstalledVersion')->andReturn('1.0.0');
        $extension->shouldReceive('isInstalled')->andReturn(false);
        $extension->shouldReceive('isActive')->andReturn(false);
        $extension->shouldReceive('install')->times(1);

        $this->callUpdateItem($extension, [
            'state' => ExtensionsController::EXTENSION_STATE_INSTALLED,
            'version' => [
                'version' => '1.0.0',
            ],
        ]);
    }

    /**
     * Tests the ExtensionsController::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController::updateItem()
     */
    public function testUpdateItemCanActivateExtension()
    {
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('getInstalledVersion')->andReturn('1.0.0');
        $extension->shouldReceive('isInstalled')->andReturn(true);
        $extension->shouldReceive('isActive')->andReturn(false);
        $extension->shouldReceive('activate')->times(1);

        $this->callUpdateItem($extension, [
            'state' => ExtensionsController::EXTENSION_STATE_ACTIVE,
            'version' => [
                'version' => '1.0.0',
            ],
        ]);
    }

    /**
     * Tests the Extensions::updateItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\Extensions::updateItem()
     */
    public function testUpdateItemCanInstallAndActivateExtension()
    {
        $extension = Mockery::mock(PluginExtension::class);

        $extension->shouldReceive('getInstalledVersion')->andReturn('1.0.0');
        $extension->shouldReceive('isInstalled')->andReturn(false);
        $extension->shouldReceive('isActive')->andReturn(false);
        $extension->shouldReceive('activate')->times(1);
        $extension->shouldReceive('install')->times(1);

        $this->callUpdateItem($extension, [
            'state' => ExtensionsController::EXTENSION_STATE_ACTIVE,
            'version' => [
                'version' => '1.0.0',
            ],
        ]);
    }

    /**
     * Mocks {@see ExtensionsController::updateItem()} to operate on the given extension and payload.
     *
     * @param Mockery\MockInterface $extension a mock of an extension
     * @param array $payload test request payload
     *
     * @return array
     */
    protected function callUpdateItem($extension, array $payload) : array
    {
        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_json_params')->andReturn($payload);
        $request->shouldReceive('get_param')->withArgs(['slug'])->andReturn('plugin');

        $this->mockStaticMethod(StringHelper::class.'::sanitize')->andReturnArg(0);

        Patchwork\redefine(ExtensionsController::class.'::getManagedExtension', Patchwork\always($extension));
        Patchwork\redefine(ExtensionsController::class.'::prepareItem', Patchwork\always(['response']));

        WP_Mock::userFunction('rest_ensure_response')->andReturnArg(0);

        $response = (new ExtensionsController())->updateItem($request);

        $this->assertSame(['response'], $response);

        return (array) $response;
    }
}
