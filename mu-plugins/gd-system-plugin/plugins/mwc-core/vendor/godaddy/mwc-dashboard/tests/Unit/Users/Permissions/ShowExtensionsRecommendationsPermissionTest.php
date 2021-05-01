<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Users\Permissions;

use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission;
use ReflectionClass;
use WP_Mock;

/**
 * Provides tests for the ShowExtensionsRecommendationsPermission class.
 *
 * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission
 */
final class ShowExtensionsRecommendationsPermissionTest extends WPTestCase
{
    /**
     * Prepares this test case.
     */
    public function setUp(): void
    {
        parent::setUp();

        WP_Mock::userFunction('metadata_exists', ['times' => 1]);
        WP_Mock::userFunction('update_user_meta');
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::__construct()
     */
    public function testCanInstantiate()
    {
        WP_Mock::userFunction('user_can')->with(1, 'install_plugins')->andReturnTrue();
        WP_Mock::userFunction('user_can')->with(1, 'activate_plugins')->andReturnTrue();

        $instance = new ShowExtensionsRecommendationsPermission(1);

        $reflection = new ReflectionClass($instance);

        $metaKeyProperty = $reflection->getProperty('metaKey');
        $metaKeyProperty->setAccessible(true);

        $this->assertNotNull($instance);
        $this->assertEquals('_mwc_marketing_permissions_show_extensions_recommendations', $metaKeyProperty->getValue($instance));

        // the permission is allowed by default
        $this->assertTrue($instance->isAllowed());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::allow()
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::isAllowed()
     */
    public function testCanAllow()
    {
        WP_Mock::userFunction('user_can')->with(1, 'install_plugins')->andReturnTrue();
        WP_Mock::userFunction('user_can')->with(1, 'activate_plugins')->andReturnTrue();

        $instance = new ShowExtensionsRecommendationsPermission(1);

        $this->assertTrue($instance->isAllowed());

        $returnedInstance = $instance->allow();

        $this->assertSame($returnedInstance, $instance);
        $this->assertTrue($instance->isAllowed());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::disallow()
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::isAllowed()
     */
    public function testCanDisallow()
    {
        WP_Mock::userFunction('user_can')->with(1, 'install_plugins')->andReturnTrue();
        WP_Mock::userFunction('user_can')->with(1, 'activate_plugins')->andReturnTrue();

        $instance = new ShowExtensionsRecommendationsPermission(1);

        $this->assertTrue($instance->isAllowed());

        $instance->allow();

        $this->assertTrue($instance->isAllowed());

        $returnedInstance = $instance->disallow();

        $this->assertSame($returnedInstance, $instance);
        $this->assertFalse($instance->isAllowed());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission::isAllowed()
     *
     * @param bool $expected expected return value for isAllowed()
     * @param string $metaValue current meta value
     * @param bool $canInstallPlugins whether the user can install plugins
     * @param bool $canActivatePlugins whether the user can activate plugins
     *
     * @dataProvider providerIsAllowedChecksWhetherUserCanInstallAndActivatePlugins
     */
    public function testIsAllowedChecksWhetherUserCanInstallAndActivatePlugins(bool $expected, string $metaValue, bool $canInstallPlugins, bool $canActivatePlugins)
    {
        WP_Mock::userFunction('user_can')->with(1, 'install_plugins')->andReturn($canInstallPlugins);
        WP_Mock::userFunction('user_can')->with(1, 'activate_plugins')->andReturn($canActivatePlugins);

        $instance = new ShowExtensionsRecommendationsPermission(1);

        $instance->setUserMeta($metaValue);

        $this->assertSame($expected, $instance->isAllowed());
    }

    /** @see testIsAllowedChecksWhetherUserCanInstallAndActivatePlugins() */
    public function providerIsAllowedChecksWhetherUserCanInstallAndActivatePlugins()
    {
        return [
            [ true, 'yes', true, true ],
            [ false, 'yes', false, true ],
            [ false, 'yes', true, false ],
            [ false, 'yes', false, false ],
            [ false, 'no', true, true ],
            [ false, 'no', false, true ],
            [ false, 'no', true, false ],
            [ false, 'no', false, false ],
        ];
    }
}
