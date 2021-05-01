<?php
/**
 * NextGen logo menu
 *
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * Main Logo_Menu class
 *
 * @package NextGen
 */
class Logo_Menu {
	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'register_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'localize_scripts' ] );

	}

	/**
	 * Enqueue the script.
	 */
	public function localize_scripts() {

		wp_localize_script(
			'nextgen-logo-menu-script',
			'logoMenuData',
			(array) apply_filters(
				'nextgen_admin_links',
				[
					'admin' => get_admin_url(),
				]
			)
		);

	}

	/**
	 * Enqueue the script.
	 */
	public function register_scripts() {

		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		// Editor Script.
		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/logo-menu.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		wp_enqueue_script(
			'nextgen-logo-menu-script',
			GD_NEXTGEN_PLUGIN_URL . 'build/logo-menu.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true // Enqueue script in the footer.
		);

		wp_set_script_translations( 'logo-menu-script', 'nextgen', GD_NEXTGEN_PLUGIN_DIR . '/languages' );

		wp_enqueue_style(
			'nextgen-logo-menu-style',
			GD_NEXTGEN_PLUGIN_URL . 'build/style-logo-menu.css',
			[],
			$asset_file['version']
		);

	}
}
