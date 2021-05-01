# mwc-core

## Testing

You can install and activate the **MWC Core** plugin as a regular WordPress plugin on a local website following these steps:

1. Make sure your site has Settings > Permalinks set to anything other than `plain`
1. Copy of the content of the `gd-config.php` file found at the root of your TLA site into the `wp-config.php` file of your local site
1. Clone this repo or create a symlink of your clone in `wp-content/plugins/mwc-core`
1. Go into the `mwc-core` directory and run `composer install`
1. Activate the **MWC Core** plugin

### How to use development versions of the `mwc-common` and `mwc-dashboard` packages?

You can use a development version of one of the shared libraries using two methods:

#### Replace the vendor with the development version of the shared library

1. Clone the shared library repo or create a symlink of your clone in `vendor/godaddy/{package}`

If you are using a symlink, the plugin may be unable to determine the correct URLs for the CSS assets included in the `mwc-dashboard` package. You can define the `MWC_DASHBOARD_PLUGIN_URL` constant in your `wp-config.php` file to provide the correct URL prefix. For example:

```php
define( 'MWC_DASHBOARD_PLUGIN_URL', 'https://commerce.godaddy.test/wp-content/plugins/mwc-core/vendor/godaddy/mwc-dashboard/' );
```

Make sure that the part of the value before `/vendor/` matches the URL for the `mwc-core` directory in your local site.

#### Update Composer to use a development version of the shared library

1. Update the version contraint for the shared library in the `composer.json` file to be `dev-{branch} as {version}`
1. Replace `{branch}` with the development branch you want to test
1. Replace `{version}` with the latest tagged version of that shared library

Example:

```json
{
    "require": {
        "php": ">=7.0",
        "godaddy/mwc-common": "^1.0",
        "godaddy/mwc-dashboard": "dev-main as 1.0.0"
    },
}
```

### How to serve the frontend assets from a different domain?

You can change the asset URLs by editing the `vendor/godaddy/mwc-dashboard/configurations/mwc-dashboard.php` configuration file.

If you do that, make sure to clear cached data so that new values are used.

### How to clear cached data?

This is a snippet clears all cached data related to configurations and extensions, both from our code and from Woo, so you can test with a clean slate whenever you need to:

```
delete_transient('gd_extensions');
delete_transient('gd_configurations');
delete_transient('_woocommerce_helper_subscriptions');
delete_transient('_woocommerce_helper_updates');
delete_transient('_woocommerce_helper_updates_count');
delete_transient('wc_addons_featured');
delete_site_transient('update_plugins');
```
