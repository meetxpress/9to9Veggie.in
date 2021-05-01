---
id: page 
title: Page
---

The `Page` component provides a starting point and common set of functionality for rendering and listing user facing content.

## Base Class
The base class offered provides common functionality for building more specific page types, but should not be used on its own.  For functionality related to specific types of pages, you should consult the specific [page type](/components/page#available-types).

### Instantiating a Page
To initialize a new page you will need to provide some minimal information.  Every new page expects at least a `screenId` and `pageTitle` to be provided.

```php
use GoDaddy\WordPress\MWC\Common\Pages\Types\MyPageType;

$page = new MyPageType('screen-id', 'My Page Title');
```

### Rendering a Page
Every page will include a `render()` function that can be called when you are ready to render the page markup.  To render the page markup simply execute the `render()` function.

```php
use GoDaddy\WordPress\MWC\Common\Pages\Types\MyPageType;

$page = new MyPageType('screen-id', 'My Page Title');

$page->render();
```

## Available Types
| Type           	| Description       |
|----------------	|----------------	|
| None 	            | None 	            |

### Creating a Type
While this library may contain several commonly used pages and [page types](/components/page#available-types), it may not always have exactly what you need.  For those cases, you are free to create a new page type that extends `AbstractPage` or to extend a [page type](/components/page#available-types) that already exists.  The base classes should offer most commonly required and generic functionality out of the box, while a new page type can focus on adding type specific functionality.

All page types should be an implementation of the `RenderableContract` to ensure the expected functionality is present.

```php
use GoDaddy\WordPress\MWC\Common\Pages\AbstractPage;
use GoDaddy\WordPress\MWC\Common\Pages\Contracts\RenderableContract;

final class Page{MyPageType} extends AbstractPage implements RenderableContract
{
  //
}
```

## Screen Class

The `Screen` class holds contextual information about the page/screen the user is currently viewing.  This can be used in any situation where we need the current state of what the user is viewing.

```php
use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;

$screen = new Screen([
    'pageId'       => 'some_product',
    'pageContexts' => ['woocommerce'],
    'objectId'     => '123',
    'objectType'   => 'product',
    'objectStatus' => 'publish',
]);

$dataArray = $screen->toArray();
```
