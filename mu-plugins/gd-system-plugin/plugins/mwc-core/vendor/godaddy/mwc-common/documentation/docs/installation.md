---
id: installation
title: Installation
---

The Managed WooCommerce Common Package is structured as a standard composer package.  To include this in a project simply require the package:

```
composer require gdcorp-partners/mwc-common
```

:::note Do Not Include More Than Once
Currently this package is included within a system that builds via git submodules.  As a result, there could be conflicts of this package in certain instances.  If you are building an additional feature set within the Managed WooCommerce platform this package is already included by default and does not need to be re-imported!
:::

Once required, you may use the offerings within this package in the normal PHP fashion by importing the desired item by its namespace.  See a specific component within this documentation for specifics on the use of that item.
