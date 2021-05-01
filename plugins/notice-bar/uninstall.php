<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Notice_Bar
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Cleanup plugin options.
delete_option( '_nb_plugin_settings' );
