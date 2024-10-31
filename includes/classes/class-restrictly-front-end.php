<?php
/**
 * Restrictly Front End class
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if( ! class_exists( 'Restrictly_Front_End' ) ) {
	class Restrictly_Front_End {
		
		public function __construct() {
		}
		
		/**
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			
			// We can style restricted content differently if we like
			add_filter( 'body_class', array( $this, 'body_class_filter' ) );
			// Filter the content
			add_filter( 'the_content', array( $this, 'filter_content' ) );
					
		}
		
		/**
		 * Add classes to body
		 * @since 1.0.0
		 */
		public function body_class_filter( $classes ) {
			// Add a class to the body if the content is restricted
			$user_can_access = restrictly_can_user_access();
			if( ! $user_can_access ) {
				$classes[] = 'restrictly-restricted-content';
			}
			return $classes;
		}
		
		/**
		 * Filter the content
		 * Check access rights and modify the content accordingly
		 * @since 1.0.0
		 */
		public function filter_content( $content ) {
			
			// Check that we're on a page or post and part of the main query
			if( ( is_singular() || is_archive() || is_front_page() ) && is_main_query() ) {
				$user_can_access = restrictly_can_user_access();
				if( ! $user_can_access ) {
					// If user can't access this content, check what action to take
					// Either redirect or display message
					
					// Apply filter
					$message = restrictly_get_restricted_message();
					$content = wpautop( esc_html( $message ) );
				}
			}
			
			return $content;
		}
		
	}
	
}