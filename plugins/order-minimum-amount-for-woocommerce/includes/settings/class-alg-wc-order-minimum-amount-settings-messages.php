<?php
/**
 * Order Minimum Amount for WooCommerce - Messages Section Settings
 *
 * @version 2.2.1
 * @since   1.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount_Settings_Messages' ) ) :

class Alg_WC_Order_Minimum_Amount_Settings_Messages extends Alg_WC_Order_Minimum_Amount_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'messages';
		$this->desc = __( 'Messages', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.1
	 * @since   1.2.0
	 * @todo    [dev] (maybe) add "Message on requirements met"
	 * @todo    [dev] Additional Positions: (maybe) priorities
	 * @todo    [dev] Additional Positions: Cart: (maybe) `woocommerce_cart_is_empty`
	 */
	function get_settings() {

		$settings = array(
			array(
				'title'    => __( 'Message Options', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_general_options',
			),
			array(
				'title'    => __( 'Notices', 'order-minimum-amount-for-woocommerce' ) . ': ' . __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Add', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_cart_notice_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Notices', 'order-minimum-amount-for-woocommerce' ) . ': ' . __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Add', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_checkout_notice_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Notice type', 'order-minimum-amount-for-woocommerce' ) . ': ' . __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Styling.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_cart_notice_type',
				'default'  => 'notice',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'error'  => __( 'Error', 'order-minimum-amount-for-woocommerce' ),
					'notice' => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Notice type', 'order-minimum-amount-for-woocommerce' ) . ': ' . __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Styling.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_checkout_notice_type',
				'default'  => 'error',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'error'  => __( 'Error', 'order-minimum-amount-for-woocommerce' ),
					'notice' => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_general_options',
			),
			array(
				'title'    => __( 'Additional Positions', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_positions_options',
			),
			array(
				'title'    => __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_message_positions_cart',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_before_cart_table'                 => __( 'Before cart table', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_before_cart_contents'              => __( 'Before cart contents', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_contents'                     => __( 'Cart contents', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_coupon'                       => __( 'Cart coupon', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_actions'                      => __( 'Cart actions', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_cart_contents'               => __( 'After cart contents', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_cart_table'                  => __( 'After cart table', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_before_cart_totals'                => __( 'Before cart totals', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_totals_before_shipping'       => __( 'Cart totals: Before shipping', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_totals_after_shipping'        => __( 'Cart totals: After shipping', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_totals_before_order_total'    => __( 'Cart totals: Before order total', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_cart_totals_after_order_total'     => __( 'Cart totals: After order total', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_proceed_to_checkout'               => __( 'Proceed to checkout', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_cart_totals'                 => __( 'After cart totals', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_before_shipping_calculator'        => __( 'Before shipping calculator', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_shipping_calculator'         => __( 'After shipping calculator', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_message_positions_checkout',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_before_checkout_form'              => __( 'Before checkout form', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_before_customer_details'  => __( 'Before customer details', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_billing'                  => __( 'Billing', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_shipping'                 => __( 'Shipping', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_after_customer_details'   => __( 'After customer details', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_before_order_review'      => __( 'Before order review', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_order_review'             => __( 'Order review', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_review_order_before_shipping'      => __( 'Order review: Before shipping', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_review_order_after_shipping'       => __( 'Order review: After shipping', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_review_order_before_submit'        => __( 'Order review: Payment: Before submit button', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_review_order_after_submit'         => __( 'Order review: Payment: After submit button', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_checkout_after_order_review'       => __( 'After order review', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_checkout_form'               => __( 'After checkout form', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_positions_options',
			),
			array(
				'title'    => __( 'Messages', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => apply_filters( 'alg_wc_order_minimum_amount_settings', '<p style="background-color: #cccccc; padding: 15px;">' .
						sprintf( 'Get <a target="_blank" href="%s">Order Minimum/Maximum Amount for WooCommerce Pro</a> plugin to set custom messages.',
							'https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/' ) . '</p>' ) .
					sprintf( __( 'You can use shortcodes in messages, e.g. for WPML/Polylang translations: %s', 'order-minimum-amount-for-woocommerce' ),
						'<br><code>[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate][alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate][alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]</code>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_content_options',
			),
		);
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Min sum', 'order-minimum-amount-for-woocommerce' ) . ': ' .
						( 'checkout' === $cart_or_checkout ? __( 'Checkout', 'order-minimum-amount-for-woocommerce' ) : __( 'Cart', 'order-minimum-amount-for-woocommerce' ) ),
					'desc'     => sprintf( __( 'Placeholders: %s.', 'order-minimum-amount-for-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array( '%min_order_sum%', '%cart_total_sum%', '%min_order_sum_diff%' ) ) . '</code>' ),
					'desc_tip' => __( 'Message to the customer if order is below minimum sum.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_min_sum_message[{$cart_or_checkout}]",
					'default'  => __( 'You must have an order with a minimum of %min_order_sum% to place your order, your current order total is %cart_total_sum%.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'readonly' => 'readonly' ) ),
					'alg_wc_oma_raw' => true,
				),
			) );
		}
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Min quantity', 'order-minimum-amount-for-woocommerce' ) . ': ' .
						( 'checkout' === $cart_or_checkout ? __( 'Checkout', 'order-minimum-amount-for-woocommerce' ) : __( 'Cart', 'order-minimum-amount-for-woocommerce' ) ),
					'desc'     => sprintf( __( 'Placeholders: %s.', 'order-minimum-amount-for-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array( '%min_order_qty%', '%cart_total_qty%', '%min_order_qty_diff%' ) ) . '</code>' ),
					'desc_tip' => __( 'Message to the customer if order is below minimum quantity.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_min_qty_message[{$cart_or_checkout}]",
					'default'  => __( 'You must have an order with a minimum of %min_order_qty% pcs. to place your order, your current order quantity is %cart_total_qty%.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'readonly' => 'readonly' ) ),
					'alg_wc_oma_raw' => true,
				),
			) );
		}
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Max sum', 'order-minimum-amount-for-woocommerce' ) . ': ' .
						( 'checkout' === $cart_or_checkout ? __( 'Checkout', 'order-minimum-amount-for-woocommerce' ) : __( 'Cart', 'order-minimum-amount-for-woocommerce' ) ),
					'desc'     => sprintf( __( 'Placeholders: %s.', 'order-minimum-amount-for-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array( '%max_order_sum%', '%cart_total_sum%', '%max_order_sum_diff%' ) ) . '</code>' ),
					'desc_tip' => __( 'Message to the customer if order is above maximum sum.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_max_sum_message[{$cart_or_checkout}]",
					'default'  => __( 'You must have an order with a maximum of %max_order_sum% to place your order, your current order total is %cart_total_sum%.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'readonly' => 'readonly' ) ),
					'alg_wc_oma_raw' => true,
				),
			) );
		}
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Max quantity', 'order-minimum-amount-for-woocommerce' ) . ': ' .
						( 'checkout' === $cart_or_checkout ? __( 'Checkout', 'order-minimum-amount-for-woocommerce' ) : __( 'Cart', 'order-minimum-amount-for-woocommerce' ) ),
					'desc'     => sprintf( __( 'Placeholders: %s.', 'order-minimum-amount-for-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array( '%max_order_qty%', '%cart_total_qty%', '%max_order_qty_diff%' ) ) . '</code>' ),
					'desc_tip' => __( 'Message to the customer if order is above maximum quantity.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_max_qty_message[{$cart_or_checkout}]",
					'default'  => __( 'You must have an order with a maximum of %max_order_qty% pcs. to place your order, your current order quantity is %cart_total_qty%.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'textarea',
					'css'      => 'width:100%;',
					'custom_attributes' => apply_filters( 'alg_wc_order_minimum_amount_settings', array( 'readonly' => 'readonly' ) ),
					'alg_wc_oma_raw' => true,
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_content_options',
			),
		) );

		return $settings;
	}

}

endif;

return new Alg_WC_Order_Minimum_Amount_Settings_Messages();
