---
id: is-single-page-application
title: IsSinglePageApplication
---

The `IsSinglePageApplication` trait designates an object intended to handle a single page application (SPA), to provide methods for [enqueueing](/components/enqueue) its main script and rendering its HTML container.

## Properties

The trait relies on 3 properties that its methods will use to [enqueue the script](/traits/is-single-page-application#enqueue-script) and [render the page](/traits/is-single-page-application#render-page). These can be defined in constructor or in the appropriate methods before calling the trait's methods. 

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait;

class MySinglePageApplicationHandler
{
  use IsSinglePageApplicationTrait;

    /**
     * Constructor.
     */
    public function __construct() {
    
        $this->appHandle = 'script-handle'; // the internal name of the SPA script
        $this->appSource = 'https://example.com/app.js'; // should point to the SPA script source URL or path
        $this->divId     = 'spa-container'; // will be the ID of the <div> element used as the app container
    }
}
```

## Enqueue Script

To [enqueue](/components/enqueue) the script, a method that will handle the properties [previously defined](/traits/is-single-page-application#enqueue-script) can be invoked.

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait;

class MySinglePageApplicationHandler
{
  use IsSinglePageApplicationTrait;

    /**
     * Constructor.
     */
    public function __construct() {
    
        $this->appHandle = 'script-handle';
        $this->appSource = 'https://example.com/app.js';
        $this->divId     = 'spa-container';
        
        $this->enqueueApp();
    }
}
```

## Render Page

To render the page HTML, after having defined the script properties and enqueue it, another method can be invoked.

```php
use GoDaddy\WordPress\MWC\Common\Traits\IsSinglePageApplicationTrait;

class MySinglePageApplicationHandler
{
  use IsSinglePageApplicationTrait;

    /**
     * Constructor.
     */
    public function __construct() {
    
        $this->appHandle = 'script-handle';
        $this->appSource = 'https://example.com/app.js';
        $this->divId     = 'spa-container';
        
        $this->enqueueApp();
        $this->render();
    }
}
```
