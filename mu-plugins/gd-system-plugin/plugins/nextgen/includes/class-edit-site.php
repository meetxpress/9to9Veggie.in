<?php
/**
 * NextGen block overrides
 *
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

/**
 * Block Editor Override Class.
 */
class Edit_Site {


	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart
		 add_action( 'admin_init', [ $this, 'nextgen_block_editor_assets' ] );
		// Temporary disable.
		// add_filter( 'script_loader_tag', [ $this, 'initialize_nextgen_block_editor' ], 10, 2 );
		// @codingStandardsIgnoreEnd

		add_action( 'init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'register_nextgen_pattern_categories' ] );
		add_action( 'admin_init', [ $this, 'remove_default_block_patterns' ] );

	}

	/**
	 * Register NextGen settings.
	 *
	 * @access public
	 */
	public function register_settings() {
		register_setting(
			'nextgen_admin_dashboard_shortcut_enabled',
			'nextgen_admin_dashboard_shortcut_enabled',
			[
				'type'              => 'boolean',
				'description'       => __( 'Setting to disable or enable NextGen administration dashboard shortcut.', 'nextgen' ),
				'sanitize_callback' => null,
				'show_in_rest'      => true,
				'default'           => true,
			]
		);

	}

	/**
	 * Remove theme supports for NextGen.
	 *
	 * @access public
	 */
	public function remove_default_block_patterns() {

		$registered_patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();

		array_walk(
			$registered_patterns,
			function( $item ) {

				if ( ! isset( $item['name'] ) ) {

					return;

				}

				if ( strpos( $item['name'], 'core/' ) === 0 ) {

					unregister_block_pattern( $item['name'] );

				}

			}
		);

	}

	/**
	 * Register pattern categories for NextGen.
	 *
	 * @access public
	 */
	public function register_nextgen_pattern_categories() {

		register_block_pattern_category( 'headline', [ 'label' => __( 'Headline', 'nextgen' ) ] );
		register_block_pattern_category( 'text', [ 'label' => __( 'Text', 'nextgen' ) ] );
		register_block_pattern_category( 'list', [ 'label' => __( 'List', 'nextgen' ) ] );
		register_block_pattern_category( 'image', [ 'label' => __( 'Image', 'nextgen' ) ] );
		register_block_pattern_category( 'gallery', [ 'label' => __( 'Gallery', 'nextgen' ) ] );
		register_block_pattern_category( 'contact', [ 'label' => __( 'Contact', 'nextgen' ) ] );
		register_block_pattern_category( 'call-to-action', [ 'label' => __( 'Call To Action', 'nextgen' ) ] );

	}

	/**
	 * Enqueue NextGen Block Editor scripts and styles.
	 */
	public function nextgen_block_editor_assets() {
		$default_asset_file = [
			'dependencies' => [],
			'version'      => GD_NEXTGEN_VERSION,
		];

		$asset_filepath = GD_NEXTGEN_PLUGIN_DIR . '/build/edit-site.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : $default_asset_file;

		// @codingStandardsIgnoreStart
		// Temporary disable.
		/*
		 wp_enqueue_script(
			 'nextgen-edit-site',
			 GD_NEXTGEN_PLUGIN_URL . 'build/edit-site.js',
			 $asset_file['dependencies'],
			 $asset_file['version'],
			 true
		 ); */
		// @codingStandardsIgnoreEnd

		wp_enqueue_style(
			'nextgen-edit-site',
			GD_NEXTGEN_PLUGIN_URL . 'build/style-edit-site.css',
			[],
			$asset_file['version']
		);
	}

	/**
	 * Override the @wordpress/edit-post initialization script.
	 *
	 * @param string $tag The inline script tag.
	 * @param string $handle The inline script handle.
	 *
	 * @return string The inline script tag.
	 */
	public function initialize_nextgen_block_editor( $tag, $handle ) {
		if ( 'wp-edit-post' === $handle ) {
			$tag = str_replace(
				'wp.editPost.initializeEditor',
				'nextgen[\'edit-site\'].initializeRender',
				$tag
			);
		}

		return $tag;
	}

}
