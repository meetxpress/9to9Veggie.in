<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\Menu;

use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Menu\GetHelpMenu;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Menu\GetHelpMenu
 */
final class GetHelpMenuTest extends WPTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        if (! defined('MWC_DASHBOARD_PLUGIN_URL')) {
            define('MWC_DASHBOARD_PLUGIN_URL', 'https://demo.local/wp-content/plugins/mwc-dashboard/');
        }
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Menu\GetHelpMenu::addMenuItem
     */
    public function testAddMenuItem()
    {
        $menu = $this->getInstance();

        WP_Mock::userFunction('add_menu_page', ['times' => 1]);
        WP_Mock::userFunction('__', ['times' => 1])->andReturn('Get Help');

        $menu->addMenuItem();

        $this->assertConditionsMet();
    }

    private function getInstance()
    {
        return new class extends GetHelpMenu {
            /** @noinspection PhpMissingParentConstructorInspection */
            public function __construct()
            {
            }
        };
    }
}
