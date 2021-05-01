<?php
/**
 * Order Minimum Amount for WooCommerce - General Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount_Settings_General' ) ) :

class Alg_WC_Order_Minimum_Amount_Settings_General extends Alg_WC_Order_Minimum_Amount_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Order Min/Max Amount Options', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_options',
			),
			array(
				'title'    => __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_options',
			),
			array(
				'title'    => __( 'Order Sum', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_sum_options',
			),
			array(
				'title'    => __( 'Min sum', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Minimum order sum.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_min_sum',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array(
					'step' => '0.000001',
					'min'  => '0',
				),
			),
			array(
				'title'    => __( 'Max sum', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Maximum order sum.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_sum',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array(
					'step' => '0.000001',
					'min'  => '0',
				),
			),
			array(
				'title'    => __( 'Exclude shipping', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Excludes shipping from cart total.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_shipping',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Exclude discounts', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Excludes discounts from cart total.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_discounts',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_sum_options',
			),
			array(
				'title'    => __( 'Order Quantity', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_qty_options',
			),
			array(
				'title'    => __( 'Min quantity', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Minimum order quantity.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_min_qty',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array(
					'step' => '0.000001',
					'min'  => '0',
				),
			),
			array(
				'title'    => __( 'Max quantity', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Maximum order quantity.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_qty',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array(
					'step' => '0.000001',
					'min'  => '0',
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_qty_options',
			),
			array(
				'title'    => __( 'General', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_general_options',
			),
			array(
				'title'    => __( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Stops customer from reaching the checkout page on wrong min/max amount.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Customer is redirected back to the cart page.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_block_checkout',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_general_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Order_Minimum_Amount_Settings_General();
