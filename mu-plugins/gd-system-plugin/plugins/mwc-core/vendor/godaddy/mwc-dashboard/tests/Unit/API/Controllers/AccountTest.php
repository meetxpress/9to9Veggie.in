<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController;
use ReflectionException;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController
 */
final class AccountTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new AccountController();
        $routeProperty = TestHelpers::getInaccessibleProperty($controller, 'route');

        $this->assertSame('account', $routeProperty->getValue($controller));
    }

    /**
     * Tests the AccountController::getItem() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getItem()
     */
    public function testCanGetItem()
    {
        WP_Mock::userFunction('current_user_can')->andReturn(false);

        WP_Mock::userFunction('rest_ensure_response')
            ->once()
            ->andReturnArg(0);

        $this->assertSame([
            'account' => [
                'privateLabelId'      => null,
                'isVersioningManual'  => false,
                'isOnResellerAccount' => false,
                'permissions'         => [
                    'INSTALL_PLUGINS' => false,
                ],
            ],
        ], (new AccountController())->getItem());
    }

    /**
     * Tests that the private label ID is returned based on configuration.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getItem()
     *
     * @throws Exception
     */
    public function testCanGetPrivateLabelIdStatus()
    {
        WP_Mock::userFunction('current_user_can')->andReturn(false);

        WP_Mock::userFunction('rest_ensure_response')
            ->once()
            ->andReturnArg(0);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'getResellerId')
            ->once()
            ->andReturn(2);

        $this->assertSame(2, ArrayHelper::get((new AccountController())->getItem(), 'account.privateLabelId'));
    }

    /**
     * Tests that the manual versioning flag is returned based on configuration.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getItem()
     *
     * @throws Exception
     */
    public function testCanGetManualVersioningStatus()
    {
        WP_Mock::userFunction('current_user_can')->andReturn(false);

        WP_Mock::userFunction('rest_ensure_response')
            ->once()
            ->andReturnArg(0);

        $this->mockStaticMethod(Configuration::class, 'get')
            ->andReturn(true)
            ->matchArgs([
                'features.extensions.versionSelect',
            ]);

        $this->assertTrue(ArrayHelper::get((new AccountController())->getItem(), 'account.isVersioningManual'));
    }

    /**
     * Tests that the reseller status is returned based on configuration.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getItem()
     *
     * @throws Exception
     */
    public function testCanGetResellerStatus()
    {
        WP_Mock::userFunction('current_user_can')->andReturn(false);

        WP_Mock::userFunction('rest_ensure_response')
            ->once()
            ->andReturnArg(0);

        $this->mockStaticMethod(ManagedWooCommerceRepository::class, 'isReseller')
            ->once()
            ->andReturn(true);

        $this->assertTrue(ArrayHelper::get((new AccountController())->getItem(), 'account.isOnResellerAccount'));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getAccountPermissions()
     *
     * @param bool $permission expected permission status
     * @param bool $install_plugins whether the user can install plugins
     * @param bool $activate_plugins whether the user can activate plugins
     *
     * @dataProvider provideGetInstallPluginsPermissionData
     */
    public function testCanGetInstallPluginsPermission($permission, $install_plugins, $activate_plugins)
    {
        WP_Mock::userFunction('current_user_can')->with('install_plugins')->andReturn($install_plugins);
        WP_Mock::userFunction('current_user_can')->with('activate_plugins')->andReturn($activate_plugins);

        $method = TestHelpers::getInaccessibleMethod(AccountController::class, 'getAccountPermissions');

        $this->assertSame($permission, ArrayHelper::get($method->invoke(new AccountController()), 'INSTALL_PLUGINS'));
    }

    /** @see testCanGetInstallPluginsPermission() */
    public function provideGetInstallPluginsPermissionData()
    {
        return [
            [true, true, true],
            [false, true, false],
            [false, false, true],
            [false, false, false],
        ];
    }

    /**
     * Tests the AccountController::getItemSchema() method.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController::getItemSchema()
     */
    public function testCanGetItemSchema()
    {
        WP_Mock::userFunction('__')->andReturn('translated');

        $schema = (new AccountController())->getItemSchema();

        $this->assertSame('account', $schema['title']);
        $this->assertSame('object', $schema['type']);

        // test the actual structure so a red flag is raised if it's ever changed accidentally
        $this->assertSame([
            'privateLabelId' => [
                'description' => 'translated',
                'type'        => 'int',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'isVersioningManual' => [
                'description' => 'translated',
                'type'        => 'bool',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'isOnResellerAccount' => [
                'description' => 'translated',
                'type'        => 'bool',
                'context'     => ['view', 'edit'],
                'readonly'    => true,
            ],
            'permissions' => [
                'type'        => 'object',
                'properties'  => [
                    'INSTALL_PLUGINS' => [
                        'description' => __('Whether the account can install and activate plugins.', 'mwc-dashboard'),
                        'type'        => 'bool',
                        'context'     => ['view', 'edit'],
                        'readonly'    => true,
                    ],
                ],
            ]
        ], $schema['properties']);
    }
}
