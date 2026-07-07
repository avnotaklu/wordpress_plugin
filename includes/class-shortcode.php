<?php
/**
 * Shortcode registration.
 *
 * @package CalIDEventEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Event_Embed_Shortcode {

	/**
	 * Boot shortcode.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'cal_id_event_embed', array( __CLASS__, 'render_shortcode' ) );
	}

	/**
	 * Render shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_shortcode( $atts ) {
		$defaults = array(
			'event_path' => '',
			'layout' => 'inline',
			'theme' => 'auto',
			'brand_color' => '',
			'button_text' => 'Book now',
			'embed_height' => 600,
			'hide_event_details' => false,
			'prefill_enabled' => false,
			'utm_source' => '',
			'utm_medium' => '',
			'utm_campaign' => '',
			'utm_content' => '',
			'utm_term' => '',
		);

		$atts = shortcode_atts( $defaults, $atts, 'cal_id_event_embed' );

		return Cal_ID_Event_Embed_Render::render(
			array(
				'eventPath' => $atts['event_path'],
				'layout' => $atts['layout'],
				'theme' => $atts['theme'],
				'brandColor' => $atts['brand_color'],
				'buttonText' => $atts['button_text'],
				'embedHeight' => $atts['embed_height'],
				'hideEventDetails' => $atts['hide_event_details'],
				'prefillEnabled' => $atts['prefill_enabled'],
				'utmSource' => $atts['utm_source'],
				'utmMedium' => $atts['utm_medium'],
				'utmCampaign' => $atts['utm_campaign'],
				'utmContent' => $atts['utm_content'],
				'utmTerm' => $atts['utm_term'],
			),
			'frontend'
		);
	}
}
