<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Pages\Plugins;

use Exception;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\Pages\Plugins\IncludedWooCommerceExtensionsTab;
use ReflectionClass;
use ReflectionException;
use WP_Mock;

class IncludedWooCommerceExtensionsTabTest extends WPTestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Pages\Plugins\IncludedWooCommerceExtensionsTab::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $tab = new IncludedWooCommerceExtensionsTab();

        $reflection = new ReflectionClass($tab);

        $registerFiltersMethod = $reflection->getMethod('registerFilters');
        $registerFiltersMethod->setAccessible(true);

        WP_Mock::expectFilterAdded('views_plugin-install', [$tab, 'addView'], 10, 1);

        $registerFiltersMethod->invoke($tab);

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\Pages\Plugins\IncludedWooCommerceExtensionsTab::addView()
     *
     * @param string|array $views
     * @param string|array $expected
     *
     * @throws Exception
     *
     * @dataProvider dataProviderAddView
     */
    public function testAddView($views, $expected)
    {
        $tab = new IncludedWooCommerceExtensionsTab();

        $resultedViews = $tab->addView($views);

        $this->assertSame($expected, $resultedViews);
    }

    /** @see testAddView */
    public function dataProviderAddView() : array
    {
        $pluginInstallPopular       = 'plugin-install-popular';
        $pluginInstallGdIncluded    = 'plugin-install-gd-included';
        $pluginInstallGdIncludedUrl = '<a href="/wp-admin/admin.php?page=wc-addons">Included WooCommerce Extensions</a>';

        return [
            'when $views is null'                                               => [null, null],
            'when $views is not an array'                                       => ['test', 'test'],
            'when $views has the plugin-install-popular index'                  => [
                [ 'a' => 'a', $pluginInstallPopular => 'url', 'b' => 'b' ],
                [ 'a' => 'a', $pluginInstallGdIncluded => $pluginInstallGdIncludedUrl, $pluginInstallPopular => 'url', 'b' => 'b' ]
            ],
            'when $views has the plugin-install-popular index at the beginning' => [
                [ $pluginInstallPopular => 'url', 'a' => 'a', 'b' => 'b' ],
                [ $pluginInstallGdIncluded => $pluginInstallGdIncludedUrl, $pluginInstallPopular => 'url', 'a' => 'a', 'b' => 'b' ]
            ],
            'when $views has the plugin-install-popular index at the end'       => [
                [ 'a' => 'a', 'b' => 'b', $pluginInstallPopular => 'url' ],
                [ 'a' => 'a', 'b' => 'b', $pluginInstallGdIncluded => $pluginInstallGdIncludedUrl, $pluginInstallPopular => 'url' ]
            ],
            'when $views has no plugin-install-popular index'                   => [
                [ 'a' => 'a', 'b' => 'b' ],
                [ $pluginInstallGdIncluded => $pluginInstallGdIncludedUrl, 'a' => 'a', 'b' => 'b' ],
            ],
        ];
    }
}
