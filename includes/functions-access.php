<?php
/**
 * Restrictly functions for access
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is the current user allowed to access the content?
 * @since 1.0.0
 * @return Boolean
 */
if( ! function_exists( 'restrictly_can_user_access') ) {
	function restrictly_can_user_access() {
		$can_access = false;
		// Check if the content is restricted
		$restricted = restrictly_is_page_restricted();
		if( $restricted ) {
			// If there is a restriction set, check the rule for who's permitted to access the content
			$rules = restrictly_get_restriction_rule(); // Array of permitted roles
			// If there's no rule set, the user can view
			if( empty( $rules ) ) {
				$can_access = true;
			}
			// Get the user role
			$user_role = restrictly_get_current_user_role();
			if( ! empty( $user_role ) ) {
				// Check the logged in user
				if( in_array( $user_role, $rules ) || in_array( 'all', $rules ) ) {
					$can_access = true;
				}
			}
		} else {
			// If there's no restriction, the user is permitted to access the content
			$can_access = true;
		}
		
		$can_access = apply_filters( 'restrictly_filter_can_user_access', $can_access );
		return $can_access;
	}
}

/**
 * Is the restriction scope global?
 * @since 1.0.0
 * @return Boolean
 */
if( ! function_exists( 'restrictly_is_global_restriction') ) {
	function restrictly_is_global_restriction() {
		// Is the restriction mode global or by page / post?
		$options = get_option( 'restrictly_options_settings' );
		if( empty( $options['enable_metafields'] ) ) {
			// The scope is global
			return true;
		}
		return false;		
	}
}

/**
 * Is this page/post restricted?
 * @since 1.0.0
 * @return Boolean
 */
if( ! function_exists( 'restrictly_is_page_restricted') ) {
	function restrictly_is_page_restricted( $post_id=null ) {
		
		$is_restricted = false;
		
		$global = restrictly_is_global_restriction();
		if( $global ) {
			// If restriction is global, then page must be restricted
			$is_restricted = true;
		} else {
			global $post;
			if( ! $post_id ) {
				$post_id = get_the_ID();
			}
			$restricted = get_post_meta( $post_id, 'restrictly_restrict_content', true );
			
			if( ! empty( $restricted ) ) {
				// Page is restricted
				$is_restricted = true;
			}
		}
		// Filter this
		$is_restricted = apply_filters( 'restrictly_filter_is_page_restricted', $is_restricted, $post_id );

		return $is_restricted;
	}
}

/**
 * Get the rule for who can access the content
 * @since 1.0.0
 * @return Array
 */
if( ! function_exists( 'restrictly_get_restriction_rule') ) {
	function restrictly_get_restriction_rule( $post_id=null ) {
		// Get the permitted roles from the general settings
		$restriction_rule = restrictly_get_unfiltered_restriction_rule();
		// Filter it
		$restriction_rule = apply_filters( 'restrictly_filter_restriction_rule', $restriction_rule, $post_id );
		// Return it
		return $restriction_rule;
	}
}

/**
 * Get restricted content message
 * @since 1.0.0
 * @return String
 */
if( ! function_exists( 'restrictly_get_restricted_message') ) {
	function restrictly_get_restricted_message() {
		// Is the restriction mode global or by page / post?
		$options = get_option( 'restrictly_options_settings' );
		if( ! empty( $options['restricted_message'] ) ) {
			// Return the message
			return $options['restricted_message'];
		}
		return false;		
	}
}

/**
 * Get restriction options
 * @since 1.0.1
 * @return Array	$levels is a simple key=>value array of available roles
 */
if( ! function_exists( 'restrictly_get_restriction_levels') ) {
	function restrictly_get_restriction_levels() {
		$levels = array();
		
		// Need to check if function is available yet
		if( function_exists( 'get_editable_roles' ) ) {
			// Add all user roles
			$roles = get_editable_roles();
			// We've got some roles so we can pass the roles into our array
			foreach( $roles as $role=>$role_object ) {
				$levels[$role] = $role_object['name'];
			}
		} else {
			// Look for saved transient value
			$levels = get_transient( 'restrictly_levels' );
			// If we haven't found our roles yet, just add default roles
			if( empty( $levels ) ) {
				$levels = array(
					'subscriber'	=> __( 'Subscriber', 'restrictly' ),
					'contributor'	=> __( 'Contributor', 'restrictly' ),
					'editor'		=> __( 'Editor', 'restrictly' ),
					'author'		=> __( 'Author', 'restrictly' ),
					'administrator'	=> __( 'Administrator', 'restrictly' ),
				);
			}
		}
		
		$levels = apply_filters( 'restrictly_filter_restriction_levels', $levels );
		
		// Save the roles to a transient
		// This helps because sometimes we want to get roles, e.g. in metaboxes, before get_editable_roles is available
		set_transient( 'restrictly_levels', $levels );

		return $levels;
	}
}

/**
 * Get the current user role
 * @since 1.0.1
 * @return String
 */
if( ! function_exists( 'restrictly_get_current_user_role') ) {
	function restrictly_get_current_user_role() {
		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = ( array ) $user->roles;
			return $role[0];
		} else {
			// If the user isn't logged in
			return false;
		}
	}
}

/**
 * From version 1.0.1 of Restrictly, we converted restrictly_restrict_content from checkbox to multiselect
 * This just ensures that existing values are correctly carried over
 * We don't pass it through the restriction rule filter as that was only introduced in 1.0.1
 * We can also use this to get the permitted roles without recursively passing through restrictly_filter_restriction_rule
 * @since 1.0.1
 * @return Array
 */
if( ! function_exists( 'restrictly_get_unfiltered_restriction_rule') ) {
	function restrictly_get_unfiltered_restriction_rule() {
		// If the existing value is not an array, we get the global restriction rule
		// And apply that here
		$restriction_rule = array();
		$options = get_option( 'restrictly_options_settings' );
		if( isset( $options['restriction_rule'] ) ) {
			$restriction_rule = $options['restriction_rule'];
		}				
		// Since 1.0.1, $restriction_rule should be an array of permitted roles
		if( ! is_array( $restriction_rule ) ) {
			$restriction_rule = array( $restriction_rule );
		}
		return $restriction_rule;
	}
}



