<?php
/**
 * Block registration.
 *
 * @package CalIDEventEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Event_Embed_Block {

	/**
	 * Boot the block integration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	/**
	 * Register the block metadata and editor assets.
	 *
	 * @return void
	 */
	public static function register_block() {
		$block_path = CAL_ID_EMBED_PLUGIN_DIR . 'build/block';

		if ( ! file_exists( $block_path . '/block.json' ) ) {
			$block_path = CAL_ID_EMBED_PLUGIN_DIR . 'src/block';
		}

		register_block_type(
			$block_path,
			array(
				'render_callback' => array( __CLASS__, 'render_block' ),
			)
		);
	}

	/**
	 * Render block output.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public static function render_block( $attributes ) {
		return Cal_ID_Event_Embed_Render::render( $attributes, is_admin() ? 'editor' : 'frontend' );
	}
}
