<?php
/**
 * Class for managing plugin settings.
 *
 * @package Notice_Bar
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Settings Class.
 */
class Notice_Bar_Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

	}

	/**
	 * Register plugin settings menu.
	 *
	 * @since 1.0.0
	 */
	function register_admin_menu() {

            add_menu_page(
		    __( 'Notice Bar', 'notice-bar' ),
		    __( 'Notice Bar', 'notice-bar' ),
		    'manage_options',
		    'notice-bar',
		    array( 'Notice_bar_main', 'new_settings_page' ),
			'dashicons-megaphone'
		);
            
	}

    function save_settings(){

    	if ( ! empty( $_POST ) && isset( $_POST['nb-submit'] ) ) {

        	if ( ! isset( $_POST['nb_settings_nonce_field'] ) || ! wp_verify_nonce( $_POST['nb_settings_nonce_field'], 'nb_settings_action' ) ) {
			}
			else{

				global $notice_bar_themes;
				$settings['status'] = esc_attr( $_POST[ NB_SETTINGS_NAME ]['status'] );
				$settings['theme'] = esc_attr( $_POST[ NB_SETTINGS_NAME ]['theme'] );

				$theme_settings_name = 'theme_' . $settings['theme'] . '_settings';

				$settings[$theme_settings_name]['message'] = wp_kses_data( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['message'] );
				$settings[$theme_settings_name]['position'] = esc_attr( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['position'] );
				$settings[$theme_settings_name]['button_label'] = sanitize_text_field( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['button_label'] );
				$settings[$theme_settings_name]['button_link'] = esc_url_raw( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['button_link'] );
				$settings[$theme_settings_name]['button_target'] = esc_attr( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['button_target'] );
				$settings[$theme_settings_name]['background_color'] = sanitize_text_field( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['background_color'] );
				$settings[$theme_settings_name]['font_color'] = sanitize_text_field( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['font_color'] );
				$settings[$theme_settings_name]['font_size'] = absint( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['font_size'] );
				$settings[$theme_settings_name]['bar_control'] = esc_attr( $_POST[ NB_SETTINGS_NAME ][$theme_settings_name]['bar_control'] );

				$theme_settings = $notice_bar_themes->theme_settings( $settings['theme'] );

				if( '' === $settings[$theme_settings_name]['message'] ){

					$settings[$theme_settings_name]['message'] = $theme_settings['message'];

				}

				if( $settings[$theme_settings_name]['font_size'] < 1 ){

					$settings[$theme_settings_name]['font_size'] = $theme_settings['font_size'];

				}

            	update_option( NB_SETTINGS_NAME, $settings );

            	// set_transient( 'notice_bar_success_message', __( 'Settings saved.', 'notice-bar' ), 0 );

            	wp_redirect( admin_url( 'admin.php?page=notice-bar&success=true' ) );
			}

        }

    }

	/**
	 * Admin scripts.
	 *
	 * @since 1.0.0
	 */
	function admin_scripts( $hook ) {

        if ( 'toplevel_page_notice-bar' === $hook ) {

            wp_register_style( 'notice-bar-admin-style', NB_FILE_URL . '/assets/css/admin.css', false, '1.0.0' );

            wp_enqueue_style( 'notice-bar-admin-style' );

            wp_register_script( 'notice-bar-jquery-validation', NB_FILE_URL . '/assets/js/jquery.validate.min.js', array( 'jquery' ), '1.14.0', false );

            wp_enqueue_script( 'notice-bar-jquery-validation' );

            wp_register_script( 'notice-bar-scripts', NB_FILE_URL . '/assets/js/scripts.js', array( 'jquery' ), '1.0.0', false );

            wp_enqueue_script( 'notice-bar-scripts' );

        }

	}
}

new Notice_Bar_Settings();
