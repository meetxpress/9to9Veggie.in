<?php
if ( !class_exists( 'Notice_bar_main' ) ) {

	class Notice_bar_main {

		/**
		 * New version settings initialization
		 * 
		 * @since 2.0.0
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'new_version_menu' ), 20 ); // adds new version notification bar option page
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) ); // registers css and js for new version admin settings 
			add_action( 'admin_post_nb_settings_save', array( $this, 'save_settings' ) ); // saves the new version settings
			add_action( 'admin_post_nb_restore_default_action', array( $this, 'restore_default_settings' ) ); // restores default settings for new version
			add_action( 'wp_footer', array( $this, 'new_notice_bar' ) ); // appends new notice bar in the frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'register_front_assets' ) ); // registers css and js for frontend 
			if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'notice-bar') ) {
				add_action( 'admin_notices', array( $this, 'new_version_notice' ) );
			}
		}

		/**
		 * New version menu settings 
		 * 
		 * @since 2.0.0
		 */
		public static function new_version_menu() {
			
				add_submenu_page( 'notice-bar', __( 'Notice Bar', 'notice-bar' ), __( 'Notice Bar', 'notice-bar' ), 'manage_options', 'notice-bar', array( 'Notice_bar_main', 'new_settings_page' ) );
			}

		/**
		 * Settings Page for new version
		 * 
		 * @since 2.0.0
		 */
		public static function new_settings_page() {
			include(NB_BASE_PATH . '/inc/backend/settings.php');
		}


		/**
		 * Registers admin css and js for new version
		 * 
		 * @since 2.0.0
		 */
		public static function register_admin_assets( $hook ) {
			if ( $hook == 'toplevel_page_notice-bar' || $hook == 'notice-bar_page_notice-bar-settings' ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'nb-new-admin-style', NB_FILE_URL . '/css/backend.css', array(), NB_VERSION );
				wp_enqueue_style( 'nb-fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), NB_VERSION );
				wp_enqueue_script( 'nb-new-admin-script', NB_FILE_URL . '/js/backend.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), NB_VERSION );
			} else {
				return;
			}
		}

		/**
		 * Saves settings
		 * 
		 * @since 2.0.0
		 */
		function save_settings() {
			if ( !empty( $_POST ) && wp_verify_nonce( $_POST['nb_settings_nonce_field'], 'nb-settings-nonce' ) ) {
				include(NB_BASE_PATH . '/inc/cores/save-settings.php');
			} else {
				die( 'No script kiddies please!!' );
			}
		}

		/**
		 * Returns default settings array 
		 * 
		 * @return array
		 * 
		 * @since 2.0.0
		 */
		public static function default_settings() {
			$default_settings = array(
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
			return $default_settings;
		}

		/**
		 * Retores default settings
		 * 
		 * @since 2.0.0
		 */
		function restore_default_settings() {
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'nb-restore-nonce' ) ) {
				$default_settings = $this->default_settings();
				update_option( 'nb_new_settings', $default_settings );
				$redirect_url = admin_url( 'admin.php?page=notice-bar&success=true&msg=2' );
				wp_redirect( $redirect_url );
				exit;
			}
		}

		/**
		 * Print array in pre format
		 * 
		 * @since 2.0.0
		 */
		public static function print_array( $array ) {
			echo "<pre>";
			print_r( $array );
			echo "</pre>";
		}

		/**
		 * Sanitizes multidemnsional array
		 * @param array $array
		 * @param array $sanitize_rule
		 * @return array
		 * 
		 * @since 2.0.0
		 */
		function sanitize_array( $array = array(), $sanitize_rule = array() ) {
			if ( !is_array( $array ) || count( $array ) == 0 ) {
				return array();
			}

			foreach ( $array as $k => $v ) {
				if ( !is_array( $v ) ) {

					$default_sanitize_rule = (is_numeric( $k )) ? 'html' : 'text';
					$sanitize_type = isset( $sanitize_rule[$k] ) ? $sanitize_rule[$k] : $default_sanitize_rule;
					$array[$k] = $this->nb_sanitize_value( $v, $sanitize_type );
				}
				if ( is_array( $v ) ) {
					$array[$k] = $this->sanitize_array( $v, $sanitize_rule );
				}
			}

			return $array;
		}

		function nb_sanitize_value( $value = '', $sanitize_type = 'text' ) {
			switch ( $sanitize_type ) {
				case 'html':
					$allowed_html = array(
						'a' => array(
							'href' => array(),
							'title' => array(),
							'target' => array()
						),
						'br' => array(),
						'em' => array(),
						'strong' => array(),
						'button' => array()
					);
					return wp_kses( $value, $allowed_html );
					break;
				default:
					return sanitize_text_field( $value );
					break;
			}
		}

		/**
		 * New Version Notice Bar in Frontend
		 * 
		 * @since 2.0.0
		 * 
		 */
		function new_notice_bar() {
			/**
			 * If old version is activated
			 */
			$nb_settings = get_option( 'nb_new_settings' );
			/**
			 * If notice bar is disabled
			 */
			if ( !isset( $nb_settings['enable'] ) ) {
				return;
			}
			if ( isset( $nb_settings['enable'] ) && $nb_settings['enable'] == 0 ) {
				return;
			}
			if ( !( isset( $_COOKIE['nb_notice_flag'] ) && $_COOKIE['nb_notice_flag'] == 'yes' && $nb_settings['display']['close_action'] == 'close') ) {

				include(NB_BASE_PATH . '/inc/frontend/front-notice-bar.php');
			}
		}

		/**
		 * New version CSS and JS for frontend
		 * 
		 * @since 2.0.0
		 */
		function register_front_assets() {
			$nb_settings = get_option( 'nb_new_settings' );
			$debug_mode = (isset($nb_settings['debug_mode']))?true:false;
			/**
			 * If notice bar is disabled
			 */
			if ( !isset( $nb_settings['enable'] ) ) {
				return;
			}
			if ( isset( $nb_settings['enable'] ) && $nb_settings['enable'] == 0 ) {
				return;
			}
			/**
			 * Frontend Styles
			 */
			wp_enqueue_style( 'nb-front-fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), NB_VERSION );
			if ( $debug_mode ) {

				wp_enqueue_style( 'nb-news-ticker-style', NB_FILE_URL . '/css/ticker-style.css', false, NB_VERSION );
				wp_enqueue_style( 'nb-bxslider-style', NB_FILE_URL . '/css/jquery.bxslider.css', false, NB_VERSION );
				wp_enqueue_style( 'nb-new-style', NB_FILE_URL . '/css/frontend.css', false, NB_VERSION );
			} else {
				wp_enqueue_style( 'nb-news-ticker-style', NB_FILE_URL . '/css/ticker-style.min.css', false, NB_VERSION );
				wp_enqueue_style( 'nb-bxslider-style', NB_FILE_URL . '/css/jquery.bxslider.min.css', false, NB_VERSION );
				wp_enqueue_style( 'nb-new-style', NB_FILE_URL . '/css/frontend.min.css', false, NB_VERSION );
			}

			/**
			 * Frontend Scripts
			 */
			wp_enqueue_script( 'nb-bx-slider', NB_FILE_URL . '/js/jquery.bxslider.min.js', array( 'jquery' ), NB_VERSION );
			if ( $debug_mode ) {
				wp_enqueue_script( 'nb-news-ticker', NB_FILE_URL . '/js/jquery.ticker.js', array( 'jquery' ), NB_VERSION );
				wp_enqueue_script( 'nb-new-script', NB_FILE_URL . '/js/nb-frontend.js', array( 'jquery', 'nb-bx-slider', 'nb-news-ticker' ), NB_VERSION );
			} else {
				wp_enqueue_script( 'nb-news-ticker', NB_FILE_URL . '/js/jquery.ticker.min.js', array( 'jquery' ), NB_VERSION );
				wp_enqueue_script( 'nb-new-script', NB_FILE_URL . '/js/nb-frontend.min.js', array( 'jquery', 'nb-bx-slider', 'nb-news-ticker' ), NB_VERSION );
			}
		}

		/**
		 * New Version Notice
		 * 
		 * @since 2.0.0
		 */
		function new_version_notice() {
			
				?>
				<!--<div class="notice error my-acf-notice is-dismissible" >
					<p><?php _e( 'Features before version 2.0.0 is no longer supported now. Also, font-size option will not be supported in the upcoming version.', 'notice-bar' ); ?>
				</div>-->


				<?php
			
		}

	}

	$new_notice_bar = new Notice_bar_main();
}

