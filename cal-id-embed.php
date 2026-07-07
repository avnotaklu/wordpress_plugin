<?php
/**
 * Plugin Name: Cal ID Embed
 * Plugin URI: https://cal.id/
 * Description: Embed hosted Cal ID event pages in WordPress.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Cal ID
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cal-id-embed
 *
 * @package CalIDEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAL_ID_EMBED_VERSION', '1.0.0' );
define( 'CAL_ID_EMBED_MINIMUM_PHP', '7.4' );
define( 'CAL_ID_EMBED_MINIMUM_WP', '6.0' );
define( 'CAL_ID_EMBED_PLUGIN_FILE', __FILE__ );
define( 'CAL_ID_EMBED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAL_ID_EMBED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CAL_ID_EMBED_PLUGIN_DIR . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'Cal_ID_Embed_Plugin', 'activate' ) );

Cal_ID_Embed_Plugin::instance();
