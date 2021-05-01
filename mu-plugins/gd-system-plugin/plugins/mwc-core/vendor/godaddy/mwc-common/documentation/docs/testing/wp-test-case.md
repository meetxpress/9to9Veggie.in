---
id: wp-test-case
title: WPTestCase
---

The MWC Common library uses PHPUnit for unit testing. While the aim is to have all tests as unit tests, there is often the need to mock a WordPress function or installation variable. For this reason, to avoid having to run a WordPress instance, we rely on [WP_Mock](https://github.com/10up/wp_mock). At the same time, the MWC Common library includes a set of test helpers that extend WP_Mock which could be reused by projects that bundle this library.

## Base class

The `WPTestCase` can be used as a base class for building tests in place of PHPUnit's own vanilla `TestCase`, which is extended by `WPTestCase`. 

```php
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

class MyClassTest extends WPTestCase 
{    
    public function testSomething() : void 
    {
        // all methods from PHPUnit TestCase will also be available
        $this->assertConditionsMet();    
    }
}
```

Below there is a list of methods that are unique to `WPTestCase` for MWC testing.

### mockStaticMethod

An improvement over `WP_Mock` own method. Uses [Mockery](https://github.com/mockery/mockery) to mock a static method.

```php
$this->mockStaticMethod(MyStaticHandler::class, 'myStaticMethod')->andReturn(true);
```

### mockWordPressGetOption

Will mock a WordPress `get_option()` function call with an expected result.

```php
// mocks a call to `get_option('foo')` expected to return `bar` 
// mocked return values can be of any type expected for `get_option()` to return
$this->mockWordPressGetOption('foo', 'bar');
```

### mockWordPressPluginFunctions

Mocks a WordPress plugin activation.

```php
$this->mockWordPressPluginFunctions('myPlugin', null);
```

### mockWordPressRequestFunctions

Used to mock a WordPress request call with `wp_remote_request()`. Use the method arguments to mock the response.

```php
// mocks a request resulting in a 200 success response with foo => bar json encoded body
$this->mockWordPressRequestFunctions(200, ['foo' => 'bar']);
// mocks a request resulting in a 400 error response as `WP_Error`
$this->mockWordPressRequestFunctions(400, [], true);
```

### mockWordPressRequestFunctionsWithArgs

A variant of the previous helper method, this method will mock a request with `wp_remote_request()` but will accept an array of arguments only to pass to the WordPress function.

```php
$this->mockWordPressRequestFunctionsWithArgs( [
    'url' => 'https://example.com', // passed to `wp_remote_request()` 
    'response' => [
        'code' => 200,
        'body' => [
            // define here the response body properties and values that will be returned as a JSON body
        ]
    ],
]);
```

### mockWordPressResponseFunctions

Will mock expected WordPress response functions, such as:

* `is_wp_error()`
* `wp_remote_retrieve_body()`
* `wp_remote_retrieve_response_code()`

```php
$this->mockWordPressResponseFunctions();
MyHandler::methodThatExpectsWPResponseFunctions();
```

### mockWordPressScriptFunctions

Will mock expected WordPress scripts handling functions:

* `wp_register_script()`
* `wp_enqueue_script()`
* `wp_add_inline_script()`

```php
$this->mockWordPressScriptFunctions();
MyHandler::methodThatUsesWPScriptFunctions();
```

### mockWordPressTransients

Mocks expected WordPress transient functions.

* `get_transient()`
* `set_transient()`
* `delete_transient()`

```php
$this->mockWordPressTransients();
MyHandler::methodThatExpectsWPTransients();
```