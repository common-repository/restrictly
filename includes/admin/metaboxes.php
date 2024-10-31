<?php 


function restrictly_metaboxes() {
	
	$screens = array( 'page', 'post' );
	$screens = apply_filters( 'restrictly_filter_metabox_screens', $screens );
	$levels = restrictly_get_restriction_levels();
	
	$metaboxes = array (
		array (
			'ID'			=> 'restrictly_metabox',
			'title'			=> __( 'Restrictly Settings', 'restrictly' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> $screens,
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'restrictly_restrict_content',
					'name'		=> 'restrictly_restrict_content',
					'title'		=> __( 'Restrict content?', 'restrictly' ),
					'type'		=> 'checkbox',
					'class'		=> ''
				),
			)
		)
	);
	$metaboxes = apply_filters( 'restrictly_filter_posts_metaboxes', $metaboxes );
		
	$metaboxes[] = array (
		'ID'			=> 'restrictly_media_metabox',
		'title'			=> __( 'Restrictly Settings', 'restrictly' ),
		'callback'		=> 'meta_box_callback',
		'screens'		=> array( 'attachment' ),
		'context'		=> 'side',
		'priority'		=> 'low',
		'fields'		=> array (
			array (
				'ID'		=> 'restrictly_restrict_attachment',
				'name'		=> 'restrictly_restrict_attachment',
				'title'		=> __( 'Restrict access?', 'restrictly' ),
				'type'		=> 'is_restricted',
				'class'		=> ''
			),
		)
	);
		

	return $metaboxes;
	
}