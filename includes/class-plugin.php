<?php
/**
 * Plugin bootstrap and environment checks.
 *
 * @package CalIDEventEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cal_ID_Event_Embed_Plugin {

	/** @var self|null */
	private static $instance = null;

	/** @var bool */
	private $is_supported = true;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Activation hook.
	 *
	 * @return void
	 */
	public static function activate() {
		$php_ok = version_compare( PHP_VERSION, CAL_ID_EMBED_MINIMUM_PHP, '>=' );
		$wp_ok  = version_compare( get_bloginfo( 'version' ), CAL_ID_EMBED_MINIMUM_WP, '>=' );

		if ( ! $php_ok || ! $wp_ok ) {
			set_transient(
				'cal_id_event_embed_activation_error',
				array(
					'php_ok' => $php_ok,
					'wp_ok'  => $wp_ok,
				),
				30
			);
			return;
		}
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		require_once CAL_ID_EMBED_PLUGIN_DIR . 'includes/class-sanitize.php';
		require_once CAL_ID_EMBED_PLUGIN_DIR . 'includes/class-render.php';
		require_once CAL_ID_EMBED_PLUGIN_DIR . 'includes/class-shortcode.php';
		require_once CAL_ID_EMBED_PLUGIN_DIR . 'includes/class-block.php';
		add_action( 'admin_notices', array( $this, 'maybe_render_environment_notice' ) );

		$this->is_supported = $this->environment_is_supported();

		if ( ! $this->is_supported ) {
			return;
		}

		Cal_ID_Event_Embed_Block::init();
		Cal_ID_Event_Embed_Shortcode::init();
	}

	/**
	 * Check whether current environment satisfies minimum requirements.
	 *
	 * @return bool
	 */
	private function environment_is_supported() {
		return version_compare( PHP_VERSION, CAL_ID_EMBED_MINIMUM_PHP, '>=' ) && version_compare( get_bloginfo( 'version' ), CAL_ID_EMBED_MINIMUM_WP, '>=' );
	}

	/**
	 * Render admin notice if the environment is not supported.
	 *
	 * @return void
	 */
	public function maybe_render_environment_notice() {
		$activation_error = get_transient( 'cal_id_event_embed_activation_error' );

		if ( false === $activation_error && $this->is_supported ) {
			return;
		}

		delete_transient( 'cal_id_event_embed_activation_error' );

		$messages = array();

		if ( isset( $activation_error['php_ok'] ) && ! $activation_error['php_ok'] ) {
			$messages[] = sprintf(
				/* translators: %s: minimum PHP version. */
				esc_html__( 'Cal ID Event Embed requires PHP %s or later.', 'cal-id-event-embed' ),
				esc_html( CAL_ID_EMBED_MINIMUM_PHP )
			);
		}

		if ( isset( $activation_error['wp_ok'] ) && ! $activation_error['wp_ok'] ) {
			$messages[] = sprintf(
				/* translators: %s: minimum WordPress version. */
				esc_html__( 'Cal ID Event Embed requires WordPress %s or later.', 'cal-id-event-embed' ),
				esc_html( CAL_ID_EMBED_MINIMUM_WP )
			);
		}

		if ( empty( $messages ) ) {
			return;
		}

		echo '<div class="notice notice-error"><p>' . implode( ' ', $messages ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
