=== Order Minimum/Maximum Amount for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, order minimum amount, order maximum amount
Requires at least: 4.4
Tested up to: 5.4
Stable tag: 2.2.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set required minimum and maximum order amounts in WooCommerce.

== Description ==

**Order Minimum/Maximum Amount for WooCommerce** plugin lets you set required minimum/maximum sum/quantity for orders in WooCommerce.

= Main Features =

* Set **minimum sum** for orders.
* Set **minimum quantity** for orders.
* Set **maximum sum** for orders.
* Set **maximum quantity** for orders.
* Customizable user **messages** can be displayed on **cart and/or checkout** pages.
* Select if you want to **exclude shipping and/or discounts** when calculating order (i.e. cart) total **sum**.
* Optionally **stop customer from reaching the checkout page** if amount requirements are not met.
* Optionally set different order amounts on **per user role** basis.

= Premium Version =

With [premium version](https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/) you can:

* Set different order amounts on **per user** basis.
* Set amounts for **all** user roles.
* **More customization** options for the messages.

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Order Min/Max Amount".

== Changelog ==

= 2.2.3 - 18/07/2020 =
* Dev - `alg_wc_oma_check_order_min_max_amount` filter added.
* Dev - `alg_wc_oma_block_checkout` filter added.

= 2.2.2 - 17/07/2020 =
* Dev - `get_cart_total()` - Using `WC()->cart->get_total( 'edit' )` function now (instead of `WC()->cart->total`).
* WC tested up to: 4.3.

= 2.2.1 - 27/05/2020 =
* Dev - Localization - Messages added to the translation file.
* WC tested up to: 4.1.

= 2.2.0 - 14/04/2020 =
* Dev - Messages - "Additional Positions" options added.
* Dev - `[alg_wc_order_min_max_amount]` shortcode added.
* Dev - Code refactoring.
* Dev - Admin "reset settings" notice updated.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.4.
* Plugin URI updated.

= 2.1.0 - 30/10/2019 =
* Fix - General - Exclude shipping - Shipping tax function fixed.
* Dev - "Order Min/Max Amount per User" options added.
* Dev - User Roles - Enable section - Defaults to `no` now.
* Dev - Admin settings restyled.
* Dev - Code refactoring.
* WC tested up to: 3.7.

= 2.0.0 - 30/07/2019 =
* Dev - "Order **Maximum** Sum/Quantity" options added.
* Dev - "Order Minimum **Quantity**" options added.
* Dev - Messages - `%min_order_sum_diff%` and `%min_order_qty_diff%` placeholders added.
* Dev - Messages - Placeholders replaced: `%minimum_order_amount%` with `%min_order_sum%` and `%cart_total%` with `%cart_total_sum%`.
* Dev - User Roles - "Enable section" option added (defaults to `yes`).
* Dev - User Roles - Roles settings are stored in array now.
* Dev - User Roles - "Customer" role moved to the top of the list.
* Dev - Step in settings increased to `0.000001`.
* Dev - Major code refactoring.

= 1.2.1 - 25/07/2019 =
* Dev - Messages - Shortcodes are now processed in cart and checkout messages; `[alg_wc_oma_translate]` shortcode added for WPML/Polylang translations.
* Dev - Admin settings - Descriptions updated; "Your settings have been reset" notice added.
* Tested up to: 5.2.
* WC tested up to: 3.6.

= 1.2.0 - 30/10/2018 =
* Fix - "get_cart_url is deprecated" notice fixed.
* Dev - "Exclude discounts" option added.
* Dev - "Notice type on checkout page" and "Notice type on cart page" options added.
* Dev - Now checking all user roles instead of first one only.
* Dev - "Raw" values are now allowed in messages.
* Dev - Amount step decreased in admin settings.
* Dev - Admin settings sections restyled and descriptions updated.
* Dev - Code refactoring.
* Dev - Plugin URI updated.

= 1.1.0 - 24/07/2017 =
* Dev - Autoloading plugin options.
* Dev - `exit` added after `wp_safe_redirect()`.
* Dev - Plugin URI updated.
* Dev - Plugin header ("Text Domain" etc.) updated.

= 1.0.1 - 08/02/2017 =
* Dev - Language (POT) file added.
* Fix - Link fixed in User Role settings.

= 1.0.0 - 04/02/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
