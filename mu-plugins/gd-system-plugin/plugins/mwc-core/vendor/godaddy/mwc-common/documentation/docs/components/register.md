---
id: register
title: Register
---

The `Register` component provides a standardized, agnostic, performant, and flexible way to register items with the broader project and related functionality.

The component abstracts away the concern for an engineer to remember how every item interacts with the broader project / system.  
You are able to simple tell the system an item needs to be registered with it without concern over the specifics of that execution.

## Base Class

The base class should generally not be used to register items, but is available for the edge cases where it may make sense. 
Generally speaking the base class offers a common entry point into all registration with a shared interface for each [register type](/components/register#available-types) to inherit or overwrite.

### Dynamic Registration

By default, all register types will execute when calling `execute()`, however we often only want to register an item if certain criteria is met.  
The `Register` component offers the ability to dynamically determine if an item should be registered without the need to spread conditions throughout the code.  
To dynamically determine if an item should be registered simply provide a function which returns a `boolean` at runtime.

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;

// if the callable returns true, the execution will complete
Register::action()
    ->setCondition(function() { return true; })
    ->execute();

// if the callable returns false, the execution will not complete
Register::action()
    ->setCondition(function() { return false; })
    ->execute();
```

_If for some reason you need to remove later, before execution, the register condition, you can do so via `removeCondition` method._

### Grouping Registration

We often want to common registration items together for easier identification and execution elsewhere within a project.  In WordPress and WooCommerce specifically groups are exposed such that other projects may manipulate their behavior.  You may declare a group association for a given registration instance by using the `setGroup()` method.

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;

// the action will be placed in 'some-group'
Register::action()->setGroup('some-group');
```

### Execution Priority

Oftentimes you may need more fine-grained control over when a registration loads in relation to its peers.  This is especially true since we may not know the registration queue stack due to other projects / code base additions.  You may explicitly declare a priority by providing an integer to the `priority()` method.  
This defaults to `null` and will be added to the bottom of the stack.

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;

// handlerOne will be executed first
Register::action()
    ->setGroup('my-group')
    ->setHandler([$this, 'handlerOne'])
    ->setPriority(10)
    ->execute();

// handlerTwo will be executed after
Register::action()
    ->setGroup('my-group')
    ->setHandler([$this, 'handlerTwo'])
    ->setPriority(20)
    ->execute();
```

### Deregistering

In some cases, it may be necessary to deregister the object in the register instance which is something like undoing an `execute()` call. This can be achieved by storing the register in an object and calling `deregister()` if necessary.

As a usage example, WordPress filters and actions sometimes may be added before a statement and removed after that.

```php
$action = Register::action()
    ->setGroup('my-group')
    ->setHandler([$this, 'handler'])
    ->setPriority(20)
    ->execute();

// plugin execution here
$something->callSomethingThatWillTriggerMyAction();

$action->deregister();
```

### Handler Arguments

You can set the number of arguments that are expected to be passed to the handler.

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;

// the handler must receive exactly 2 arguments
Register::action()
    ->setGroup('my-group')
    ->setHandler([$this, 'handler'])
    ->setArgumentsCount(2)
    ->execute();
```

## Available Types
:::note IDE Help
IDEs with Intellisense will provide the available Registration types without checking this documentation by starting the static declaration `Register::` providing a seamless dev experience and abstracting out the requirement of knowledge of individual class type operations.
:::

| Type   	| Key    	|
|--------	|--------	|
| Action 	| action 	|
| Filter 	| filter 	|

### Creating a Type

While the general the register base operations provide use case coverage for most situations, individual register types are needed to establish the execution of a given registration.  Types should inherit the base class and overwrite those items which are specific to the individual type by extending the base [Register](/components/register#base-class) class.

In addition, all types should be an implementation of the `RegistrableContract` to ensure the expected functionality is present.

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Register\Contracts\RegistrableContract;

final class Register{MyRegisterType} extends Register implements RegistrableContract
{
  //
}
```

Registration classes expect a `execute()` method for the final execution of the registration and a `validate()` method to ensure all required states are present to fulfil the `RegistrableContract`.

### Static Instantiation

The Register component is a **action class** meaning we typically perform the same end actions when an instance is present regardless of the state held within the class itself.  This is as opposed to a Data class which is very specific to each own instance and each use case influences how it will be used.  As a result, we strive to provide a common entry point and interface for the Register component such that engineers do not need to keep up with the specifics of every type given the specifics of that type are not relevant to usage.

This means you will see types statically instantiated from the base class entry point:

```php
use GoDaddy\WordPress\MWC\Common\Register\Register;

// instantiate an instance of the action registration
Register::action();

// instantiate an instance of the filter registration
Register::filter();
```
