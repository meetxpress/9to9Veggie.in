---
id: overview
title: Overview
slug: /
---

The Managed WooCommerce Common Package provides a collection of common patterns, normalized objects, and helper functionalities to make building PHP applications faster while ensuring best practices.  The engineering experience should not be one of having to sweat every detail when implementing a new feature.  

We have a great team of engineers that have likely already solved part of that problem and on of the primary focuses of this package is to share those solutions with the broader team so that they may focus on building new feature sets in a bug free manner without needing to re-invent the wheel or grapple with the introduction of long term technical debt.

The package strives to provide an amazing developer experience, while limiting dependency surface areas, technical debt creation, or avoidable edge case bugs while maximizing reusability through componenitization, predictability through normalization, and stability through out of the box test coverage.

## Why a componetized core package?

There are a variety of great libraries, toolsets, and frameworks available in the wild we could have relied upon in pushing our platform work forward.  In fact, we have built our platform on top of WordPress and WooCommerce core functionality.  So why this abstraction layer in addition to those code bases already provided?

### A scalable approach

TBD: Problems with scalability.. speed of development, the common approach of building single plugins versus platforms, and the ability to agnostically interact with other services and systems.

### Working towards a common goal

TBD: Code and best practice sharing.  Don't build the same thing twice. Encouragement of componentization and reusability to heavily encourage OOP / good design practices.

### Limitation of risk

We currently plan to use this package in our work with WordPress and WooCommerce.  The two have a long history of untested code ([6.8% coverage](https://coveralls.io/builds/34429237)) coupled with a history of breaking changes during version releases.  As a result, each version update currently requires a lengthy compatability review which cannot be fully automated at this time.  Finally because we do not control the code base, we have no influence over its directional shift and how it may impact our deeper functionalities.

In order to combat this risk and as a general best practice when dealing with dependencies, this package strives to limit the surface area of those dependencies and isolate them on the fringes.  The approach allows for our code base to be completely controlled, simply tested, and ensures future external changes are more easily handled.

By pushing minimizing our dependency risk through surface area reduction and moving it to the fringes, our engineers are able to write clean and simplistic unit tests within our broader code base with clear separation of responsibility thorughout functionality.  This allows for code coverage to be higher, less complex, and less fragile -- all while implementing the code and coverage at a far faster rate.