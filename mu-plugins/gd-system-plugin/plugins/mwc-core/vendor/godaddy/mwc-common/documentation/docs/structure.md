---
id: structure
title: Directory Structure
---

## Overview

The provided core package structure is intended to provide a solid starting point for our current teams and future contributors as we expand our shared capabilities. You are free to propose changes or additions to the structure, but implementation will require group consensus / approval.

:::danger
Directory structure changes could have substantial impacts on deeply nested pieces of our projects.  Be sure to give deep thought to proposed changes and if they are really necessary.  Consider an alias namespace to preserve backwards compatibility.
:::

## Directories

### Cache Directory

The `cache` directory contains a base [Cache](/components/cache#base-class) class which allows for the standardization of cache handling for [defined cache types](components/cache#available-types).  All caching should generally be channeled through this class which offers test coverage out of the box and best practice standards agreed upon by the broader team.

### Enqueue Directory

The `enqueue` directory contains a base [Enqueue](/components/enqueue#base-class) class which allows for the standardization of including assets within a given page or code base.  You may find specific handling for [defined enqueue types](components/enqueue#available-types) in the [Enqueue](components/enqueue) component.  

All asset inclusions of common types (scripts, styles, etc) should generally be channeled through this class which offers test coverage out of the box and best practice standards agreed upon by the broader team.

### Exceptions Directory

The `exceptions` directory provides a modified Base Exceptions Handler which intercepts all exceptions thrown by children to modify their behavior.  Specifically it injects global context into the exceptions so that reporting has the information deemed most valuable for the team, as well as providing proper separation of handling for api versus web request responses when an exception is thrown.

In addition, the folder contains a few standard exceptions used in all projects.  Exceptions placed in this core directory for shared use should meet a high bar of common reusability as to not bloat the package simply for convenience.  If there are a few use cases, but the implementation of a given exception does not rise to the level of broad usage, consider a more generic exception and extending it.

### Extensions Directory

WordPress and WooCommerce offer a common way to extend their platforms via extensions, plugins, and themes.  Generally all platforms and frameworks offer a similar type of functionality.  The `extensions` directory is aimed at standardizing the handling of these functionalities to give engineers a known interface and toolset for controlling them.  

You may find specific handling for [defined extension types](/components/extension#available-types) in the [Extensions](/components/extension) component.

### Helpers Directory

The `helpers` directory contains classes which provide independent helper functionality.  In order to be a `Helper` class and included in this directory a class should be completely independent in its functionality with no related project dependencies.  Anotherwards the classes could be dropped into any project without changes and provide the same functionality.  

The classes within the `helpers` directory differ from a [repository](/structure#repositories-directory) based class where functionality is related or interacts with dependencies and is more heavily project based.

### Loggers Directory

The `loggers` directory contains share loggers used throughout our projects to standardize the reporting of exceptions or handling/reporting of events.

### Plugin Directory

The `plugin` directory contains base classes for creating future plugins in a standardized manner and optionally extendable shared functionality.  Additional plugin base offerings should always extend the main base plugin class and simply provide additional functionality and configuration out of the box that is useful in a reusable manner.  

:::note Should an item be included in this directory?
If the functionality or settings are specific only a single plugin then they should be implemented in that individual project and not in this shared core library.
:::

### Register Directory

The `register` directory contains a base [Register](/components/register#base-class) class which allows for the standardization of registering functionality upon instantiation of a page or project.  You may find specific handling for [defined register types](components/register#available-types) in the [register](components/register) component.  

All usage of common types (actions, filters, menus, routes, etc) should generally be channeled through this class which offers test coverage out of the box and best practice standards agreed upon by the broader team.

### Repositories Directory

The `repositories` directory contains a set of broadly shared classes which provide common functionality for interacting with a given dependencies or external code base.  These classes exist to minimize dependency surface area and promote the fundamentals of single responsibility architectural design across our broader projects and code bases.  

When changes are made within these classes the same input and output expectations should always be preserved such that required updates, bug fixes, and performance increases are shared by the broader code base, effectively reducing technical debt exposure from aging of the code base.

### Traits Directory

The `traits` directory contains a set of commonly used functionality which can be imported into a class.  To constitute a trait class within this directory the functionality included within the trait has been deemed to be generic functionality, broadly applicable to many classes, that would never be used outside of those classes, but can stand on its own in terms of independent functionality.  

`Trait` classes within this directory should still strictly adhere to single responsibility principles in that the class contents to all be deeply related to the class nomenclature.  No `trait` classes should be a simple collection of functionality we want to include in a class such as a single trait which contains a random unrelated set of methods that we may want to use in a given set of classes.
