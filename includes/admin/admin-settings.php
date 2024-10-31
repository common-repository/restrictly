<?php
/**
 * Functions and data for the admin
 * Includes our settings
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns an array of settings
 *
 * @since 1.0.0
 * @returns Array
 */
if( ! function_exists( 'restrictly_settings' ) ) {
	function restrictly_settings() {
		
		$levels = restrictly_get_restriction_levels();
		
		$settings = array(
			'enable_metafields' => array(
				'id'			=> 'enable_metafields',
				'label'			=> __( 'Set restrictions by page/post', 'restrictly' ),
				'callback'		=> 'restrictly_checkbox_callback',
				'description'	=> __( 'Enable the Restrictly metafields on posts and pages, allowing you to set restrictions on individual posts and pages. Disable this option to set restrictions globally on the whole site.', 'restrictly' ),
				'page'			=> 'restrictly_options',
				'section'		=> 'restrictly_options_settings',
			),
			'restriction_rule' => array(
				'id'			=> 'restriction_rule',
				'label'			=> __( 'Permitted roles', 'restrictly' ),
				'callback'		=> 'restrictly_multi_select_callback',
				'description'	=> __( 'Select the user roles permitted to access restricted content.', 'restrictly' ),
				'choices'		=> $levels,
				'page'			=> 'restrictly_options',
				'section'		=> 'restrictly_options_settings',
			),
			'restricted_message' => array(
				'id'			=> 'restricted_message',
				'label'			=> __( 'Restricted content message', 'restrictly' ),
				'callback'		=> 'restrictly_textarea_callback',
				'description'	=> __( 'Display this message to users who don\'t have permission to access content. This message will display if you are not using the Enable \'No Access\' page setting above.', 'restrictly' ),
				'page'			=> 'restrictly_options',
				'section'		=> 'restrictly_options_settings',
			),
		);
		
		$settings = apply_filters( 'restrictly_settings', $settings );
		
		return $settings;
	}

}