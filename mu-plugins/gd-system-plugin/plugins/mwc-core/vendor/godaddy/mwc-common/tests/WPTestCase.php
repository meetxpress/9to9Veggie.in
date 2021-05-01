<?php

namespace GoDaddy\WordPress\MWC\Common\Tests;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use Mockery;
use PHPUnit\Util\Test;
use ReflectionMethod;
use WP_Mock;
use WP_Mock\Tools\TestCase;

class WPTestCase extends TestCase
{
    /**
     * Set up function.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->mockConfigurationOptions();
        $this->mockConfigurationTransients();

        // clear configuration cache to force each test to define the expected configuration
        Configuration::clear();
    }

    /**
     * Backported from PHPUnit 9.4 TestCase class.
     *
     * WP_Mock's TestCase expects that method to be present on {@see setUpContentFiltering()}.
     *
     * @return array
     */
    public function getAnnotations() : array
    {
        return Test::parseTestMethodAnnotations(
            static::class,
            $this->getName(false)
        );
    }

    /**
     * Mock a static method of a class.
     *
     * Copied from {@see WP_Mock\Tools\TestCase::mockStaticMethod()}.
     *
     * @param string $class  The classname or class::method name
     * @param null|string $method The method name. Optional if class::method used for $class
     *
     * @return \Mockery\Expectation
     * @throws Exception
     */
    protected function mockStaticMethod($class, $method = null)
    {
        if (! $method) {
            list($class, $method) = (explode('::', $class) + [null, null]);
        }
        if (! $method) {
            throw new Exception(sprintf('Could not mock %s::%s', $class, $method));
        }
        if (! WP_Mock::usingPatchwork() || ! function_exists('Patchwork\redefine')) {
            throw new Exception('Patchwork is not loaded! Please load patchwork before mocking static methods!');
        }

        $safe_method = "wp_mock_safe_$method";
        $signature = md5("$class::$method");

        if (! empty($this->mockedStaticMethods[$signature])) {
            $mock = $this->mockedStaticMethods[$signature];
        } else {
            $rMethod = false;
            if (class_exists($class)) {
                $rMethod = new ReflectionMethod($class, $method);
            }
            if (
                $rMethod &&
                (
                    ! $rMethod->isUserDefined() ||
                    ! $rMethod->isStatic() ||
                    $rMethod->isPrivate()
                )
            ) {
                throw new Exception(sprintf('%s::%s is not a user-defined non-private static method!', $class, $method));
            }

            /** @var \Mockery\Mock $mock */
            $mock = Mockery::mock($class);
            $mock->shouldAllowMockingProtectedMethods();
            $this->mockedStaticMethods[$signature] = $mock;

            \Patchwork\redefine("$class::$method", function () use ($mock, $safe_method) {
                return call_user_func_array([$mock, $safe_method], func_get_args());
            });
        }

        $expectation = $mock->shouldReceive($safe_method);

        return $expectation;
    }

    /**
     * Mock option function calls involved in getting and setting configuration values.
     *
     * It's important to mock the functions with parameters expectations to allow tests to mock other calls to each of the transient functions if necessary.
     */
    public function mockConfigurationOptions()
    {
        WP_Mock::userFunction('get_option')->with('gd_mwc_site_id', '')->andReturnFalse();
    }

    /**
     * Mock transient function calls involved in getting and setting configuration values.
     *
     * It's important to mock the functions with parameters expectations to allow tests to mock other calls to each of the transient functions if necessary.
     */
    public function mockConfigurationTransients()
    {
        WP_Mock::userFunction('set_transient')
            ->with('gd_configurations', Mockery::any(), Mockery::any())
            ->andReturnTrue();

        WP_Mock::userFunction('get_transient')->with('gd_configurations')->andReturnFalse();
        WP_Mock::userFunction('delete_transient')->with('gd_configurations')->andReturnTrue();
    }

    /**
     * Mock a WordPress' @see {get_option()} call.
     *
     * @param string|array $options
     * @param mixed|null $returnValue
     */
    protected function mockWordPressGetOption($options, $returnValue = null)
    {
        WP_Mock::userFunction('get_option')
            ->withArgs(ArrayHelper::wrap($options))
            ->andReturn($returnValue);
    }

    /**
     * Mocks WordPress Plugin functions.
     *
     * @param string $pluginName
     * @param mixed $returnValue
     */
    protected function mockWordPressPluginFunctions(string $pluginName, $returnValue)
    {
        WP_Mock::userFunction('activate_plugin')
            ->withArgs([$pluginName])
            ->andReturn($returnValue);
    }

    /**
     * Mocks WordPress request functions.
     *
     * @TODO: update method to take an array of args to increase the range of request fields that can be mocked {WV 2020-12-18}
     *
     * @param int $statusCode
     * @param string|array $body
     * @param bool $error
     */
    protected function mockWordPressRequestFunctions(int $statusCode = 200, $body = null, bool $error = false)
    {
        if (null === $body) {
            $body = ['products' => [
                ['name' => 'test'],
                ['name' => 'WooCommerce test'],
                ['name' => 'test WooCommerce test'],
                ['name' => 'test WooCommerce'],
            ]];
        }

        $this->mockWordPressResponseFunctions($statusCode, $body, $error);

        WP_Mock::userFunction('wp_remote_request')->andReturn([
            'headers'  => '',
            'body'     => json_encode($body),
            'response' => [
                'code'    => $statusCode,
            ],
        ]);
    }

    /**
     * Mocks WordPress request functions.
     *
     * @TODO: should we replace mockWordPressRequestFunctions with this implementation {WV 2020-12-19}
     *
     * @param array $args expected function arguments and return values
     */
    protected function mockWordPressRequestFunctionsWithArgs(array $args)
    {
        $url = ArrayHelper::get($args, 'url');

        $code = ArrayHelper::get($args, 'response.code', 200);

        $body = ArrayHelper::get($args, 'response.body', [
            'products' => [
                ['name' => 'test'],
                ['name' => 'WooCommerce test'],
                ['name' => 'test WooCommerce test'],
                ['name' => 'test WooCommerce'],
            ],
        ]);

        $this->mockWordPressResponseFunctions($code, $body, ArrayHelper::get($args, 'error', false));

        WP_Mock::userFunction('wp_remote_request', [
            'args' => [
                // expect the first parameter for wp_remote_request() to be a URL that includes the URL in the args array
                function ($arg) use ($url) {
                    if ($url) {
                        return false !== strpos($arg, $url);
                    }

                    return true;
                },
                '*',
            ],
        ])->andReturn([
            'headers'  => ArrayHelper::get($args, 'response.headers', []),
            'body'     => json_encode($body),
            'response' => [
                'code' => $code,
            ],
        ]);
    }

    /**
     * Mocks WordPress response functions.
     *
     * @param int $statusCode
     * @param string|array $body
     * @param bool $error
     */
    protected function mockWordPressResponseFunctions(int $statusCode = 200, $body = 'success', bool $error = false)
    {
        WP_Mock::userFunction('is_wp_error')->andReturn($error);
        WP_Mock::userFunction('wp_remote_retrieve_body')->andReturn(json_encode($body));
        WP_Mock::userFunction('wp_remote_retrieve_response_code')->andReturn($statusCode);
    }

    /**
     * Mocks WordPress script functions.
     */
    protected function mockWordPressScriptFunctions()
    {
        WP_Mock::userFunction('wp_enqueue_script')->andReturnNull();
        WP_Mock::userFunction('wp_register_script')->andReturnNull();
        WP_Mock::userFunction('wp_add_inline_script')->andReturnNull();
    }

    /**
     * Mocks WordPress transient functions.
     */
    protected function mockWordPressTransients()
    {
        WP_Mock::userFunction('delete_transient')->andReturnTrue();
        WP_Mock::userFunction('get_transient')->andReturnNull();
        WP_Mock::userFunction('set_transient')->andReturnNull();
    }

    /**
     * Mocks a WordPress add action call.
     *
     * @TODO consider dropping this method in favor of {@see WP_Mock::expectActionAdded()} {FN 2020-01-08}.
     *
     * @param string $group
     * @param string|array|Closure $handler
     * @param int|null $priority
     * @param int|null $arguments
     */
    protected function mockWooCommerceAction(string $group, $handler, $priority = null, $arguments = null)
    {
        WP_Mock::expectActionAdded($group, $handler, $priority ?? 10, $arguments ?? 1);
    }

    /**
     * Mocks a WordPress add filter call.
     *
     * @TODO consider dropping this method in favor of {@see WP_Mock::expectFilterAdded()} {FN 2020-01-08}.
     *
     * @param string $group
     * @param string|array|Closure $handler
     * @param int|null $priority
     * @param int|null $arguments
     */
    protected function mockWooCommerceFilter(string $group, $handler, $priority = null, $arguments = null)
    {
        WP_Mock::expectFilterAdded($group, $handler, $priority ?? 10, $arguments ?? 1);
    }
}
