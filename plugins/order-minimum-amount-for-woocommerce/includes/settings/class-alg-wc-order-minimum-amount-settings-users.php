<?php
/**
 * Order Minimum Amount for WooCommerce - Users Section Settings
 *
 * @version 2.2.0
 * @since   2.1.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount_Settings_Users' ) ) :

class Alg_WC_Order_Minimum_Amount_Settings_Users extends Alg_WC_Order_Minimum_Amount_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function __construct() {
		$this->id   = 'users';
		$this->desc = __( 'Users', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.0
	 * @since   2.1.0
	 * @todo    [dev] (maybe) better descriptions (i.e. what happens when it's set to zero (in "Guest Fallback" section))
	 */
	function get_settings() {

		$settings = array(
			array(
				'title'    => __( 'Order Min/Max Amount per User', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => apply_filters( 'alg_wc_order_minimum_amount_settings', '<p style="background-color: #cccccc; padding: 15px;">' .
					sprintf( 'You will need <a target="_blank" href="%s">Order Minimum/Maximum Amount for WooCommerce Pro</a> plugin to set amounts per user.',
						'https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/' ) . '</p>' ),
				'id'       => 'alg_wc_oma_by_user_options',
			),
			array(
				'title'    => __( 'Order min/max amount per user', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'When enabled, you can set amounts per user on each user\'s profile edit page (in "Users > Edit user").', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_by_user_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_by_user_options',
			),
		);

		$guest_fallback_settings = array(
			array(
				'title'    => __( 'Guest Fallback', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'This is used for non-registered users (i.e. guests) as a fallback.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_by_user_guest_options',
			),
		);
		foreach ( array( 'min', 'max' ) as $min_or_max ) {
			foreach ( array( 'sum', 'qty' ) as $sum_or_qty ) {
				$guest_fallback_settings = array_merge( $guest_fallback_settings, array(
					array(
						'title'    => ( 'min' === $min_or_max ? __( 'Min', 'order-minimum-amount-for-woocommerce' ) : __( 'Max', 'order-minimum-amount-for-woocommerce' ) ) . ' ' .
							( 'sum' === $sum_or_qty ? __( 'sum', 'order-minimum-amount-for-woocommerce' ) : __( 'quantity', 'order-minimum-amount-for-woocommerce' ) ),
						'id'       => "alg_wc_oma_{$min_or_max}_{$sum_or_qty}_by_user_guest",
						'default'  => 0,
						'type'     => 'number',
						'custom_attributes' => array( 'step' => '0.000001', 'min' => '0' ),
					),
				) );
			}
		}
		$guest_fallback_settings = array_merge( $guest_fallback_settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_by_user_guest_options',
			),
		) );

		return array_merge( $settings, $guest_fallback_settings );
	}

}

endif;

return new Alg_WC_Order_Minimum_Amount_Settings_Users();
