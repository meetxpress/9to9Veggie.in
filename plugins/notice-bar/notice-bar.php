<?php

/**
 * Plugin Name: Notice Bar
 * Description: A simple plugin to show notice bar in WordPress sites.
 * Plugin URI: http://wensolutions.com/plugins/notice-bar/
 * Author: WEN Solutions
 * Author URI: http://wensolutions.com
 * Version: 2.0.8
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: notice-bar
 * Domain Path: languages
 *
 * @package Notice_Bar
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


if ( !class_exists( 'Notice_Bar' ) && !class_exists( 'Notice_Bar_Pro' ) ) :

	define( 'NB_BASE_PATH', dirname( __FILE__ ) );
	define( 'NB_FILE_PATH', __FILE__ );
	define( 'NB_THEME_PATH', NB_BASE_PATH . '/themes' );
	define( 'NB_SETTINGS_NAME', '_nb_plugin_settings' );
	define( 'NB_FILE_URL', plugins_url( '', __FILE__ ) );
	define( 'NB_VERSION', '2.0.7' );

	/**
	 * Main Class.
	 */
	class Notice_Bar {

		/**
		 * Plugin instance.
		 *
		 * @var Notice_Bar The single instance of the class.
		 * @since 1.0.0
		 */
		private static $instance = null;

		/**
		 * Main Notice_Bar Instance.
		 *
		 * Ensures only one instance of Notice_Bar is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @return Notice_Bar - Main instance.
		 */
		public static function get_instance() {
			if ( !isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			$this->includes();
			$this->init_hooks();
			add_action( 'admin_init', array($this, 'child_plugin_has_parent_plugin') );


		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function init_hooks() {

			// Load plugin text domain for localization.
			load_plugin_textdomain( 'notice-bar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Add settings link in plugin listing.
			$plugin = plugin_basename( __FILE__ );
			add_filter( 'plugin_action_links_' . $plugin, array( $this, 'add_settings_link' ) );
		}


		function child_plugin_has_parent_plugin() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) &&  is_plugin_active( 'notice-bar-pro/notice-bar-pro.php' ) ) {
			// add_action( 'admin_notices', array($this,'child_plugin_notice') );

			deactivate_plugins( plugin_basename( __FILE__ ) ); 
			wp_die( 'Sorry, but pro version of this plugin is already activated. Please deactivate it to successfully activate the free version.' );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

	
		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			include NB_BASE_PATH . '/core/backend/settings.php';
			include NB_BASE_PATH . '/class-notice-bar-main.php';
		}

		/**
		 * Activate.
		 *
		 * @since 1.0.0
		 */
		public static function activate() {

			/**
			 * For New version settings
			 * @version 2.0.0
			 */
			include(NB_BASE_PATH . '/inc/cores/activation.php');
		}

		/**
		 * Deactivate.
		 *
		 * @since 1.0.0
		 */
		public static function deactivate() {
			
		}

		/**
		 * Links in plugin listing.
		 *
		 * @since 1.0.0
		 *
		 * @param array $links Array of links.
		 * @return array Modified array of links.
		 */
		public static function add_settings_link( $links ) {
			$url = add_query_arg( array(
				'page' => 'notice-bar',
					), admin_url( 'admin.php' )
			);
			$settings_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'notice-bar' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

	}

	// Trigger plugin instance.
	add_action( 'plugins_loaded', array( 'Notice_Bar', 'get_instance' ) );

	// Activation hook.
	register_activation_hook( __FILE__, array( 'Notice_Bar', 'activate' ) );

	// Deactivation hook.
	register_deactivation_hook( __FILE__, array( 'Notice_Bar', 'deactivate' ) );

else:
	add_action( 'admin_notices', 'notice_bar_child_plugin_notice' );
	function notice_bar_child_plugin_notice(){
		$class = 'notice notice-error is-dismissible';
		$message = __( 'Notice Bar Pro is activated. Notice Bar (Free version) will be deactivated now.', 'notice-bar' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		deactivate_plugins( plugin_basename( __FILE__ ) );  
	}
endif;
