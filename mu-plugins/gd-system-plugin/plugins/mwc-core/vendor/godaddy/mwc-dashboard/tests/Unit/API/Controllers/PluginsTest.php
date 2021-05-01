<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository;
use Mockery;
use ReflectionClass;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController
 */
final class PluginsTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new PluginsController();
        $route = TestHelpers::getInaccessibleProperty($controller, 'route');
        $this->assertSame('shop/plugins', $route->getValue($controller));
    }

    /**
     * Tests the getItemSchema() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController::getItemSchema()
     */
    public function testGetItemSchema()
    {
        WP_Mock::userFunction('__');

        $controller = new PluginsController();

        $this->assertIsArray($controller->getItemSchema());
        $this->assertIsArray($controller->getItemSchema()['properties']);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController::getItems()
     *
     * @param PluginExtension[] $managedPlugins
     * @param PluginExtension[] $skyVergePlugins
     * @param array $expectedResponse
     *
     * @throws Exception
     * @dataProvider dataProviderGetItems
     */
    public function testGetItems(array $managedPlugins, array $skyVergePlugins, array $expectedResponse)
    {
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedPlugins')
             ->andReturn($managedPlugins);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceSkyVergePlugins')
             ->andReturn($skyVergePlugins);

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getWooCommerceLicense')
            ->andReturn('mock plugin license');

        $this->mockStaticMethod(WooCommercePluginsRepository::class, 'getDocumentationUrl')
            ->andReturn('https://queried-mock-plugin.com');

        $controller = new PluginsController();

        $response = (object) ['response' => true];

        WP_Mock::userFunction('rest_ensure_response')
            ->with([
                'plugins' => $expectedResponse,
            ])
            ->andReturn($response);

        $this->assertSame($response, $controller->getItems());
    }

    /**
     * @see testGetItems
     *
     * @return array[]
     */
    public function dataProviderGetItems() : array
    {
        WP_Mock::userFunction('get_transient')->andReturn(null);

        $mockPluginExtension = $this->getMockBuilder(PluginExtension::class)
                                                ->onlyMethods(['isInstalled'])
                                                ->getMock();
        $mockPluginExtension->expects($this->any())->method('isInstalled')->willReturn(true);

        $mockPluginExtension
            ->setId('1')
            ->setName('Mock Plugin')
            ->setSlug('mock-plugin')
            ->setDocumentationUrl('https://mock-plugin.com');

        $mockPluginExtensionWithNoDocumentationUrl = $this->getMockBuilder(PluginExtension::class)
                                    ->onlyMethods(['isInstalled'])
                                    ->getMock();
        $mockPluginExtensionWithNoDocumentationUrl->expects($this->once())->method('isInstalled')->willReturn(true);
        $mockPluginExtensionWithNoDocumentationUrl
            ->setId('2')
            ->setName('Mock Plugin with no documentation URL')
            ->setSlug('mock-plugin-with-no-doc-url');

        $mockPluginExtensionNotInstalled = $this->getMockBuilder(PluginExtension::class)
                     ->onlyMethods(['isInstalled'])
                     ->getMock();
        $mockPluginExtensionNotInstalled->expects($this->once())->method('isInstalled')->willReturn(false);
        $mockPluginExtensionNotInstalled
            ->setId('3')
            ->setName('Mock Plugin that is not installed')
            ->setSlug('mock-plugin-not-installed');

        return [
            [[], [], []],
            [[$mockPluginExtensionNotInstalled], [], []],
            [[$mockPluginExtension], [], [
                [
                    'slug' => $mockPluginExtension->getSlug(),
                    'name' => $mockPluginExtension->getName(),
                    'managed' => true,
                    'license' => 'mock plugin license',
                    'documentationUrl' => $mockPluginExtension->getDocumentationUrl(),
                ],
            ]],
            [[$mockPluginExtension], [$mockPluginExtension], [
                [
                    'slug' => $mockPluginExtension->getSlug(),
                    'name' => $mockPluginExtension->getName(),
                    'managed' => true,
                    'license' => 'mock plugin license',
                    'documentationUrl' => $mockPluginExtension->getDocumentationUrl(),
                ],
            ]],
            [[], [$mockPluginExtension], [
                [
                    'slug' => $mockPluginExtension->getSlug(),
                    'name' => $mockPluginExtension->getName(),
                    'managed' => false,
                    'license' => 'mock plugin license',
                    'documentationUrl' => $mockPluginExtension->getDocumentationUrl(),
                ],
            ]],
            [[$mockPluginExtensionWithNoDocumentationUrl], [], [
                [
                    'slug' => $mockPluginExtensionWithNoDocumentationUrl->getSlug(),
                    'name' => $mockPluginExtensionWithNoDocumentationUrl->getName(),
                    'managed' => true,
                    'license' => 'mock plugin license',
                    'documentationUrl' => 'https://queried-mock-plugin.com',
                ],
            ]],
            [[], [$mockPluginExtensionWithNoDocumentationUrl], [
                [
                    'slug' => $mockPluginExtensionWithNoDocumentationUrl->getSlug(),
                    'name' => $mockPluginExtensionWithNoDocumentationUrl->getName(),
                    'managed' => false,
                    'license' => 'mock plugin license',
                    'documentationUrl' => 'https://queried-mock-plugin.com',
                ],
            ]],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController::registerRoutes()
     */
    public function testRegisterRoutes()
    {
        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/shop/plugins', Mockery::any());

        (new PluginsController())->registerRoutes();

        $this->assertConditionsMet();
    }
}
