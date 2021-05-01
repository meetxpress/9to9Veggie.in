---
id: enqueue
title: Enqueue
---

The `Enqueue` component provides a standardized, agnostic, performant, and flexible way to add assets to a given page or project by ultimately injecting the appropriate html tags into the resulting DOM.

The component abstracts away the concern for an engineer to remember the specific workflows of a given asset type and provides them an easy way to simply declare it should be added.

## Base Class

The base class should generally not be used to enqueue items, but is available for the edge cases where it may make sense.  Generally speaking the base enqueue class offer a common entry point into all DOM asset inclusion with a shared interface for each [enqueue type](/components/enqueue#available-types) to inherit or overwrite.

### Asset Handle

It is required for every asset to have a name or a handle. This can be set via `setHandle`.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

Enqueue::script()->setHandle('myScript');

Enqueue::style()->setHandle('myStylesheet');
```

### Asset Source

It is required that you either provide a location to the asset or include it [inline](/components/enqueue#include-inline).  To include a file by its location you must include its path to the `setSource` method.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// external file
Enqueue::script()->setSource('https://mysite.com/file.js');

// internal file
Enqueue::script()->setSource('assets/filt.js');
```

### Defer to Footer

The base class and inheriting [enqueue types](/components/enqueue#available-types) generally default to placing the item in either the header or footer, but if you need to handle a specific instance differently you may explicitly declare the behavior.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// place in header
Enqueue::script()->setDeferred(false);

// place in footer
Enqueue::script()->setDeferred(true);
```

### Declare Dependencies

If a given [enqueue type](/components/enqueue#available-types) requires another asset to be loaded before it can be included, you may explicitly declare those beyond what is provided by that type by default.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

Enqueue::script()->setDependencies(['jquery-ui']);

Enqueue::style()->setDependencies(['jquery-ui', 'myStylesheet']);
```

### Set File Version

You may set a specific version on the asset to ensure browsers properly handle potential conflicts and/or recognize when a file should be reloaded.  The version will be `null` by default.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// Set the version
Enqueue::script()->setVersion('1.1.1');
```

### Enqueue condition

You may optionally set a condition to enqueue an asset with `setCondition` method. The condition must be a `callable` type. If the condition returns true the asset will be enqueued, otherwise it will be not even if you call `execute`.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// the callable is a method within the current class
Enqueue::script()->setCondition([$this, 'method']);

// the callable is a Closure
Enqueue::style()->setCondition(function() { return true; });
```

_Note: if necessary a condition, once set, can be removed using the `removeCondition` method._

## Additional Methods

### Set Media Type

The `EnqueueStyle` type supports setting a media type.

You may set a specific media type tag for the asset. The type will be `all` by default.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// set the version
Enqueue::style()->setMedia('print');
```

### Inline Object

The `EnqueueScript` type supports appending one inline JavaScript object to the DOM via the methods `attachInlineScriptObject` `attachInlineScriptVariables`.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// will attach an inline object called `objectName`
Enqueue::script()->attachInlineScriptObject('objectName');

// will append to the object the following properties defined in the array keys with values defined in the array values
Enqueue::script()->attachInlineScriptVariables([
    'myVar' => 1,
    'myOtherVar' => [
        'key' => 'value'
    ]   
]);
```

## Available Types
:::note IDE Help
IDEs with Intellisense will provide the available Enqueue types without checking this documentation by starting the static declaration `Enqueue::` providing a seamless dev experience and abstracting out the requirement of knowledge of individual class type operations.
:::

| Type   	| Key    	|
|--------	|--------	|
| Script 	| script 	|
| Style  	| style  	|

### Creating a Type
While the general enqueue base operations provide use case coverage for most situations, individual enqueue types may need to customize behavior for simple items like the validation to more advanced behavior registry execution.  Types should inherit the base class and overwrite those items which are specific to the individual type by extending the base [Enqueue](/components/enqueue#base-class) class.

In addition, all types should be an implementation of the `EnqueuableContract` to ensure the expected functionality is present.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Contracts\EnqueuableContract;
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

final class Enqueue{MyEnqueueType} extends Enqueue implements EnqueuableContract
{
  //
}
```

All enqueuable types must implement a `validate()` method to ensure the expected class states are set before execution and a `execute()` method to be called to perform the enqueue of the asset.  Enqueuable types always call execute:

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

Enqueue::script()
  ->setSource('http://mywebsite/file.js')
  ->execute();
```

## Static Instantiation
The Enqueue component is a **action class** meaning we typically perform the same end actions when an instance is present regardless of the state held within the class itself.  This is as opposed to a Data class which is very specific to each instance and each use case influences how it will be used.  As a result, we strive to provide a common entry point and interface for the Enqueue component such that engineers do not need to keep up with the specifics of every type given the specifics of that type are not relevant to usage.

This means you will see types statically instantiated from the base class entry point:

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// instantiate an instance of the script enqueuable
Enqueue::script();

// instantiate an instance of the style enqueuable
Enqueue::style();
```

## Execution

After all the properties of an enqueuable have been defined, the enqueue action can be triggered via `execute` method.

```php
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;

// enqueue a script with only the minimum required properties set
Enqueue::script()
    ->setHandle('myScript')
    ->setSource('https://example.com/myScript.js')
    ->execute();

// enqueue a style with only the minimum required properties set
Enqueue::style()
    ->setHandle('myStyle')
    ->setSource('https://example.com/myStyle.css')
    ->execute();
```