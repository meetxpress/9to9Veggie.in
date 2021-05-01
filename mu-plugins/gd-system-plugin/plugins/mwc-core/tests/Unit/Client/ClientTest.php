<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Client;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\Client\Client;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Client\Client
 */
final class ClientTest extends WPTestCase
{
    /**
     * Runs before each test.
     * @throws Exception
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'hasEcommercePlan')
             ->andReturn(true);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::__construct()
     * @throws ReflectionException
     * @throws Exception
     */
    public function testConstructor()
    {
        Configuration::set('mwc.client.index.url', 'https://example.com');

        $properties = [
            'appHandle' => 'mwcClient',
            'appSource' => 'https://example.com',
        ];

        $client = new Client();

        foreach ($properties as $propertyName => $expectedValue) {
            $property = TestHelpers::getInaccessibleProperty(Client::class, $propertyName);

            $this->assertSame($expectedValue, $property->getValue($client));
        }
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::registerHooks()
     * @throws ReflectionException
     */
    public function testRegisterHooks()
    {
        $method = TestHelpers::getInaccessibleMethod(Client::class, 'registerHooks');
        $client = new Client();

        WP_Mock::expectActionAdded('admin_enqueue_scripts', [$client, 'enqueueAssets'], 10, 1);
        WP_Mock::expectActionAdded('admin_print_styles', [$client, 'enqueueMessagesContainerStyles'], 10, 1);
        WP_Mock::expectActionAdded('all_admin_notices', [$client, 'renderMessagesContainer'], 10, 1);

        $method->invoke($client);

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::renderMessagesContainerStyles()
     * @throws Exception
     */
    public function testCanRenderMessagesContainerStyles()
    {
        Configuration::set('mwc.assets.styles', '/foobar');

        $client = new Client();

        WP_Mock::userFunction('wp_register_style')
            ->withSomeOfArgs('mwcClient-messages-styles')
            ->once();
        WP_Mock::userFunction('wp_enqueue_style')
            ->withSomeOfArgs('mwcClient-messages-styles')
            ->once();

        $client->enqueueMessagesContainerStyles();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::renderMessagesContainer()
     * @throws Exception
     */
    public function testCanRenderMessagesContainer()
    {
        $client = new Client();

        ob_start();
        $client->renderMessagesContainer();

        $this->assertStringContainsString('<div id="mwc-messages-container" class="mwc-messages-container">', ob_get_clean());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::enqueueAssets()
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::enqueueApp()
     * @throws Exception
     */
    public function testEnqueueAssets()
    {
        Configuration::set('mwc.client.runtime.url', 'https://example.com/runtime');
        Configuration::set('mwc.client.vendors.url', 'https://example.com/vendors');
        Configuration::set('mwc.client.index.url', 'https://example.com/index');

        $this->mockStaticMethod(WordPressRepository::class, 'getCurrentScreen')
            ->andReturn(null);

        $client = new Client();

        WP_Mock::userFunction('wp_register_script', ['times' => 1])->with('mwcClient-runtime', 'https://example.com/runtime', [], null, true);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1])->with('mwcClient-runtime');

        WP_Mock::userFunction('wp_register_script', ['times' => 1])->with('mwcClient-vendors', 'https://example.com/vendors', [], null, true);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1])->with('mwcClient-vendors');

        WP_Mock::userFunction('wp_register_script', ['times' => 1])->with('mwcClient', 'https://example.com/index', [], null, true);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1])->with('mwcClient');
        WP_Mock::userFunction('wp_localize_script', ['times' => 1])->with('mwcClient', 'mwcClient', ['root' => 'https://example.com/rest', 'nonce' => 'nonce']);

        WP_Mock::userFunction('rest_url')->andReturn('https://example.com/rest');
        WP_Mock::userFunction('esc_url_raw')->andReturnArg(0);
        WP_Mock::userFunction('wp_create_nonce')->andReturn('nonce');

        $client->enqueueAssets();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Client\Client::getPageContextVariables()
     *
     * @dataProvider providerCanGetPageContextVariables
     *
     * @param $screen
     * @param $context
     *
     * @throws ReflectionException
     * @throws Exception
     *
     * @return void
     */
    public function testCanGetPageContextVariables($screen, $context)
    {
        $this->mockStaticMethod(WordPressRepository::class, 'getCurrentScreen')->andReturn($screen);

        $method = TestHelpers::getInaccessibleMethod(Client::class, 'getPageContextVariables');

        $this->assertSame($context, ArrayHelper::get($method->invoke(new Client()), 'page'));
    }

    /** @see testCanGetPageContextVariables */
    public function providerCanGetPageContextVariables()
    {
        return [
            [
                new Screen([
                    'pageId'       => 'plugins',
                    'pageContexts' => ['plugins'],
                ]),
                [
                    'id'       => 'plugins',
                    'contexts' => ['plugins'],
                ]
            ],
            [
                null,
                null,
            ],
        ];
    }
}
