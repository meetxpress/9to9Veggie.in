<?php
/**
 * Order Minimum Amount for WooCommerce - Settings
 *
 * @version 2.2.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Order_Minimum_Amount' ) ) :

class Alg_WC_Settings_Order_Minimum_Amount extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_order_minimum_amount';
		$this->label = __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );
		// Sections
		require_once( 'class-alg-wc-order-minimum-amount-settings-section.php' );
		require_once( 'class-alg-wc-order-minimum-amount-settings-general.php' );
		require_once( 'class-alg-wc-order-minimum-amount-settings-messages.php' );
		require_once( 'class-alg-wc-order-minimum-amount-settings-user-roles.php' );
		require_once( 'class-alg-wc-order-minimum-amount-settings-users.php' );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 * @todo    [dev] find better solution
	 */
	function maybe_unsanitize_option( $value, $option, $raw_value ) {
		return ( ! empty( $option['alg_wc_oma_raw'] ) ? $raw_value : $value );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'    => __( 'Reset Settings', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'    => __( 'Reset section settings', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Reset', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => $this->id . '_' . $current_section . '_reset',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'order-minimum-amount-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'order-minimum-amount-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Settings_Order_Minimum_Amount();
