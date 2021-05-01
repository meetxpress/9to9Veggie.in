---
id: page
title: Page
---

The `Page` repository provides an abstraction layer for common interactions with the pages within a project.  To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PageRepository;
```

## Current Page

Return the currently active page.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PageRepository;

PageRepository::getCurrentPage();
```

## Is Current Page

Checks if the current page matches the given page slug.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\PageRepository;

PageRepository::isCurrentPage('admin');
```
