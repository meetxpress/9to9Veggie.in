<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\DataSources\WordPress\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter
 */
final class WordPressScreenAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests whether the constructor can set screen property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::__construct()
     * @throws ReflectionException
     */
    public function testCanConstructorSetScreenProperty()
    {
        $screen = $this->getWPScreenMock('page', 'add', 'page');

        $adapter = new WordPressScreenAdapter($screen);

        $this->assertSame($screen, (TestHelpers::getInaccessibleProperty(WordPressScreenAdapter::class, 'screen'))->getValue($adapter));
    }

    /**
     * Tests that if can get post list page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getPostListPageData()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getNormalizedPostType()
     * @throws ReflectionException
     */
    public function testCanGetPostListPageData()
    {
        $screen = $this->getWPScreenMock('pages', 'list', 'page');

        $this->assertSame([
            'pageId'       => 'page_list',
            'pageContexts' => ['page_list'],
        ], (TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getPostListPageData'))->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * Tests that if can get add post page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getAddPostPageData()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getNormalizedPostType()
     * @throws ReflectionException
     */
    public function testCanGetAddPostPageData()
    {
        $screen = $this->getWPScreenMock('post', 'add', 'product');

        $this->assertSame([
            'pageId'       => 'add_product',
            'pageContexts' => ['add_product'],
        ], (TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getAddPostPageData'))->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * Tests that if can get edit post page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getEditPostPageData()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getNormalizedPostType()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getNormalizedPostStatus()
     * @throws ReflectionException
     */
    public function testCanGetEditPostPageData()
    {
        $screen = $this->getWPScreenMock('post', 'edit', 'shop_coupon');

        $_REQUEST['post'] = 123;

        WP_Mock::userFunction('get_post_status')->with(123)->once()->andReturn('wc-published');

        $this->assertSame([
            'pageId'       => 'edit_coupon',
            'pageContexts' => ['edit_coupon'],
            'objectId'     => '123',
            'objectType'   => 'coupon',
            'objectStatus' => 'published',
        ], (TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getEditPostPageData'))->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * Tests that if can get WooCommerce settings page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommerceSettingsPageData()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommercePageId()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommercePageContexts()
     *
     * @param string $tab
     * @param string $section
     * @param array $data
     *
     * @throws ReflectionException
     * @dataProvider providerCanGetWooCommerceSettingsPageData
     */
    public function testCanGetWooCommerceSettingsPageData(string $tab, string $section, array $data)
    {
        $screen = $this->getWPScreenMock('woocommerce_page_wc-settings', '', '');

        $_REQUEST['tab'] = $tab;
        $_REQUEST['section'] = $section;

        $this->assertSame($data, (TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getWooCommerceSettingsPageData'))->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * @see WordPressScreenAdapterTest::testCanGetWooCommerceSettingsPageData()
     *
     * @return array
     */
    public function providerCanGetWooCommerceSettingsPageData() : array
    {
        return [
            'Settings only'                                  => [
                '',
                '',
                [
                    'pageId'       => 'woocommerce_settings',
                    'pageContexts' => [
                        'woocommerce_settings',
                    ],
                ]
            ],
            'Settings with General tab'                      => [
                'general',
                '',
                [
                    'pageId'       => 'woocommerce_settings_general',
                    'pageContexts' => [
                        'woocommerce_settings',
                        'woocommerce_settings_general',
                    ],
                ]
            ],
            'Settings with General tab and Currency section' => [
                'general',
                'currency',
                [
                    'pageId'       => 'woocommerce_settings_general_currency',
                    'pageContexts' => [
                        'woocommerce_settings',
                        'woocommerce_settings_general',
                        'woocommerce_settings_general_currency',
                    ],
                ]
            ],
        ];
    }

    /**
     * Tests that if can get WooCommerce admin page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommerceAdminPageData()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommercePageId()
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getWooCommercePageContexts()
     *
     * @param string $path
     * @param array $data
     *
     * @throws ReflectionException
     * @dataProvider providerCanGetWooCommerceAdminPageData
     */
    public function testCanGetWooCommerceAdminPageData($path, array $data)
    {
        $screen = $this->getWPScreenMock('woocommerce_page_wc-admin', '', '');

        $_REQUEST['path'] = $path;

        $this->assertSame($data, (TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getWooCommerceAdminPageData'))->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * @see WordPressScreenAdapterTest::testCanGetWooCommerceSettingsPageData()
     *
     * @return array
     */
    public function providerCanGetWooCommerceAdminPageData() : array
    {
        return [
            'No path param'              => [
                null,
                [
                    'pageId'       => 'woocommerce_admin',
                    'pageContexts' => [
                        'woocommerce_admin',
                    ],
                ],
            ],
            'Admin only'                 => [
                '',
                [
                    'pageId'       => 'woocommerce_admin',
                    'pageContexts' => [
                        'woocommerce_admin',
                    ],
                ],
            ],
            'Admin Marketing > Overview' => [
                '%2Fmarketing',
                [
                    'pageId'       => 'woocommerce_admin_marketing',
                    'pageContexts' => [
                        'woocommerce_admin',
                        'woocommerce_admin_marketing',
                    ],
                ],
            ],
            'Admin Analytics > Products' => [
                '%2Fanalytics%2Fproducts',
                [
                    'pageId'       => 'woocommerce_admin_analytics_products',
                    'pageContexts' => [
                        'woocommerce_admin',
                        'woocommerce_admin_analytics',
                        'woocommerce_admin_analytics_products',
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests that if can get generic page data.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::getGenericPageData()
     * @throws ReflectionException
     */
    public function testCanGetGenericPageData()
    {
        $screen = $this->getWPScreenMock('post', '', '');

        $method = TestHelpers::getInaccessibleMethod(WordPressScreenAdapter::class, 'getGenericPageData');

        $this->assertSame([
            'pageId'       => 'post',
            'pageContexts' => ['post'],
        ], $method->invoke(new WordPressScreenAdapter($screen)));
    }

    /**
     * Tests that if can convert to source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::convertToSource()
     */
    public function testCanConvertToSource()
    {
        $adapter = new WordPressScreenAdapter($this->getWPScreenMock('post', '', ''));

        $this->assertIsArray($adapter->convertToSource());
        $this->assertEquals([], $adapter->convertToSource());
    }

    /**
     * Tests that if can get  convert from source.
     *
     * @covers       \GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters\WordPressScreenAdapter::convertFromSource()
     *
     * @param string $base
     * @param string $action
     * @param string $postType
     * @param array $request
     * @param array $expectedData
     *
     * @dataProvider providerCanConvertFromSource
     */
    public function testCanConvertFromSource(string $base, string $action, string $postType, array $request, array $expectedData)
    {
        $screen = $this->getWPScreenMock($base, $action, $postType);

        $_REQUEST = $request;

        if ($postId = ArrayHelper::get($_REQUEST, 'post')) {
            WP_Mock::userFunction('get_post_status')->with($postId)->once()->andReturn('draft');
        }

        $convertedData = (new WordPressScreenAdapter($screen))->convertFromSource();

        $this->assertIsArray($convertedData);
        $this->assertSame($expectedData, $convertedData);
    }

    /**
     * @see WordPressScreenAdapterTest::testCanConvertFromSource()
     *
     * @return array[]
     */
    public function providerCanConvertFromSource() : array
    {
        return [
            'List page'                 => [
                'edit',
                '',
                'post',
                [],
                [
                    'pageId'       => 'post_list',
                    'pageContexts' => ['post_list'],
                ]
            ],
            'Add page'                  => [
                'post',
                'add',
                'post',
                [],
                [
                    'pageId'       => 'add_post',
                    'pageContexts' => ['add_post'],
                ]
            ],
            'Edit page'                 => [
                'post',
                'edit',
                'post',
                [
                    'post' => '456',
                ],
                [
                    'pageId'       => 'edit_post',
                    'pageContexts' => ['edit_post'],
                    'objectId'     => '456',
                    'objectType'   => 'post',
                    'objectStatus' => 'draft',
                ]
            ],
            'WooCommerce Settings page' => [
                'woocommerce_page_wc-settings',
                '',
                '',
                [
                    'tab'     => 'shipping',
                    'section' => 'zones',
                ],
                [
                    'pageId'       => 'woocommerce_settings_shipping_zones',
                    'pageContexts' => [
                        'woocommerce_settings',
                        'woocommerce_settings_shipping',
                        'woocommerce_settings_shipping_zones',
                    ],
                ],
            ],
            'Genetic page'              => [
                'page',
                '',
                '',
                [],
                [
                    'pageId'       => 'page',
                    'pageContexts' => ['page'],
                ],
            ],
        ];
    }

    /**
     * @param string $base
     * @param string $action
     * @param string $postType
     *
     * @return Mockery\LegacyMockInterface|Mockery\MockInterface|\WP_Screen
     */
    private function getWPScreenMock(string $base, string $action, string $postType)
    {
        $screen = Mockery::mock('WP_Screen');

        $screen->base = $base;
        $screen->action = $action;
        $screen->post_type = $postType;

        return $screen;
    }
}
