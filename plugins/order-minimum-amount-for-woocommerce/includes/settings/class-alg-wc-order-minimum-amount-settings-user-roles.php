<?php
/**
 * Order Minimum Amount for WooCommerce - User Roles Section Settings
 *
 * @version 2.2.0
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount_Settings_User_Roles' ) ) :

class Alg_WC_Order_Minimum_Amount_Settings_User_Roles extends Alg_WC_Order_Minimum_Amount_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'user_roles';
		$this->desc = __( 'User Roles', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.0
	 * @since   1.2.0
	 * @todo    [dev] remove "Deprecated settings" code (or move it to `version_updated()`)
	 */
	function get_settings() {

		// Deprecated settings
		$data_version           = get_option( 'alg_wc_oma_data_version', array() );
		$data_version_user_role = ( isset( $data_version['user_role'] ) ? $data_version['user_role'] : 0 );
		if ( version_compare( $data_version_user_role, '2.0.0', '<' ) ) {
			$data = get_option( 'alg_wc_oma_min_sum_by_user_role', array() );
			foreach ( alg_wc_order_minimum_amount()->core->get_all_user_roles() as $role_key => $role_data ) {
				if ( ! isset( $data[ $role_key ] ) ) {
					$data[ $role_key ] = get_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key, 0 );
				}
				delete_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key );
			}
			$data_version['user_role'] = alg_wc_order_minimum_amount()->version;
			update_option( 'alg_wc_oma_min_sum_by_user_role', $data );
			update_option( 'alg_wc_oma_data_version',         $data_version );
		}

		// Settings
		$settings = array(
			array(
				'title'    => __( 'Order Min/Max Amount by User Role', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'This is optional.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ) .
					apply_filters( 'alg_wc_order_minimum_amount_settings', '<p style="background-color: #cccccc; padding: 15px;">' .
						sprintf( 'You will need <a target="_blank" href="%s">Order Minimum/Maximum Amount for WooCommerce Pro</a> plugin to set amounts for <strong>some</strong> user roles.',
							'https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/' ) . '</p>' ),
				'id'       => 'alg_wc_oma_by_user_role_options',
			),
			array(
				'title'    => __( 'Order min/max amount by user role', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_by_user_role_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
		);
		$all_user_roles = alg_wc_order_minimum_amount()->core->get_all_user_roles();
		if ( isset( $all_user_roles['customer'] ) ) {
			$customer_role  = $all_user_roles['customer'];
			unset( $all_user_roles['customer'] );
			$all_user_roles = array_merge( array( 'customer' => $customer_role ), $all_user_roles );
		}
		foreach ( $all_user_roles as $role_key => $role_data ) {
			foreach ( array( 'min', 'max' ) as $min_or_max ) {
				foreach ( array( 'sum', 'qty' ) as $sum_or_qty ) {
					$settings = array_merge( $settings, array(
						array(
							'title'    => ( 'min' === $min_or_max && 'sum' === $sum_or_qty ? $role_data['name'] : '' ),
							'desc'     => ( 'min' === $min_or_max ? __( 'Min', 'order-minimum-amount-for-woocommerce' ) : __( 'Max', 'order-minimum-amount-for-woocommerce' ) ) . ' ' .
								( 'sum' === $sum_or_qty ? __( 'sum', 'order-minimum-amount-for-woocommerce' ) : __( 'quantity', 'order-minimum-amount-for-woocommerce' ) ),
							'id'       => "alg_wc_oma_{$min_or_max}_{$sum_or_qty}_by_user_role[{$role_key}]",
							'default'  => 0,
							'type'     => 'number',
							'custom_attributes' => ( ! in_array( $role_key, array( 'guest', 'administrator', 'customer' ) ) ?
								apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'readonly' => 'readonly' ), 'custom_atts' ) :
								array( 'step' => '0.000001', 'min' => '0' )
							),
							'desc_tip' => ( ! in_array( $role_key, array( 'guest', 'administrator', 'customer' ) ) ?
								apply_filters( 'alg_wc_order_minimum_amount_settings', 'Get <strong><em>"Order Minimum/Maximum Amount for WooCommerce Pro"</em></strong> plugin to set value.' ) :
								''
							),
						),
					) );
				}
			}
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_by_user_role_options',
			),
		) );

		return $settings;
	}

}

endif;

return new Alg_WC_Order_Minimum_Amount_Settings_User_Roles();
