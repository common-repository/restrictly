<?php
/* 
Plugin Name: Restrictly
Plugin URI: https://catapultthemes.com/
Description: Restrict content simply
Version: 1.0.1
Author: Catapult Themes
Author URI: https://catapultthemes.com/
Text Domain: restrictly
Domain Path: /languages
*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function restrictly_load_plugin_textdomain() {
    load_plugin_textdomain( 'restrictly', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'restrictly_load_plugin_textdomain' );

/**
 * Define constants
 */
if ( ! defined( 'RESTRICTLY_PLUGIN_URL' ) ) {
	define( 'RESTRICTLY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'RESTRICTLY_PLUGIN_DIR' ) ) {
	define( 'RESTRICTLY_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'RESTRICTLY_PLUGIN_VERSION' ) ) {
	define( 'RESTRICTLY_PLUGIN_VERSION', '1.0.1' );
}
// Plugin Root File.
if ( ! defined( 'RESTRICTLY_PLUGIN_FILE' ) ) {
	define( 'RESTRICTLY_PLUGIN_FILE', __FILE__ );
}

/**
 * Load her up.
 */

require_once dirname( __FILE__ ) . '/includes/classes/class-restrictly-front-end.php';
require_once dirname( __FILE__ ) . '/includes/functions-access.php';
function restrictly_front_end_init() {
	$Restrictly_Front_End = new Restrictly_Front_End();
	$Restrictly_Front_End->init();
}
add_action( 'init', 'restrictly_front_end_init' );

if( is_admin() ) {
	require_once dirname( __FILE__ ) . '/includes/admin/admin-settings.php';
	require_once dirname( __FILE__ ) . '/includes/admin/admin-settings-callbacks.php';
	require_once dirname( __FILE__ ) . '/includes/admin/class-restrictly-admin.php';
	require_once dirname( __FILE__ ) . '/includes/admin/class-restrictly-admin-notices.php';
	// Only load metaboxes if metafields are enabled
	$options = get_option( 'restrictly_options_settings' );
	if( ! empty( $options['enable_metafields'] ) ) {
		require_once dirname( __FILE__ ) . '/includes/admin/metaboxes.php';
		require_once dirname( __FILE__ ) . '/includes/admin/class-restrictly-metaboxes.php';
	}
}