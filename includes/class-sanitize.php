<?php
/**
 * Sanitization helpers.
 *
 * @package CalIDEventEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Event_Embed_Sanitize {

	/**
	 * Normalize an event path to owner/event or team/owner/event.
	 *
	 * @param mixed $raw Raw input.
	 * @return string|WP_Error
	 */
	public static function sanitize_event_path( $raw ) {
		$raw = is_scalar( $raw ) ? trim( (string) $raw ) : '';

		if ( '' === $raw ) {
			return new WP_Error( 'cal_id_event_path_empty', 'Event path is empty.' );
		}

		if ( preg_match( '#^[a-z][a-z0-9+.-]*://#i', $raw ) ) {
			$parsed = function_exists( 'wp_parse_url' ) ? wp_parse_url( $raw ) : parse_url( $raw );

			if ( empty( $parsed['scheme'] ) || 'https' !== strtolower( (string) $parsed['scheme'] ) ) {
				return new WP_Error( 'cal_id_event_path_invalid_scheme', 'Only https URLs are allowed.' );
			}

			if ( empty( $parsed['host'] ) || 'cal.id' !== strtolower( (string) $parsed['host'] ) ) {
				return new WP_Error( 'cal_id_event_path_invalid_host', 'Invalid host.' );
			}

			if ( ! empty( $parsed['query'] ) || ! empty( $parsed['fragment'] ) ) {
				return new WP_Error( 'cal_id_event_path_invalid_url', 'Query strings and fragments are not allowed.' );
			}

			$raw = isset( $parsed['path'] ) ? (string) $parsed['path'] : '';
		}

		$raw = trim( $raw );
		$raw = trim( $raw, " \t\n\r\0\x0B/" );

		if ( '' === $raw ) {
			return new WP_Error( 'cal_id_event_path_empty', 'Event path is empty.' );
		}

		if ( false !== strpos( $raw, '..' ) || false !== strpos( $raw, '\\' ) ) {
			return new WP_Error( 'cal_id_event_path_traversal', 'Invalid path.' );
		}

		if ( preg_match( '#\s#', $raw ) ) {
			return new WP_Error( 'cal_id_event_path_invalid_chars', 'Invalid path.' );
		}

		$patterns = array(
			'#^[A-Za-z0-9_-]+/[A-Za-z0-9_-]+$#',
			'#^team/[A-Za-z0-9_-]+/[A-Za-z0-9_-]+$#',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $raw ) ) {
				return $raw;
			}
		}

		return new WP_Error( 'cal_id_event_path_invalid_format', 'Invalid event path format.' );
	}

	/**
	 * Sanitize theme.
	 *
	 * @param mixed $raw Raw input.
	 * @return string
	 */
	public static function sanitize_theme( $raw ) {
		$value = strtolower( trim( (string) $raw ) );
		$allowed = array( 'light', 'dark', 'auto' );

		return in_array( $value, $allowed, true ) ? $value : 'auto';
	}

	/**
	 * Sanitize layout.
	 *
	 * @param mixed $raw Raw input.
	 * @return string
	 */
	public static function sanitize_layout( $raw ) {
		$value = strtolower( trim( (string) $raw ) );
		$allowed = array( 'inline', 'modal', 'floating' );

		return in_array( $value, $allowed, true ) ? $value : 'inline';
	}

	/**
	 * Sanitize brand color.
	 *
	 * @param mixed $raw Raw input.
	 * @return string
	 */
	public static function sanitize_brand_color( $raw ) {
		$value = strtolower( trim( (string) $raw ) );
		$named_colors = array(
			'black',
			'white',
			'blue',
			'red',
			'green',
			'yellow',
			'orange',
			'purple',
			'pink',
			'gray',
			'grey',
			'teal',
			'cyan',
			'magenta',
			'brown',
		);

		if ( preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/i', $value ) ) {
			return $value;
		}

		if ( in_array( $value, $named_colors, true ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Sanitize button text.
	 *
	 * @param mixed $raw Raw input.
	 * @return string
	 */
	public static function sanitize_button_text( $raw ) {
		$value = trim( wp_strip_all_tags( (string) $raw ) );
		$value = preg_replace( '/\s+/', ' ', $value );
		if ( function_exists( 'mb_substr' ) ) {
			$value = mb_substr( $value, 0, 60 );
		} else {
			$value = substr( $value, 0, 60 );
		}

		return '' !== $value ? $value : 'Book now';
	}

	/**
	 * Sanitize embed height.
	 *
	 * @param mixed $raw Raw input.
	 * @return int
	 */
	public static function sanitize_embed_height( $raw ) {
		$value = (int) $raw;

		if ( $value < 320 ) {
			return 320;
		}

		if ( $value > 1600 ) {
			return 1600;
		}

		return $value;
	}

	/**
	 * Sanitize UTM parameter.
	 *
	 * @param mixed $raw Raw input.
	 * @return string
	 */
	public static function sanitize_utm_param( $raw ) {
		$value = trim( (string) $raw );
		$value = preg_replace( '/[^A-Za-z0-9._~-]/', '', $value );

		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $value, 0, 100 );
		}

		return substr( $value, 0, 100 );
	}

	/**
	 * Sanitize boolean flag.
	 *
	 * @param mixed $raw Raw input.
	 * @return bool
	 */
	public static function sanitize_boolean_flag( $raw ) {
		return filter_var( $raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? true : false;
	}

	/**
	 * Sanitize prefill flag.
	 *
	 * @param mixed $raw Raw input.
	 * @return bool
	 */
	public static function sanitize_prefill_flag( $raw ) {
		return self::sanitize_boolean_flag( $raw );
	}
}
