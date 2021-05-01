---
id: adapters
title: Adapters
---

Adapters take care of mapping data received from external sources into the format expected by the objects in the core package.

## DataSourceAdapterContract

All adapters must implement the `DataSourceAdapterContract` interface.

### Methods

#### convertFromSource

Maps the source data into data that can be used by the target object.

#### convertToSource

Gets the source data that was provided to the adapter when it was created.

## ExtensionAdapterContract

All adapters used to convert extension data should implement the `DataSourceAdapterContract` interface. This allows other components to work on extension data without worrying about its source.

### Methods

#### getType()

Gets the type of the extension from the data that the adapter currently holds.

```php
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\ExtensionAdapterContract;

function buildManagedExtension(ExtensionAdapterContract $adapter): AbstractExtension
{
    if (Theme::TYPE === $adapter->getType()) {
        return (new Theme())->setProperties($adapter->convertFromSource());
    }

    return (new Plugin())->setProperties($adapter->convertFromSource());
}
```

#### getImageUrls()

Gets the array of image URLs for the extension, formatted as `$identifier => $url`.

```php
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\ExtensionAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

function renderExtensionImage(ExtensionAdapterContract $adapter)
{
    $hero_image_url = ArrayHelper::get($adapter->getImageUrls(), 'hero', 'placeholder-image.png');

    ?>
    <img src="<?php echo esc_url($hero_image_url); ?>" />
    <?php
}
```

## Available Adapters

### ExtensionAdapter

Converts data for a single extension returned by the [SkyVerge Extensions API](https://github.com/gdcorp-partners/skyverge-extensions-api) into an array that can be used to set the properties of an [extension object](/components/extension.md).

```php
use GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter;

$adapter = new ExtensionAdapter($data);

return (new Plugin())->setProperties($adapter->convertFromSource());
```

### WooCommerceExtensionAdapter

Converts data for a single extension returned by GoDaddy's partners API into an array of data that can be used to set the properties of an [extension object](/components/extension.md).

```php
use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter;

$adapter = new WooCommerceExtensionAdapter($data);

return (new Plugin())->setProperties($adapter->convertFromSource());
```
