<?php
/**
 * Plugin Name: NextGen
 * Plugin URI: https://www.godaddy.com
 * Description: Next Generation WordPress Experience
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Version: 1.4.2
 * Text Domain: nextgen
 * Domain Path: /languages
 * Tested up to: 5.6.0
 *
 * NextGen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with Content Management. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Content_Management
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

use WPaaS\StockPhotos\API;

defined( 'ABSPATH' ) || exit;

define( 'GD_NEXTGEN_VERSION', '1.0.0' );
define( 'GD_NEXTGEN_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'GD_NEXTGEN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once __DIR__ . '/includes/autoload.php';

/**
 * Plugin
 *
 * @package NextGen
 * @author  GoDaddy
 */
final class Plugin {

	use Singleton;

	const NEXTGEN_DISABLE_COOKIE = 'nextgen_disable';

	/**
	 * Class constructor.
	 */
	private function __construct() {

		add_filter( 'load_script_translation_file', [ $this, 'load_script_translation_file' ], 10, 3 );
		add_action( 'init', [ $this, 'init' ] );

	}

	/**
	 * NextGen Initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {
		/**
		 * Always enabled feedback modal if the person went through WPNUX onboarding.
		 * This will also only show on Gutenberg because the scripts are enqueued during enqueue_block_editor_assets.
		 */
		if ( ! empty( get_option( 'wpnux_export_data' ) ) || $this->should_activate() ) {

			new Feedback_Modal();

		}

		if ( ! $this->should_activate() ) {

			return;

		}

		add_action( 'enqueue_block_editor_assets', [ $this, 'register_styles' ] );

		load_plugin_textdomain( 'nextgen', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		$image_categories_api_class = class_exists( '\WPaaS\StockPhotos\API' ) ? new API() : false;

		new Auto_Updates();
		new Edit_Site();
		new Site_Design();
		new Site_Content();
		new Editor_Preference_Modal();
		new Publish_Guide();
		new NUX_Patterns();
		new Logo_Menu();
		new Image_Categories( $image_categories_api_class );
		new Media_Download();

	}

	/**
	 * Whether or not we meet the basic criteria of NextGen capable site.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	private function should_activate() {
		// This function could be called multiple time during a typical WordPress bootstrap.
		// eg. System plugin calls is_user_session_enabled() multiple time.
		static $should_activate = null;

		$should_activate = apply_filters( 'nextgen_force_load', $should_activate );

		if ( ! is_null( $should_activate ) ) {

			return (bool) $should_activate;

		}

		// Short circuit NextGen if disable cookie is set.
		if ( isset( $_COOKIE[ self::NEXTGEN_DISABLE_COOKIE ] ) ) {

			return $should_activate = false; //@codingStandardsIgnoreLine

		}

		// Did the user go through NUX onboarding AND still using Go?
		$has_go_theme_template = defined( 'GO_VERSION' ) && 'go' === get_option( 'stylesheet' ) && ! empty( get_option( 'wpnux_export_data' ) );
		$has_gutenberg_plugin  = defined( 'GUTENBERG_VERSION' ) || self::is_plugin_active( 'gutenberg/gutenberg.php' );
		$nextgen_query_arg     = filter_input( INPUT_GET, 'nextgen', FILTER_VALIDATE_BOOLEAN );
		$nextgen_const         = defined( 'GD_NEXTGEN_ENABLED' ) ? (bool) GD_NEXTGEN_ENABLED : false;

		$has_min_requirements = $has_go_theme_template && ! $has_gutenberg_plugin;
		$should_activate      = $has_min_requirements && ( ! is_null( $nextgen_query_arg ) ? $nextgen_query_arg : $nextgen_const );

		// If config flag is different, reconcile API with custom action event.
		if ( $should_activate !== $nextgen_const ) {

			/**
			 * Here we give 24 hours to the platform to catch up on GD Config.
			 * The cookie makes sure we don't load Nextgen when it was just flipped to false for 24 hours.
			 */
			if ( false === $nextgen_query_arg ) {

				setcookie( self::NEXTGEN_DISABLE_COOKIE, 1, time() + DAY_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN, is_ssl() );

			}

			do_action( 'nextgen_compatibility_change', $should_activate );

		}

		return $should_activate;

	}

	/**
	 * Override the translation filenames due to a bug in WP-CLI when using 'wp i18n make-json'.
	 *
	 * @see https://github.com/wp-cli/i18n-command/issues/177
	 *
	 * @param string|false $file   Path to the translation file to load. False if there isn't one.
	 * @param string       $handle Name of the script to register a translation domain to.
	 * @param string       $domain The text domain.
	 *
	 * @return string|false
	 */
	public function load_script_translation_file( $file, $handle, $domain ) {

		if ( 'nextgen' !== $domain ) {

			return $file;

		}

		$wp_scripts = wp_scripts();
		$rel_path   = str_replace( GD_NEXTGEN_PLUGIN_URL, '', $wp_scripts->registered[ $handle ]->src );
		$filename   = sprintf( '%s-%s-%s.json', $domain, determine_locale(), md5( $rel_path ) );

		return path_join( dirname( $file ), $filename );

	}

	/**
	 * Enqueue the styles.
	 */
	public function register_styles() {
		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/nextgen-editor.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_style(
			'nextgen-style',
			GD_NEXTGEN_PLUGIN_URL . 'build/style-common.css',
			[],
			$asset_file['version']
		);
	}

	/**
	 * Determines if a given plugin is active or not.
	 * Note: This is a wrapper for is_plugin_active() WordPress core method.
	 *
	 * @param string $basename Plugin basename.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_plugin_active( $basename ) {

		if ( ! function_exists( 'is_plugin_active' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		}

		return is_plugin_active( $basename );

	}

	/**
	 * Wheter or not the session is NextGen enabled.
	 * Used by the system plugin.
	 *
	 * @return bool
	 */
	public function is_user_session_enabled() {

		return $this->should_activate();

	}

}

Plugin::load();
