<?php
/**
 * Order Minimum Amount for WooCommerce - Core Class
 *
 * @version 2.2.3
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Order_Minimum_Amount_Core' ) ) :

class Alg_WC_Order_Minimum_Amount_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 * @todo    [feature] (maybe) validate on **add to cart**
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_oma_plugin_enabled', 'yes' ) ) {
			add_action( 'init', array( $this, 'add_hooks' ) );
			add_shortcode( 'alg_wc_oma_translate', array( $this, 'language_shortcode' ) );
		}
		do_action( 'alg_wc_order_minimum_amount_core_loaded', $this );
	}

	/**
	 * add_hooks.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function add_hooks() {
		// Checkout
		add_action( 'woocommerce_checkout_process', array( $this, 'checkout_process_notice' ) );
		if ( 'yes' === get_option( 'alg_wc_oma_checkout_notice_enabled', 'no' ) ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_notice' ) );
		}
		// Cart
		if ( 'yes' === get_option( 'alg_wc_oma_cart_notice_enabled', 'no' ) ) {
			add_action( 'woocommerce_before_cart', array( $this, 'cart_notice' ) );
		}
		// Block checkout
		if ( 'yes' === get_option( 'alg_wc_oma_block_checkout', 'no' ) ) {
			add_action( 'wp', array( $this, 'block_checkout' ), PHP_INT_MAX );
		}
		// Additional positions
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$positions = get_option( 'alg_wc_oma_message_positions_' . $cart_or_checkout, array() );
			if ( ! empty( $positions ) ) {
				foreach ( $positions as $position ) {
					add_action( $position, array( $this, $cart_or_checkout . '_text' ) );
				}
			}
		}
	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_oma_translate lang="DE" lang_text="Text for DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate][alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate][alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * get_order_min_max_amount.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function get_order_min_max_amount( $min_or_max, $sum_or_qty ) {
		if ( ( $val = apply_filters( 'alg_wc_order_minimum_amount_get_order_min_max_amount', 0, $min_or_max, $sum_or_qty ) ) > 0 ) {
			return $val;
		}
		// User roles
		if ( 'yes' === get_option( 'alg_wc_oma_by_user_role_enabled', 'no' ) ) {
			$current_user           = wp_get_current_user();
			$current_user_roles     = ( ! $current_user->exists() ? array( 'guest' ) : $current_user->roles );
			$all_roles_keys         = array_keys( $this->get_all_user_roles() );
			$data_version           = get_option( 'alg_wc_oma_data_version', array() );
			$data_version_user_role = ( isset( $data_version['user_role'] ) ? $data_version['user_role'] : 0 );
			foreach ( $current_user_roles as $role_key ) {
				if ( empty( $role_key ) ) {
					$role_key = 'guest';
				}
				if ( in_array( $role_key, $all_roles_keys ) ) {
					if ( 'min' === $min_or_max && 'sum' === $sum_or_qty && version_compare( $data_version_user_role, '2.0.0', '<' ) ) {
						if ( ( $order_minimum_sum = get_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key, 0 ) ) > 0 ) {
							return $order_minimum_sum;
						}
					} else {
						if ( ! isset( $this->amount_by_user_role[ $min_or_max ][ $sum_or_qty ] ) ) {
							$this->amount_by_user_role[ $min_or_max ][ $sum_or_qty ] = get_option( "alg_wc_oma_{$min_or_max}_{$sum_or_qty}_by_user_role", array() );
						}
						$val = ( isset( $this->amount_by_user_role[ $min_or_max ][ $sum_or_qty ][ $role_key ] ) ?
							$this->amount_by_user_role[ $min_or_max ][ $sum_or_qty ][ $role_key ] : 0 );
						if ( $val > 0 ) {
							return $val;
						}
					}
				}
			}
		}
		// General
		return get_option( "alg_wc_oma_{$min_or_max}_{$sum_or_qty}", 0 );
	}

	/**
	 * get_cart_total.
	 *
	 * @version 2.2.2
	 * @since   1.0.0
	 * @todo    [dev] recheck if we need `calculate_totals` for `qty`?
	 */
	function get_cart_total( $sum_or_qty ) {
		if ( ! isset( WC()->cart ) ) {
			return 0;
		}
		WC()->cart->calculate_totals();
		if ( 'sum' === $sum_or_qty ) {
			$cart_total = WC()->cart->get_total( 'edit' );
			if ( 'yes' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) ) {
				$cart_total -= ( WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() );
			}
			if ( 'yes' === get_option( 'alg_wc_oma_exclude_discounts', 'no' ) ) {
				$cart_total += ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() );
			}
			return $cart_total;
		} else { // 'qty'
			return WC()->cart->get_cart_contents_count();
		}
	}

	/**
	 * get_notice_placeholders.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_notice_placeholders( $min_or_max, $sum_or_qty, $val, $total ) {
		switch ( $sum_or_qty ) {
			case 'sum':
				switch ( $min_or_max ) {
					case 'min':
						return array(
							'%min_order_sum%'        => wc_price( $val ),
							'%cart_total_sum%'       => wc_price( $total ),
							'%min_order_sum_diff%'   => wc_price( $val - $total ),
							'%minimum_order_amount%' => wc_price( $val ),   // deprecated
							'%cart_total%'           => wc_price( $total ), // deprecated
						);
					case 'max':
						return array(
							'%max_order_sum%'        => wc_price( $val ),
							'%cart_total_sum%'       => wc_price( $total ),
							'%max_order_sum_diff%'   => wc_price( $total - $val ),
						);
				}
			case 'qty':
				switch ( $min_or_max ) {
					case 'min':
						return array(
							'%min_order_qty%'        => $val,
							'%cart_total_qty%'       => $total,
							'%min_order_qty_diff%'   => $val - $total,
						);
					case 'max':
						return array(
							'%max_order_qty%'        => $val,
							'%cart_total_qty%'       => $total,
							'%max_order_qty_diff%'   => $total - $val,
						);
				}
		}
	}

	/**
	 * get_notice_content.
	 *
	 * @version 2.2.1
	 * @since   2.2.0
	 */
	function get_notice_content( $min_or_max, $sum_or_qty, $val, $total, $cart_or_checkout ) {
		$placeholders = $this->get_notice_placeholders( $min_or_max, $sum_or_qty, $val, $total );
		$content      = apply_filters( 'alg_wc_order_minimum_amount_message',
			( 'sum' === $sum_or_qty ?
				( 'min' === $min_or_max ?
					__( 'You must have an order with a minimum of %min_order_sum% to place your order, your current order total is %cart_total_sum%.', 'order-minimum-amount-for-woocommerce' ) :
					__( 'You must have an order with a maximum of %max_order_sum% to place your order, your current order total is %cart_total_sum%.', 'order-minimum-amount-for-woocommerce' )
				) :
				( 'min' === $min_or_max ?
					__( 'You must have an order with a minimum of %min_order_qty% pcs. to place your order, your current order quantity is %cart_total_qty%.', 'order-minimum-amount-for-woocommerce' ) :
					__( 'You must have an order with a maximum of %max_order_qty% pcs. to place your order, your current order quantity is %cart_total_qty%.', 'order-minimum-amount-for-woocommerce' )
				)
			),
			"message_{$min_or_max}_{$sum_or_qty}", $cart_or_checkout );
		return str_replace( array_keys( $placeholders ), $placeholders, $content );
	}

	/**
	 * check_order_min_max_amount.
	 *
	 * @version 2.2.3
	 * @since   2.0.0
	 */
	function check_order_min_max_amount( $min_or_max, $val, $total ) {
		$result = ! ( 'min' === $min_or_max ?
			( $val && $total && $total < $val ) :
			( $val && $total && $total > $val ) );
		return apply_filters( 'alg_wc_oma_check_order_min_max_amount', $result, $min_or_max, $val, $total );
	}

	/**
	 * cart_notice.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function cart_notice() {
		$this->output_notice( 'cart', 'wc_print_notice', get_option( 'alg_wc_oma_cart_notice_type', 'notice' ) );
	}

	/**
	 * cart_text.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function cart_text() {
		echo $this->output_notice( 'cart' );
	}

	/**
	 * checkout_text.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function checkout_text() {
		echo $this->output_notice( 'checkout' );
	}

	/**
	 * checkout_notice.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function checkout_notice() {
		$this->output_notice( 'checkout', 'wc_print_notice', get_option( 'alg_wc_oma_checkout_notice_type', 'error' ) );
	}

	/**
	 * checkout_process_notice.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function checkout_process_notice() {
		$this->output_notice( 'checkout', 'wc_add_notice', 'error' );
	}

	/**
	 * output_notice.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function output_notice( $cart_or_checkout, $func = false, $notice_type = false ) {
		foreach ( array( 'min', 'max' ) as $min_or_max ) {
			foreach ( array( 'sum', 'qty' ) as $sum_or_qty ) {
				$val   = $this->get_order_min_max_amount( $min_or_max, $sum_or_qty );
				$total = $this->get_cart_total( $sum_or_qty );
				if ( ! $this->check_order_min_max_amount( $min_or_max, $val, $total ) ) {
					$content = $this->get_notice_content( $min_or_max, $sum_or_qty, $val, $total, $cart_or_checkout );
					if ( $func ) {
						$func( $content, $notice_type );
					} else {
						return $content;
					}
				}
			}
		}
	}

	/**
	 * block_checkout.
	 *
	 * @version 2.2.3
	 * @since   1.0.0
	 */
	function block_checkout( $wp ) {
		if ( ! is_checkout() || ! apply_filters( 'alg_wc_oma_block_checkout', true ) ) {
			return;
		}
		foreach ( array( 'min', 'max' ) as $min_or_max ) {
			foreach ( array( 'sum', 'qty' ) as $sum_or_qty ) {
				if ( ! $this->check_order_min_max_amount( $min_or_max, $this->get_order_min_max_amount( $min_or_max, $sum_or_qty ), $this->get_cart_total( $sum_or_qty ) ) ) {
					wp_safe_redirect( version_compare( get_option( 'woocommerce_version', null ), '2.5.0', '<' ) ? WC()->cart->get_cart_url() : wc_get_cart_url() );
					exit;
				}
			}
		}
	}

	/**
	 * get_all_user_roles.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_all_user_roles() {
		global $wp_roles;
		return array_merge( array( 'guest' => array( 'name' => __( 'Guest', 'order-minimum-amount-for-woocommerce' ), 'capabilities' => array() ) ),
			apply_filters( 'editable_roles', ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() ) ) );
	}

}

endif;

return new Alg_WC_Order_Minimum_Amount_Core();
