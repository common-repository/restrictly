<?php
/**
 * Functions for the admin
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback for header setting
 * @since 1.0.0
 */
function restrictly_page_header_callback( $args ) {
	$options = get_option( $args['section'] );
	$value = '';
	if( isset( $options[$args['id']] ) ) {
		// Ensure value is prefixed with #
		$value = '#' . str_replace( '#', '', $options[$args['id']] );
	}
}

/**
 * Callback for pages select setting
 * @since 1.0.0
 */
function restrictly_pages_select_callback( $args ) {
	$options = get_option( $args['section'] );
	$value = '';
	if( isset( $options[$args['id']] ) ) {
		$value = $options[$args['id']];
	}
	// Get all pages
	$pages = get_pages();
	
	// Iterate through the pages
	if( $pages ) { ?>
		<select name='<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]'>
			<option></option>
			<?php foreach( $pages as $page ) { ?>
				<option value='<?php echo $page -> ID; ?>' <?php selected( $value, $page -> ID ); ?>><?php echo $page -> post_title; ?></option>
			<?php } ?>
		</select>
	<?php }
	if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}

/**
 * Checkbox callback
 * @since 1.0.0
 */
function restrictly_checkbox_callback( $args ) {
	$options = get_option( $args['section'] );
	$value = '';
	if( isset( $options[$args['id']] ) ) {
		$value = $options[$args['id']];
	}
	$checked  = ! empty( $value ) ? checked( 1, $value, false ) : '';
	?>
	<input type='checkbox' name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]" <?php echo $checked; ?> value='1'>
	<?php
	if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}

/**
 * Callback for text setting
 * @since 1.0.0
 */
function restrictly_text_callback( $args ) {
	$options = get_option( $args['section'] );
	$value = '';
	if( isset( $options[$args['id']] ) ) {
		$value = $options[$args['id']];
	}
	?>
	<input type='text' name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]" value="<?php echo esc_attr( $value ); ?>" />
	<?php if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}

/**
 * Callback for select setting
 * @since 1.0.0
 */
function restrictly_select_callback( $args ) {
	$options = get_option( $args['section'] );
	$setting = '';
	if( isset( $options[$args['id']] ) ) {
		$setting = $options[$args['id']];
	}
	?>
		<select name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]">
			<?php foreach( $args['choices'] as $key=>$value ) { ?>
				<option value="<?php echo $key; ?>" <?php selected( $setting, $key ); ?>><?php echo $value; ?></option>
			<?php } ?>
		</select>
	<?php
	if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}

/**
 * Callback for multi select setting
 * @since 1.0.0
 */
function restrictly_multi_select_callback( $args ) {
	$options = get_option( $args['section'] );
	$setting = '';
	if( isset( $options[$args['id']] ) ) {
		$setting = $options[$args['id']];
	}
	if( ! is_array( $setting ) ) {
		$setting = array();
	}
	?>
		<select multiple name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>][]">
			<?php foreach( $args['choices'] as $key=>$value ) { ?>
				<option value="<?php echo $key; ?>" <?php selected( 1, in_array( $key, $setting ) ); ?>><?php echo $value; ?></option>
			<?php } ?>
		</select>
	<?php
	if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}

/**
 * Callback for textarea setting
 * @since 1.0.0
 */
function restrictly_textarea_callback( $args ) {
	$options = get_option( $args['section'] );
	$value = '';
	if( isset( $options[$args['id']] ) ) {
		$value = $options[$args['id']];
	}
	?>
	<textarea rows=5 style="width:100%;" name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]"><?php echo esc_attr( $value ); ?></textarea>
	<?php if( isset( $args['description'] ) ) { ?>
		<p class="description"><?php echo $args['description']; ?></p>
	<?php }
}