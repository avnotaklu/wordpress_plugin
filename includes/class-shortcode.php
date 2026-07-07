<?php
/**
 * Shortcode registration.
 *
 * @package CalIDEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Embed_Shortcode {

	/**
	 * Boot shortcode.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'cal_id_embed', array( __CLASS__, 'render_shortcode' ) );
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

		$atts = shortcode_atts( $defaults, $atts, 'cal_id_embed' );

		return Cal_ID_Embed_Render::render( self::normalize_atts( $atts ), 'frontend' );
	}

	/**
	 * Normalize shortcode keys to canonical attribute names.
	 *
	 * @param array $atts Shortcode attrs.
	 * @return array
	 */
	private static function normalize_atts( $atts ) {
		$map = array(
			'eventpath' => 'eventPath',
			'layout' => 'layout',
			'theme' => 'theme',
			'brandcolor' => 'brandColor',
			'buttontext' => 'buttonText',
			'embedheight' => 'embedHeight',
			'hideeventdetails' => 'hideEventDetails',
			'prefillenabled' => 'prefillEnabled',
			'utmsource' => 'utmSource',
			'utmmedium' => 'utmMedium',
			'utmcampaign' => 'utmCampaign',
			'utmcontent' => 'utmContent',
			'utmterm' => 'utmTerm',
		);

		$normalized = array();
		foreach ( $atts as $key => $value ) {
			$normalized[ $map[ strtolower( $key ) ] ?? $key ] = $value;
		}

		return $normalized;
	}
}
