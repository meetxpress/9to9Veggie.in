<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\UserController;
use GoDaddy\WordPress\MWC\Dashboard\Tests\TestHelpers as DashboardTestHelpers;
use GoDaddy\WordPress\MWC\Dashboard\Users\Permissions\ShowExtensionsRecommendationsPermission;
use Mockery;
use ReflectionException;
use WP_Mock;

use function Patchwork\always;
use function Patchwork\redefine;

/**
 * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User
 */
class UserTest extends WPTestCase
{
    /**
     * Tests the constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::__construct()
     *
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $controller = new UserController();
        $route = TestHelpers::getInaccessibleProperty($controller, 'route');
        $this->assertSame('me', $route->getValue($controller));
    }

    /**
     * Tests the getItemSchema() method is returning the correct required and optional arguments.
     *
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::getItemSchema()
     */
    public function testGetItemSchema()
    {
        WP_Mock::userFunction('__');

        $controller = new UserController();

        $args = $controller->getItemSchema();

        $this->assertIsArray($args);
        $this->assertNotNull(ArrayHelper::get($args, 'properties.user.properties.marketingPermissions.properties.SHOW_EXTENSIONS_RECOMMENDATIONS'));
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::registerRoutes()
     */
    public function testRegisterRoutes()
    {
        WP_Mock::userFunction('__');

        WP_Mock::userFunction('register_rest_route', ['times' => 1])
            ->with('godaddy/mwc/v1', '/me', Mockery::any());

        (new UserController())->registerRoutes();

        $this->assertConditionsMet();
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::getItem()
     */
    public function testCanGetItem()
    {
        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser(1));

        $permissions = $this->createMock(ShowExtensionsRecommendationsPermission::class);
        $permissions->expects($this->once())
            ->method('isAllowed')
            ->willReturn(true);

        $controller = $this->createPartialMock(UserController::class, ['getShowExtensionsRecommendationsPermission']);
        $controller->expects($this->once())
            ->method('getShowExtensionsRecommendationsPermission')
            ->willReturn($permissions);

        $expectedResponse = [
            'user' => [
                'id'                   => 1,
                'marketingPermissions' => [
                    'SHOW_EXTENSIONS_RECOMMENDATIONS' => true,
                ],
            ],
        ];

        WP_Mock::userFunction('rest_ensure_response')
            ->once()
            ->andReturnArg(0);

        $this->assertSame($expectedResponse, $controller->getItem());
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::updateItem()
     *
     * @dataProvider providerCanUpdateItem
     *
     * @param $inputValue
     * @param string $expectedPermissionsMethod
     * @param bool $expectedValue
     */
    public function testCanUpdateItem($inputValue, string $expectedPermissionsMethod, bool $expectedValue)
    {
        DashboardTestHelpers::mockWordPressRepositoryUser(DashboardTestHelpers::getMockWordPressUser(1));

        // Ensure the correct method is called on permissions class & that it returns the expected value
        $permissions = $this->createMock(ShowExtensionsRecommendationsPermission::class);
        $permissions->expects($this->once())->method($expectedPermissionsMethod);
        $permissions->expects($this->once())->method('isAllowed')->willReturn($expectedValue);

        // Provide the mocked permissions instance to the controller
        $controller = $this->createPartialMock(UserController::class, ['getShowExtensionsRecommendationsPermission']);
        $controller->expects($this->exactly(2))
            ->method('getShowExtensionsRecommendationsPermission')
            ->willReturn($permissions);

        $payload = [
            'marketingPermissions' => [
                'SHOW_EXTENSIONS_RECOMMENDATIONS' => $inputValue,
            ]
        ];

        $request = Mockery::mock('WP_REST_Request');
        $request->shouldReceive('get_json_params')->andReturn($payload);

        WP_Mock::userFunction('rest_ensure_response')->andReturnArg(0);

        $expectedResponse = [
            'user' => [
                'id'               => 1,
                'marketingPermissions' => [
                    'SHOW_EXTENSIONS_RECOMMENDATIONS' => $expectedValue,
                ],
            ],
        ];

        $this->assertSame($expectedResponse, $controller->updateItem($request));
    }

    /**
     * @see tests above
     *
     * @return array[]
     */
    public function providerCanUpdateItem() : array
    {
        return [
            [true, 'allow', true],
            ['yes', 'allow', true],
            [false, 'disallow', false],
        ];
    }

    /**
     * @covers \GoDaddy\WordPress\MWC\Dashboard\API\Controllers\User::prepareItem()
     * @throws ReflectionException
     */
    public function testCanPrepareItem()
    {
        WP_Mock::userFunction('metadata_exists')->andReturn(true);
        WP_Mock::userFunction('update_user_meta');
        WP_Mock::userFunction('get_user_meta');

        $key = 'user.marketingPermissions.SHOW_EXTENSIONS_RECOMMENDATIONS';
        $userId = 1;

        $controller = new UserController();
        $method = TestHelpers::getInaccessibleMethod(UserController::class, 'prepareItem');

        redefine(ShowExtensionsRecommendationsPermission::class.'::isAllowed', always(false));

        $item = $method->invoke($controller, $userId);
        $this->assertIsArray($item);
        $this->assertFalse(ArrayHelper::get($item, $key));

        redefine(ShowExtensionsRecommendationsPermission::class.'::isAllowed', always(true));

        $item = $method->invoke($controller, $userId);
        $this->assertIsArray($item);
        $this->assertTrue(ArrayHelper::get($item, $key));
    }
}
