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
		register_block_type(
			CAL_ID_EMBED_PLUGIN_DIR . 'src/block',
			array(
				'render_callback' => '__return_empty_string',
			)
		);
	}
}
