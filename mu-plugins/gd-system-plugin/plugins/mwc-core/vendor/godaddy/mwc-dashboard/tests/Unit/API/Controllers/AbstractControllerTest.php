<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController
 */
final class AbstractControllerTest extends WPTestCase
{
    /**
     * Tests the getItemsPermissionsCheck() method.
     *
     * @param bool $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController::getItemsPermissionsCheck()
     * @dataProvider providerGetItemsPermissionsCheck
     */
    public function testGetItemsPermissionsCheck(bool $expected)
    {
        \WP_Mock::userFunction('current_user_can')
               ->withArgs(['manage_woocommerce'])
               ->andReturn($expected);

        $controllers = new TestController();

        $this->assertSame($expected, $controllers->getItemsPermissionsCheck());
    }

    /** @see testGetItemsPermissionsCheck() */
    public function providerGetItemsPermissionsCheck() : array
    {
        return [
            'user can manage WC'    => [true],
            'user cannot manage WC' => [false],
        ];
    }

    /**
     * Tests the createItemPermissionsCheck() method.
     *
     * @param bool $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController::createItemPermissionsCheck()
     * @dataProvider providerCreateItemPermissionsCheck
     */
    public function testCreateItemPermissionsCheck(bool $expected)
    {
        \WP_Mock::userFunction('current_user_can')
               ->withArgs(['manage_woocommerce'])
               ->andReturn($expected);

        $controllers = new TestController();

        $this->assertSame($expected, $controllers->createItemPermissionsCheck());
    }

    /** @see testCreateItemPermissionsCheck() */
    public function providerCreateItemPermissionsCheck() : array
    {
        return [
            'user can manage WC'    => [true],
            'user cannot manage WC' => [false],
        ];
    }

    /**
     * Tests the updateItemPermissionsCheck() method.
     *
     * @param bool $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController::updateItemPermissionsCheck()
     * @dataProvider providerUpdateItemPermissionsCheck
     */
    public function testUpdateItemPermissionsCheck(bool $expected)
    {
        \WP_Mock::userFunction('current_user_can')
               ->withArgs(['manage_woocommerce'])
               ->andReturn($expected);

        $controllers = new TestController();

        $this->assertSame($expected, $controllers->updateItemPermissionsCheck());
    }

    /** @see testUpdateItemPermissionsCheck() */
    public function providerUpdateItemPermissionsCheck() : array
    {
        return [
            'user can manage WC'    => [true],
            'user cannot manage WC' => [false],
        ];
    }

    /**
     * Tests the deleteItemPermissionsCheck() method.
     *
     * @param bool $expected
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController::deleteItemPermissionsCheck()
     * @dataProvider providerDeleteItemPermissionsCheck
     */
    public function testDeleteItemPermissionsCheck(bool $expected)
    {
        \WP_Mock::userFunction('current_user_can')
               ->withArgs(['manage_woocommerce'])
               ->andReturn($expected);

        $controllers = new TestController();

        $this->assertSame($expected, $controllers->deleteItemPermissionsCheck());
    }

    /** @see testDeleteItemPermissionsCheck() */
    public function providerDeleteItemPermissionsCheck() : array
    {
        return [
            'user can manage WC'    => [true],
            'user cannot manage WC' => [false],
        ];
    }
}
