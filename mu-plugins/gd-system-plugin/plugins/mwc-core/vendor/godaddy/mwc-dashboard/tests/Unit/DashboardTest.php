<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\API;
use GoDaddy\WordPress\MWC\Dashboard\Dashboard;
use GoDaddy\WordPress\MWC\Dashboard\Menu\GetHelpMenu;
use GoDaddy\WordPress\MWC\Dashboard\Pages\WooCommerceExtensionsPage;
use GoDaddy\WordPress\MWC\Dashboard\Tests\TestHelpers as DashboardTestHelpers;
use Mockery;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard
 */
class DashboardTest extends WPTestCase
{
    /**
     * Sets up the tests.
     *
     * @throws Exception
     */
    public function setUp() : void
    {
        parent::setUp();

        Configuration::set('wordpress.absolute_path', 'foo/bar');
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::initializeConfiguration()
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function testInitializeConfiguration()
    {
        $this->sharedMocks();
        DashboardTestHelpers::mockWordPressTranslation('Get Help', 'mwc-dashboard');

        $dashboard = Dashboard::getInstance();
        $method = TestHelpers::getInaccessibleMethod(Dashboard::class, 'initializeConfiguration');

        $dashboardConfigurationDirectoryPath = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'configurations');

        WP_Mock::userFunction('__');

        $this->mockStaticMethod(Configuration::class, 'initialize')
            ->once()
            ->withArgs([$dashboardConfigurationDirectoryPath]);

        $method->invoke($dashboard);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::getClassesToInstantiate
     *
     * @param bool $hasEcommercePlan
     * @param bool $shouldDisplayGetHelpPage
     * @param array $expectedClasses
     *
     * @throws \ReflectionException
     *
     * @dataProvider providerGetClassesToInstantiate
     */
    public function testGetClassesToInstantiate(bool $hasEcommercePlan, bool $shouldDisplayGetHelpPage, array $expectedClasses)
    {
        $this->sharedMocks();
        WP_Mock::userFunction('__')->andReturnArg(0);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'hasEcommercePlan')
             ->andReturn($hasEcommercePlan);

        Configuration::set('features.mwc_dashboard', $shouldDisplayGetHelpPage);

        $dashboard = Dashboard::getInstance();
        $method = TestHelpers::getInaccessibleMethod(Dashboard::class, 'getClassesToInstantiate');
        $method->invoke($dashboard);

        $classesToInstantiateProperty = TestHelpers::getInaccessibleProperty(Dashboard::class, 'classesToInstantiate');

        $this->assertIsArray($classesToInstantiateProperty->getValue($dashboard));
        $this->assertEquals($expectedClasses, $classesToInstantiateProperty->getValue($dashboard));
    }

    /** @see testGetClassesToInstantiate */
    public function providerGetClassesToInstantiate() : array
    {
        return [
            [false, false, [] ],
            [true, false, [
                API::class                       => true,
                GetHelpMenu::class               => false,
                WooCommerceExtensionsPage::class => 'web',
            ]],
            [true, true, [
                API::class                       => true,
                GetHelpMenu::class               => 'web',
                WooCommerceExtensionsPage::class => 'web',
            ]],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::shouldDisplayGetHelpPage
     *
     * @param bool $isFeatureEnabled
     * @param bool $hasEcommercePlan
     * @param bool $isReseller
     * @param bool $isResellerWithSupportAgreement
     * @param bool $shouldDisplay
     *
     * @throws \ReflectionException
     *
     * @dataProvider providerShouldDisplayGetHelpPage
     */
    public function testShouldDisplayGetHelpPage( bool $isFeatureEnabled, bool $hasEcommercePlan, bool $isReseller, bool $isResellerWithSupportAgreement, bool $shouldDisplay )
    {
        $this->sharedMocks();
        DashboardTestHelpers::mockWordPressTranslation('Get Help', 'mwc-dashboard');

        $dashboard = Dashboard::getInstance();
        $method = TestHelpers::getInaccessibleMethod(Dashboard::class, 'shouldDisplayGetHelpPage');

        WP_Mock::userFunction('__');

        Configuration::set('features.mwc_dashboard', $isFeatureEnabled);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'hasEcommercePlan')
             ->andReturn($hasEcommercePlan);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
             ->andReturn($isReseller);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isResellerWithSupportAgreement')
             ->andReturn($isResellerWithSupportAgreement);

        $this->assertSame($shouldDisplay, $method->invoke($dashboard));
    }

    /** @see testShouldDisplayGetHelpPage */
    public function providerShouldDisplayGetHelpPage() : array
    {
        return [
            'feature disabled 0' => [false, false, false, false, false ],
            'feature disabled 1' => [false, false, false, true, false ],
            'feature disabled 2' => [false, false, true, false, false ],
            'feature disabled 3' => [false, false, true, true, false ],
            'feature disabled 4' => [false, true, false, false, false ],
            'feature disabled 5' => [false, true, false, true, false ],
            'feature disabled 6' => [false, true, true, false, false ],
            'feature disabled 7' => [false, true, true, true, false ],
            'non MWC 0' => [true, false, false, false, false ],
            'non MWC 1' => [true, false, false, true, false ],
            'non MWC 2' => [true, false, true, false, false ],
            'non MWC 3' => [true, false, true, true, false ],
            'reseller without support agreement' => [true, true, true, false, false ],
            'reseller with support agreement' => [true, true, true, true, true ],
            'non reseller' => [true, true, false, false, true ],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::deactivateSkyVergeDashboardPlugin
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::deactivateSkyVergeDashboard
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::displayAdminNoticeForSkyVergeDashboardPlugin
     *
     * @throws ReflectionException
     */
    public function testDeactivateSkyVergeDashboardPlugin()
    {
        $this->sharedMocks();
        DashboardTestHelpers::mockWordPressTranslation('Get Help', 'mwc-dashboard');

        $dashboard = Dashboard::getInstance();
        $method = TestHelpers::getInaccessibleMethod(Dashboard::class, 'deactivateSkyVergeDashboardPlugin');

        $deactivateFunctionMock = WP_Mock::userFunction('deactivate_plugins')->times(0);

        $method->invoke($dashboard);

        $deactivateFunctionMock->times(1)
            ->withArgs(['skyverge-dashboard/skyverge-dashboard.php']);

        Mockery::declareClass('SkyVerge_Dashboard_Loader');

        $method->invoke($dashboard);

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::renderAdminNoticeForSkyVergeDashboardPlugin
     */
    public function testCanRenderAdminNoticeForSkyVergeDashboardPlugin()
    {
        $this->sharedMocks();
        WP_Mock::userFunction('__')->andReturn('<div class="notice notice-info is-dismissible"><p>');

        ob_start();

        Dashboard::getInstance()->renderAdminNoticeForSkyVergeDashboardPlugin();

        $this->assertStringContainsString('class="notice notice-info is-dismissible"', ob_get_clean());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Dashboard::getConfigurationValues
     *
     * @throws ReflectionException
     */
    public function testGetConfigurationValues()
    {
        $this->sharedMocks();
        WP_Mock::userFunction('__');

        $dashboard = Dashboard::getInstance();
        $method = TestHelpers::getInaccessibleMethod(Dashboard::class, 'getConfigurationValues');
        $values = $method->invoke($dashboard);

        $this->assertIsArray($values);
        $this->assertArrayHasKey('PLUGIN_DIR', $values);
        $this->assertArrayHasKey('PLUGIN_URL', $values);
        $this->assertArrayHasKey('VERSION', $values);
    }

    /**
     * Shared mocks to ensure they don't persist set up past a single test.
     */
    protected function sharedMocks()
    {
        $this->mockWordPressTransients();

        WP_Mock::userFunction('load_plugin_textdomain');
        WP_Mock::userFunction('plugin_basename')->andReturn('mwc-dashboard/src/dashboard.php');
        WP_Mock::userFunction('plugin_dir_url')->andReturn('https://demo.com/wp-content/dashboards/mwc-dashboard');

        DashboardTestHelpers::mockRegisterActionCalls();
    }
}
