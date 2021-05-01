---
id: wordpress
title: General
---

The `WordPress` repository provides an abstraction layer for common interactions with WordPress within a project.  To use the methods within this class you must import the following:

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
```

## Require WordPress

Global static function to require WordPress within a class to be active.  If WordPress is not active, then an exception will be thrown.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::requireWordPressInstance();
```

## Require WordPress Filesystem

Attempts to initialize WordPress Filesystem abstractions. Throws an exception ff the filesystem cannot be accessed.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::requireWordPressFilesystem();
```

## Debug Mode

Check if the current WordPress instance is in debug mode.

:::note Multiple Debug Levels
The WordPress debug flag may not trigger all debug levels as Managed WooCommerce may have its own debug levels not dependent on WordPress core settings.
:::

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::isDebugMode();
```

## Assets Urls

Get the path to the assets for the given project.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::getAssetsUrl();
```

## Has WordPress Instance

Check if there is an active available wordpress instance.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::hasWordPressInstance();
```

## Is API Request

Check if the current WordPress instance is fulfilling an API request

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::isApiRequest();
```

## Is CLI Mode

Check if the current WordPress instance is in cli mode.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::isCliMode();
```

## Current Version

Return the current WordPress version.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::getVersion();
```

## WordPress User

Return the current WP_User.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::getUser();
```

## WordPress User By Email, Login, or ID

If you need to get a specific WP_User which is not the currently logged you are able to retrieve those by email, login, or id.  If the user does not exist, null will be returned.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::getUserByEmail('foo@bar.com');
WordPressRepository::getUserByLogin('foo');
WordPressRepository::getUserById(7256);
```

## Current Screen

If you need access to contextual information about the page/screen that the user is currently viewing, you can retrieve a new instance of the [Screen Class](/components/page#base-class) using the `getWordPressScreen()`.

```php
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

WordPressRepository::getCurrentScreen();
```
