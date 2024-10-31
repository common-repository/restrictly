<?php
/**
 * Restrictly admin class to display notices
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin admin class
 **/
if( ! class_exists( 'Restrictly_Admin_Notices' ) ) {
	
	class Restrictly_Admin_Notices extends Restrictly_Admin {
		
		public function __construct() {
			//
		}
		
		/**
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.1
		 */
		public function init() {
			$options = get_option( 'restrictly_options_settings' );
			if( isset( $options['restriction_rule'] ) && ! is_array( $options['restriction_rule'] ) ) {
				// We've still got a simple value set for the rule, not an array
				add_action( 'admin_notices', array( $this, 'review_request' ) );
			}
		}
		
		/**
		 * Let user know that restriction rule uses multiselect now
		 * @since 1.0.1
		*/
		public function review_request() {
			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				__( 'Please note that you can now set multiple user roles in the Restriction rule field in Settings > Restrictly > General. Please update the setting now to ensure you continue to restrict access to your content as required.', 'restrictly' )
			);
		}
		
	}
	
}

function restrictly_admin_notices_init() {
	$Restrictly_Admin_Notices = new Restrictly_Admin_Notices();
	$Restrictly_Admin_Notices->init();
	do_action( 'restrictly_init' );
}
add_action( 'plugins_loaded', 'restrictly_admin_notices_init' );
