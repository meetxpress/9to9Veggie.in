<?php
/**
 * NextGen Helpers
 *
 * @since 1.0.0
 * @package NextGen
 */

namespace GoDaddy\WordPress\Plugins\NextGen;

defined( 'ABSPATH' ) || exit;

trait Helper {

	/**
	 * Validate REST Nonce from the request
	 *
	 * @param string $cap Capability to check as well of the nonce.
	 *
	 * @return void
	 */
	public static function validate_rest_nonce( $cap ) {

		if ( isset( $cap ) && ! current_user_can( $cap ) ) {

			wp_send_json_error( 'User priviledge not high enough' );

			exit;

		}

		$result = wp_verify_nonce( $_SERVER['HTTP_X_WP_NONCE'], 'wp_rest' );

		if ( ! $result ) {

			wp_send_json_error( 'Invalid nonce' );

			exit;

		}

	}

	/**
	 * Get the base url for WPNUX API.
	 *
	 * @return string URL.
	 */
	public function wpnux_api_base() {
		$api_urls = [
			'local' => 'https://wpnux.test/api',
			'dev'   => 'https://wpnux.dev-godaddy.com/v2/api',
			'test'  => 'https://wpnux.test-godaddy.com/v2/api',
			'prod'  => 'https://wpnux.godaddy.com/v2/api',
		];

		$env = getenv( 'SERVER_ENV', true );

		$api_url = ! empty( $api_urls[ $env ] ) ? $api_urls[ $env ] : $api_urls['prod'];

		return untrailingslashit( (string) apply_filters( 'nextgen_wpnux_api_url', $api_url ) );
	}

}
