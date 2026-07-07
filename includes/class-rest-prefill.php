<?php
/**
 * REST endpoint for logged-in prefill data.
 *
 * @package CalIDEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Embed_Rest_Prefill {

	/**
	 * Boot the REST route.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			'cal-id-embed/v1',
			'/prefill',
			array(
				'methods'  => 'GET',
				'callback' => array( __CLASS__, 'handle_request' ),
				'permission_callback' => array( __CLASS__, 'permissions_check' ),
			)
		);
	}

	/**
	 * Check permissions.
	 *
	 * @return true|WP_Error
	 */
	public static function permissions_check() {
		if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) {
			return true;
		}

		return new WP_Error( 'rest_forbidden', 'Authentication required.', array( 'status' => 401 ) );
	}

	/**
	 * Handle request.
	 *
	 * @return array
	 */
	public static function handle_request() {
		$user = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;

		return array(
			'name'  => $user && isset( $user->display_name ) ? $user->display_name : '',
			'email' => $user && isset( $user->user_email ) ? $user->user_email : '',
		);
	}
}
