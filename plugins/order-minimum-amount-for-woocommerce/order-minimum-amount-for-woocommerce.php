<?php
/*
Plugin Name: Order Minimum/Maximum Amount for WooCommerce
Plugin URI: https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/
Description: Set required minimum and maximum order amounts in WooCommerce.
Version: 2.2.3
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: order-minimum-amount-for-woocommerce
Domain Path: /langs
Copyright: © 2020 Algoritmika Ltd.
WC tested up to: 4.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount' ) ) :

/**
 * Main Alg_WC_Order_Minimum_Amount Class.
 *
 * @class   Alg_WC_Order_Minimum_Amount
 * @version 2.2.0
 * @since   1.0.0
 */
final class Alg_WC_Order_Minimum_Amount {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '2.2.3';

	/**
	 * @var   Alg_WC_Order_Minimum_Amount The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Order_Minimum_Amount Instance.
	 *
	 * Ensures only one instance of Alg_WC_Order_Minimum_Amount is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Order_Minimum_Amount - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Order_Minimum_Amount Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @access  public
	 * @todo    [feature] (next) "Weight", "Volume" etc. (i.e. in addition to "Sum" and "Qty")
	 */
	function __construct() {

		// Check for active plugins
		if (
			! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ||
			( 'order-minimum-amount-for-woocommerce.php' === basename( __FILE__ ) && $this->is_plugin_active( 'order-minimum-amount-for-woocommerce-pro/order-minimum-amount-for-woocommerce-pro.php' ) )
		) {
			return;
		}

		// Set up localisation
		load_plugin_textdomain( 'order-minimum-amount-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Pro
		if ( 'order-minimum-amount-for-woocommerce-pro.php' === basename( __FILE__ ) ) {
			require_once( 'includes/pro/class-alg-wc-order-minimum-amount-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * is_plugin_active.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function includes() {
		// Core
		$this->core = require_once( 'includes/class-alg-wc-order-minimum-amount-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 2.2.0
	 * @since   1.2.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_order_minimum_amount_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_order_minimum_amount' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'order-minimum-amount-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/">' .
				__( 'Go Pro', 'order-minimum-amount-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Order Minimum Amount settings tab to WooCommerce settings.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'includes/settings/class-alg-wc-settings-order-minimum-amount.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 */
	function version_updated() {
		// Handling deprecated options
		$deprecated = array(
			// v2.0.0
			'alg_wc_order_minimum_amount_stop_from_seeing_checkout' => 'alg_wc_oma_block_checkout',
			'alg_wc_order_minimum_amount_exclude_shipping'          => 'alg_wc_oma_exclude_shipping',
			'alg_wc_order_minimum_amount_exclude_discounts'         => 'alg_wc_oma_exclude_discounts',
			'alg_wc_order_minimum_amount'                           => 'alg_wc_oma_min_sum',
			'alg_wc_order_minimum_amount_enabled'                   => 'alg_wc_oma_plugin_enabled',
			'alg_wc_order_minimum_amount_checkout_notice_enabled'   => 'alg_wc_oma_checkout_notice_enabled',
			'alg_wc_order_minimum_amount_cart_notice_enabled'       => 'alg_wc_oma_cart_notice_enabled',
			'alg_wc_order_minimum_amount_checkout_notice_type'      => 'alg_wc_oma_checkout_notice_type',
			'alg_wc_order_minimum_amount_cart_notice_type'          => 'alg_wc_oma_cart_notice_type',
		);
		foreach ( $deprecated as $old_option => $new_option ) {
			if ( false === get_option( $new_option, false ) && false !== ( $val = get_option( $old_option, false ) ) ) {
				update_option( $new_option, $val );
				delete_option( $old_option );
			}
		}
		$_val = get_option( 'alg_wc_oma_min_sum_message', array() );
		if ( empty( $_val ) ) {
			if ( false !== ( $val = get_option( 'alg_wc_order_minimum_amount_error_message', false ) ) ) {
				$_val['checkout'] = $val;
				delete_option( 'alg_wc_order_minimum_amount_error_message' );
			}
			if ( false !== ( $val = get_option( 'alg_wc_order_minimum_amount_cart_notice_message', false ) ) ) {
				$_val['cart'] = $val;
				delete_option( 'alg_wc_order_minimum_amount_cart_notice_message' );
			}
			update_option( 'alg_wc_oma_min_sum_message', $_val );
		}
		// Updating version
		update_option( 'alg_wc_order_minimum_amount_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_wc_order_minimum_amount' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Order_Minimum_Amount to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Order_Minimum_Amount
	 */
	function alg_wc_order_minimum_amount() {
		return Alg_WC_Order_Minimum_Amount::instance();
	}
}

alg_wc_order_minimum_amount();
