<?php
/**
 * PHPUnit bootstrap for sanitizer tests.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../../' );
}

if ( ! isset( $GLOBALS['cal_id_test_is_user_logged_in'] ) ) {
	$GLOBALS['cal_id_test_is_user_logged_in'] = false;
}

if ( ! isset( $GLOBALS['cal_id_test_current_user'] ) ) {
	$GLOBALS['cal_id_test_current_user'] = (object) array(
		'display_name' => '',
		'user_email'   => '',
	);
}

if ( ! isset( $GLOBALS['cal_id_test_rest_routes'] ) ) {
	$GLOBALS['cal_id_test_rest_routes'] = array();
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $errors = array();

		public function __construct( $code = '', $message = '' ) {
			if ( '' !== $code ) {
				$this->errors[ $code ] = array( $message );
			}
		}

		public function get_error_code() {
			return array_key_first( $this->errors );
		}
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data ) {
		return json_encode( $data );
	}
}

if ( ! function_exists( 'wp_generate_uuid4' ) ) {
	function wp_generate_uuid4() {
		return '00000000-0000-4000-8000-000000000000';
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'shortcode_atts' ) ) {
	function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
		return array_merge( $pairs, $atts );
	}
}

if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode() {}
}

if ( ! function_exists( 'rest_url' ) ) {
	function rest_url( $path = '' ) {
		return 'https://example.test/wp-json/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'is_user_logged_in' ) ) {
	function is_user_logged_in() {
		return ! empty( $GLOBALS['cal_id_test_is_user_logged_in'] );
	}
}

if ( ! function_exists( 'wp_get_current_user' ) ) {
	function wp_get_current_user() {
		return $GLOBALS['cal_id_test_current_user'];
	}
}

if ( ! function_exists( 'register_rest_route' ) ) {
	function register_rest_route( $namespace, $route, $args ) {
		$GLOBALS['cal_id_test_rest_routes'][] = array(
			'namespace' => $namespace,
			'route'     => $route,
			'args'      => $args,
		);
	}
}

if ( ! function_exists( 'get_block_wrapper_attributes' ) ) {
	function get_block_wrapper_attributes( $attrs = array() ) {
		$parts = array();
		foreach ( $attrs as $key => $value ) {
			$parts[] = sprintf( '%s="%s"', $key, $value );
		}
		return implode( ' ', $parts );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $text ) {
		return strip_tags( $text );
	}
}

require_once dirname( __DIR__, 2 ) . '/includes/class-sanitize.php';
require_once dirname( __DIR__, 2 ) . '/includes/class-render.php';
require_once dirname( __DIR__, 2 ) . '/includes/class-shortcode.php';
