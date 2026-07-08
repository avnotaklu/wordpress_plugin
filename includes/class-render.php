<?php
/**
 * Shared render layer.
 *
 * @package CalIDEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Embed_Render {

	/**
	 * Render an embed instance.
	 *
	 * @param array  $attributes Block or shortcode attributes.
	 * @param string $context Rendering context.
	 * @return string
	 */
	public static function render( $attributes = array(), $context = 'frontend' ) {
		$sanitized = self::sanitize_attributes( $attributes );
		$instance_id = self::generate_instance_id();
		$is_editor = 'editor' === $context;
		$is_valid_path = ! is_wp_error( $sanitized['event_path'] );

		self::enqueue_frontend_assets();

		$classes = array(
			'cal-id-embed',
			'layout-' . $sanitized['layout'],
			'theme-' . $sanitized['theme'],
		);

		$wrapper_attributes = array(
			'id' => $instance_id,
			'class' => implode( ' ', $classes ),
			'data-instance-id' => $instance_id,
			'data-event-path' => is_string( $sanitized['event_path'] ) ? $sanitized['event_path'] : '',
			'data-layout' => $sanitized['layout'],
			'data-theme' => $sanitized['theme'],
		);

		$wrapper = self::build_wrapper_attributes( $wrapper_attributes );
		$json_config = self::build_json_config( $sanitized );
		$output = array();
		$output[] = '<div ' . $wrapper . '>';
		$output[] = '<script type="application/json" class="cal-id-embed__config">' . self::encode_json_for_script( $json_config ) . '</script>';

		if ( '' === $sanitized['event_path_raw'] ) {
			$output[] = '<div class="cal-id-embed__placeholder">' . esc_html__( 'Enter an event path to preview.', 'cal-id-embed' ) . '<br />' . esc_html__( 'Use owner/event or team/owner/event.', 'cal-id-embed' ) . '</div>';
		} elseif ( ! $is_valid_path ) {
			$output[] = '<div class="cal-id-embed__placeholder">' . esc_html( $is_editor ? __( 'Unable to preview embed - check event path.', 'cal-id-embed' ) : __( 'Booking is temporarily unavailable. Please try again later.', 'cal-id-embed' ) ) . '</div>';
		} else {
			if ( 'inline' === $sanitized['layout'] ) {
				$output[] = '<div class="cal-id-embed__container" style="min-height:' . intval( $sanitized['embedHeight'] ) . 'px"></div>';
			} elseif ( 'modal' === $sanitized['layout'] ) {
				$output[] = '<button type="button" class="cal-id-embed__trigger" data-cal-id-trigger="' . esc_attr( $instance_id ) . '">' . esc_html( $sanitized['buttonText'] ) . '</button>';
				$output[] = '<div class="cal-id-embed__container" hidden></div>';
			} else {
				$output[] = '<div class="cal-id-embed__container"></div>';
			}
		}

		$output[] = '</div>';

		return implode( '', $output );
	}

	/**
	 * Sanitize and normalize attributes.
	 *
	 * @param array $attributes Raw attributes.
	 * @return array
	 */
	private static function sanitize_attributes( $attributes ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$event_path_raw = isset( $attributes['eventPath'] ) ? (string) $attributes['eventPath'] : '';
		$event_path = Cal_ID_Embed_Sanitize::sanitize_event_path( $event_path_raw );

		return array(
			'event_path_raw' => $event_path_raw,
			'event_path' => $event_path,
			'layout' => Cal_ID_Embed_Sanitize::sanitize_layout( $attributes['layout'] ?? 'inline' ),
			'theme' => Cal_ID_Embed_Sanitize::sanitize_theme( $attributes['theme'] ?? 'auto' ),
			'brandColor' => Cal_ID_Embed_Sanitize::sanitize_brand_color( $attributes['brandColor'] ?? '' ),
			'buttonText' => Cal_ID_Embed_Sanitize::sanitize_button_text( $attributes['buttonText'] ?? 'Book now' ),
			'embedHeight' => Cal_ID_Embed_Sanitize::sanitize_embed_height( $attributes['embedHeight'] ?? 600 ),
			'hideEventDetails' => Cal_ID_Embed_Sanitize::sanitize_boolean_flag( $attributes['hideEventDetails'] ?? false ),
			'prefillEnabled' => Cal_ID_Embed_Sanitize::sanitize_prefill_flag( $attributes['prefillEnabled'] ?? false ),
			'utmSource' => Cal_ID_Embed_Sanitize::sanitize_utm_param( $attributes['utmSource'] ?? '' ),
			'utmMedium' => Cal_ID_Embed_Sanitize::sanitize_utm_param( $attributes['utmMedium'] ?? '' ),
			'utmCampaign' => Cal_ID_Embed_Sanitize::sanitize_utm_param( $attributes['utmCampaign'] ?? '' ),
			'utmContent' => Cal_ID_Embed_Sanitize::sanitize_utm_param( $attributes['utmContent'] ?? '' ),
			'utmTerm' => Cal_ID_Embed_Sanitize::sanitize_utm_param( $attributes['utmTerm'] ?? '' ),
		);
	}

	/**
	 * Create JSON config.
	 *
	 * @param array $sanitized Sanitized attrs.
	 * @return array
	 */
	private static function build_json_config( $sanitized ) {
		return array(
			'eventPath' => is_string( $sanitized['event_path'] ) ? $sanitized['event_path'] : '',
			'layout' => $sanitized['layout'],
			'theme' => $sanitized['theme'],
			'brandColor' => $sanitized['brandColor'],
			'buttonText' => $sanitized['buttonText'],
			'embedHeight' => $sanitized['embedHeight'],
			'hideEventDetails' => $sanitized['hideEventDetails'],
			'prefillEnabled' => $sanitized['prefillEnabled'],
			'utmSource' => $sanitized['utmSource'],
			'utmMedium' => $sanitized['utmMedium'],
			'utmCampaign' => $sanitized['utmCampaign'],
			'utmContent' => $sanitized['utmContent'],
			'utmTerm' => $sanitized['utmTerm'],
			'prefillEndpoint' => rest_url( 'cal-id-embed/v1/prefill' ),
			'restNonce' => function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wp_rest' ) : '',
		);
	}

	/**
	 * Build wrapper attributes string.
	 *
	 * @param array $attributes Attributes.
	 * @return string
	 */
	private static function build_wrapper_attributes( $attributes ) {
		$parts = array();

		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$parts[] = trim( get_block_wrapper_attributes( array( 'class' => $attributes['class'] ) ) );
		} else {
			$parts[] = sprintf( 'class="%s"', esc_attr( $attributes['class'] ) );
		}

		unset( $attributes['class'] );

		foreach ( $attributes as $key => $value ) {
			$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return implode( ' ', $parts );
	}

	/**
	 * Encode JSON for script tag.
	 *
	 * @param array $data Data.
	 * @return string
	 */
	private static function encode_json_for_script( $data ) {
		$json = wp_json_encode( $data );
		return str_replace( '</', '<\/', $json );
	}

	/**
	 * Generate a unique instance id.
	 *
	 * @return string
	 */
	private static function generate_instance_id() {
		return 'cal-id-embed-' . wp_generate_uuid4();
	}

	/**
	 * Enqueue frontend assets when rendering outside the block editor.
	 *
	 * @return void
	 */
	private static function enqueue_frontend_assets() {
		$asset_file = CAL_ID_EMBED_PLUGIN_DIR . 'build/block/view.asset.php';
		$script_url = CAL_ID_EMBED_PLUGIN_URL . 'build/block/view.js';

		if ( ! file_exists( $asset_file ) ) {
			$asset_file = CAL_ID_EMBED_PLUGIN_DIR . 'src/block/view.asset.php';
			$script_url = CAL_ID_EMBED_PLUGIN_URL . 'src/block/view.js';
		}

		$asset = file_exists( $asset_file ) ? require $asset_file : array(
			'dependencies' => array(),
			'version' => CAL_ID_EMBED_VERSION,
		);

		wp_enqueue_script(
			'cal-id-embed-view',
			$script_url,
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
