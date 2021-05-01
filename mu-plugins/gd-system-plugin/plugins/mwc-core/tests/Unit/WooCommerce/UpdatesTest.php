<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\WooCommerce;

use Exception;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedExtensionsRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Updates;
use ReflectionException;
use WP_Mock;

/**
 * Provides tests for the {@see Updates}.
 */
final class UpdatesTest extends WPTestCase
{
    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Updates::addPluginExtensionsToUpdatesList()
     * @throws Exception
     */
    public function testCanAddPluginExtensionsToUpdatesList()
    {
        $installed = (new PluginExtension())
            ->setBasename('update/update.php')
            ->setSlug('update')
            ->setVersion('1.5.0');

        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')
            ->andReturnNull();
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedExtensions')
            ->andReturn([$installed]);

        WP_Mock::userFunction('get_plugins')
            ->andReturn([
                'update/update.php'       => $installed,
                'no-update/no-update.php' => (new PluginExtension())
                    ->setBasename('no-update/no-update.php')
                    ->setSlug('no-update')
                    ->setVersion('2.3.1'),
            ]);

        $list = (new Updates())->addPluginExtensionsToUpdatesList((object) [
            'checked'  => ['update/update.php' => '1.4.2', 'no-update/no-update.php' => '2.3.1'],
            'response' => ['no-update/no-update.php' => 'not-modified'],
        ]);

        $this->assertSame($installed->getVersion(), $list->response['update/update.php']->new_version);
        $this->assertSame('not-modified', $list->response['no-update/no-update.php']);
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Updates::addThemeExtensionsToUpdatesList()
     * @throws Exception
     */
    public function testCanAddThemeExtensionsToUpdatesList()
    {
        $installed = (new ThemeExtension())
            ->setSlug('update')
            ->setVersion('1.5.0');

        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')
            ->andReturnNull();
        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedExtensions')
            ->andReturn([$installed]);

        WP_Mock::userFunction('wp_get_themes')
            ->andReturn([
                'update'    => $installed,
                'no-update' => (new ThemeExtension())
                    ->setSlug('no-update')
                    ->setVersion('2.3.1'),
            ]);

        $list = (new Updates())->addThemeExtensionsToUpdatesList((object) [
            'checked'  => ['update' => '1.4.2', 'no-update' => '2.3.1'],
            'response' => ['no-update' => 'not-modified'],
        ]);

        $this->assertSame($installed->getVersion(), ArrayHelper::get($list->response, 'update.new_version'));
        $this->assertSame('not-modified', ArrayHelper::get($list->response, 'no-update'));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Updates::hideWooExtensionDetailsLinks()
     * @throws Exception
     */
    public function testCanHideWooExtensionDetailsLinksOnUpdatesPage()
    {
        ArrayHelper::set($GLOBALS, 'pagenow', 'update-core.php');

        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        WP_Mock::userFunction('wp_kses_post')->andReturnArg(0);
        WP_Mock::userFunction('get_plugins')
            ->andReturn([
                'plugin-1/plugin-1.php' => ['name' => 'Test Plugin 1'],
                'plugin-2/plugin-2.php' => ['name' => 'Test Plugin 1'],
            ]);

        $installed = [
            (new PluginExtension())
                ->setSlug('plugin-1')
                ->setBasename('plugin-1/plugin-1.php'),
            (new PluginExtension())
                ->setSlug('plugin-2')
                ->setBasename('plugin-2/plugin-2.php'),
        ];

        $this->mockStaticMethod(ManagedExtensionsRepository::class, 'getManagedPlugins')
            ->andReturn($installed);

        (new Updates())->hideWooExtensionDetailsLinks();

        // matches if the string contains the slug of every installed plugin
        $pattern = implode('.*', array_map(function (PluginExtension $plugin) {
            return preg_quote($plugin->getSlug(), '/');
        }, $installed));

        $this->expectOutputRegex("/text\/css.*$pattern/s");
    }

    /**
     * @covers       \GoDaddy\WordPress\MWC\Core\WooCommerce\Updates::isUpdateListValid()
     *
     * @param $list
     * @param bool $expected
     *
     * @throws ReflectionException
     * @dataProvider providerIsValidUpdateList
     */
    public function testCanDetermineIsValidUpdateList($list, bool $expected)
    {
        $this->mockWordPressGetOption(['gd_mwc_disable_woocommerce_marketplace_suggestions', 'yes'], 'yes');

        $updatesClass = new Updates();
        $updates = TestHelpers::getStaticHiddenMethod($updatesClass, 'isUpdateListValid');

        $this->assertEquals($expected, $updates->invoke($updatesClass, $list));
    }

    /** @see testCanDetermineIsValidUpdateList */
    public function providerIsValidUpdateList() : array
    {
        return [
            [[1], false],
            [['checked'], false],
            [['checked' => []], false],
            [(object) ['checked' => []], true],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\Updates::hideWooExtensionDetailsLinks()
     */
    public function testCanHideWooExtensionDetailsLinksOnPluginsPageCreatesNamesArray()
    {
        // @TODO: Need to come back and figure the best way to assert that the correct names are there probably by overwriting attachInlineScriptVariables to ensure the names are passed in
        $this->assertConditionsMet();
    }
}
