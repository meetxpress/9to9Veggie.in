<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Default options save to option table
 */
$default_settings = array(
    'status' => 'disabled',
    'theme' => 'default',
    'theme_default_settings' => array(
        'message' => __( 'Notice bar for all your custom notifications for your site visitors. Tweak them as you like using flexible options.', 'notice-bar' ),
        'position' => 'top',
        'button_label' => __( 'Button', 'notice-bar' ),
        'button_link' => '#',
        'button_target' => '_self',
        'background_color' => '#dd3333',
        'font_color' => '#ffffff',
        'font_size' => 12,
        'bar_control' => 'always',
    ),
);
$default_new_settings = array(
    'enable' => 0,
    'layout' => 'layout-1',
    'layout_1' => array(
        'middle' => array
            (
            'notice_type' => 'plain-text',
            'notice_text' => __( 'Notice bar for all your custom notifications for your site visitors. Tweak them as you like using flexible options.', 'notice-bar' ),
            'slider' => array(
                'slides' => array( __( 'Notice bar for all your custom notifications for your site visitors. Tweak them as you like using flexible options.', 'notice-bar' ) ),
                'auto_start' => 1,
                'show_controls' => 0,
                'slide_duration' => ''
            ),
            'ticker' => array(
                'ticker_label' => '',
                'ticker_items' => array( __( 'Notice bar for all your custom notifications for your site visitors. Tweak them as you like using flexible options.', 'notice-bar' ) ),
                'ticker_direction' => 'ltr',
                'ticker_speed' => '',
                'ticker_pause' => ''
            ),
            'social_icons' => array(
                'label' => '',
                'icons' => array
                    (
                    'facebook' => array
                        (
                        'status' => 0,
                        'url' => ''
                    ),
                    'twitter' => array
                        (
                        'status' => 0,
                        'url' => ''
                    ),
                    'google-plus' => array
                        (
                        'status' => 0,
                        'url' => ''
                    ),
                    'instagram' => array
                        (
                        'status' => 0,
                        'url' => ''
                    ),
                    'linkedin' => array
                        (
                        'status' => 0,
                        'url' => ''
                    )
                )
            )
        )
    ),
    'display' => array(
        'display_position' => 'top-fixed',
        'close_action' => 1,
        'background_color' => '#dd3333',
        'font_color' => '#ffffff',
        'font_size' => '12',
        'social_icon_background' => '#222222',
        'social_icon_color' => '#fff',
        'ticker_label_background' => '#b61818'
    )
);
if ( is_multisite() ) {
    global $wpdb;
    $current_blog = $wpdb->blogid;

// Get all blogs in the network and activate plugin on each one
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        /**
         * Old Version Settings
         * @version 1.0.0
         */
        if ( !get_option( NB_SETTINGS_NAME ) ) {
            update_option( NB_SETTINGS_NAME, $default_settings );
        }
        if ( !get_option( 'nb_new_settings' ) ) {
            update_option( 'nb_new_settings', $default_new_settings );
        }

        /**
         * New Version Settings
         * @version 2.0.0 or higher
         */
        restore_current_blog();
    }
} else {
    if ( !get_option( NB_SETTINGS_NAME ) ) {
        update_option( NB_SETTINGS_NAME, $default_settings );
    }
    
    if ( !get_option( 'nb_new_settings' ) ) {
        update_option( 'nb_new_settings', $default_new_settings );
    }
}

	