<?php
/**
 * Metaboxes
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if( ! class_exists( 'Restrictly_Metaboxes' ) ) {

	class Restrictly_Metaboxes {

		public function __construct() {
			//
		}
		
		/**
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_metabox_data' ) );
			add_action( 'edit_attachment', array( $this, 'save_attachment' ) );
		}
		
		/**
		 * Register the metabox
		 * @since 1.0.0
		 */
		public function add_meta_box() {
			
			// Get metaboxes here because custom post types are not available on init
			$metaboxes = restrictly_metaboxes();
			
			foreach( $metaboxes as $metabox ) {
				add_meta_box (
					$metabox['ID'],
					$metabox['title'],
					array( $this, $metabox['callback'] ),
					$metabox['screens'],
					$metabox['context'],
					$metabox['priority'],
					$metabox['fields']
				);
			}
			
		}
		
		/**
		 * Call the correct function for our metafield
		 * @since 1.0.0
		*/
		public function meta_box_callback( $post, $fields ) {

			wp_nonce_field( 'save_metabox_data', 'restrictly_metabox_nonce' );
			
			if( $fields['args'] ) {
				
				foreach( $fields['args'] as $field ) {
						
					switch( $field['type'] ) {
						
						case 'text':
							$this->metabox_text_output( $post, $field );
							break;
						case 'number':
							$this->metabox_number_output( $post, $field );
							break;
						case 'select':
							$this->metabox_select_output( $post, $field );
							break;
						case 'multi-select':
							$this->metabox_multi_select_output( $post, $field );
							break;
						case 'checkbox':
							$this->metabox_checkbox_output( $post, $field );
							break;
						case 'image':
							$this->metabox_image_output( $post, $field );
							break;
						case 'color':
							$this->metabox_color_output( $post, $field );
							break;
						case 'wysiwyg':
							$this->metabox_wysiwyg_output( $post, $field );
							break;
						case 'post':
							$this->metabox_post_output( $post, $field );
							break;
						case 'is_restricted':
							$this->metabox_is_restricted( $post, $field );
							break;
						case 'taxonomy':
							if( isset( $field['multi'] ) ) {
								$this->metabox_taxonomy_multi_output( $post, $field );
								break;
							} else {
								$this->metabox_taxonomy_output( $post, $field );
								break;
							}
						case 'divider':
							$this->metabox_divider_output();
							break;
						case 'html':
							$this->metabox_html_output( $field );
							break;
					
					}
						
				}
				
			}

		}
		
		/**
		 * Metabox callback for text type
		 * @since 1.0.0
		 */
		public function metabox_text_output( $post, $field ) {
			
			$value = get_post_meta( $post->ID, $field['ID'], true );
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="text" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr( $value ); ?>" >
			</div>
			<?php
		}
		
		/**
		 * Metabox callback for wysiwyg type
		 * @since 1.0.0
		 */
		public function metabox_wysiwyg_output( $post, $field ) {
			
			$value = get_post_meta( $post->ID, $field['ID'], true );
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<?php wp_editor (
					htmlspecialchars_decode( $value),
					$field['name'],
					array (
						"media_buttons" => false,
						"textarea_rows" => 5,
						"media_buttons" => true
					)
				); ?>
			</div>
			<?php
		}
		
		/**
		 * Callback for a divider
		 * @since 1.0.0
		 */
		public function metabox_divider_output() {
			?>
			<div class="divider"></div>
			<?php
		}
		
		/**
		 * Callback for html content
		 * @since 1.1.0
		 */
		public function metabox_html_output( $field ) {
			?>
			<h3><?php echo $field['title']; ?></h3>
			<?php
		}
		
		/**
		 * Callback for restricted media content
		 * @since 1.1.0
		 */
		public function metabox_is_restricted( $post, $field ) {
			$url = wp_get_attachment_url( $post->ID );
			// Get the URL without the site name
			$url = str_replace( get_site_url(), '', $url );
			$is_restricted = strpos( $url, 'restrictly' );
			if( false !== $is_restricted ) {
				_e( 'This file is restricted', 'restrictly' );
			} else {
				_e( 'This file is not restricted. To restrict access to it, upload it again and ensure you select the \'Restrict Access\' checkbox. You may wish to delete this version of the file first.', 'restrictly' );
			}
		}
		
		/**
		 * Metabox callback for select
		 * @since 1.0.0
		 */
		public function metabox_select_output( $post, $field ) {
			
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<?php if( $field['options'] ) {
						foreach( $field['options'] as $key => $value ) { ?>
							<option value="<?php echo $key; ?>" <?php selected( $field_value, $key ); ?>><?php echo $value; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}
		
		/**
		 * Metabox callback for number
		 * @since 1.0.0
		 */
		public function metabox_number_output( $post, $field ) {

			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				// Check if we're on the post-new screen
				global $pagenow;
				if( in_array( $pagenow, array( 'post-new.php' ) ) ) {
					// This is a new post screen so we can apply the default value
					$field_value = $field['default'];
				}
			}
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="number" min="<?php echo $field['min']; ?>" max="<?php echo $field['max']; ?>" step="<?php echo $field['step']; ?>" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr( $field_value ); ?>" >
			</div>
			<?php
		}

		/**
		 * Metabox callback for checkbox
		 * @since 1.0.0
		 */
		public function metabox_checkbox_output( $post, $field ) {
			$field_value = 0;
			
			// First check if we're on the post-new screen
			global $pagenow;
			if( in_array( $pagenow, array( 'post-new.php' ) ) ) {
				// This is a new post screen so we can apply the default value
				if( isset( $field['default'] ) ) {
					$field_value = $field['default'];
				}
			} else {
				$custom = get_post_custom( $post->ID );
				if( isset( $custom[$field['ID']][0] ) ) {
					$field_value = $custom[$field['ID']][0];
				}
			}
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<input type="checkbox" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="1" <?php checked( 1, $field_value ); ?>>
				<?php if( ! empty( $field['label'] ) ) { ?>
					<?php echo $field['label']; ?>
				<?php } ?>
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
			</div>
			<?php
		}
		
		/**
		 * Metabox callback for multi select
		 * @since 1.0.0
		 */
		public function metabox_multi_select_output( $post, $field ) {
			
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			
			// Make an array for values
			$values = array();
			if( $field_value ) {
				$values = explode( ',', $field_value );
			}
			?>
			
			<div class="restrictly_metafield restrictly_multiselect <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select multiple id="<?php echo $field['name']; ?>-select" name="<?php echo $field['name']; ?>-select">
					<?php if( $field['options'] ) {
						foreach( $field['options'] as $key => $value ) { 
							$selected = in_array( $key, $values ); ?>
							<option value="<?php echo $key; ?>" <?php echo selected( 1, $selected ); ?>><?php echo $value; ?></option>
						<?php }
					} ?>
				</select>
				<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr( $field_value ); ?>" >
			</div>
			<script>
				jQuery(document).ready(function($){
					$('#<?php echo $field['name']; ?>-select').on('change',function(){
						$('#<?php echo $field['name']; ?>').val($('#<?php echo $field['name']; ?>-select').val());
					});
				});
			</script>
			
			<?php
		}
		
		/**
		 * Metabox callback for post types
		 * @since 1.0.0
		 */
		public function metabox_post_output( $post, $field ) {
			
			global $post;
			
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			$temp = $post;
			$args = array (
				'post_type'			=> $field['post-type'],
				'posts_per_page'	=> -1
			);
			$options = new WP_Query( $args );
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<option value=""><?php _e( '-- Select --', 'super-hero-slider' ); ?></option>
					<?php if( $options->have_posts() ) {
						while( $options->have_posts() ) : $options->the_post(); ?>
							<option value="<?php echo $post->ID; ?>" <?php selected( $field_value, $post->ID ); ?>><?php the_title(); ?></option>
						<?php endwhile;
					}
					wp_reset_postdata();
					$post = $temp; ?>
				</select>
			</div>
			<?php
		}
		
		/**
		 * Metabox callback for single taxonomy select
		 * @since 1.0.0
		 */
		public function metabox_taxonomy_output( $post, $field ) {
			
			global $post;
			
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			$taxonomies = get_terms( $field['taxonomy'] );
			
			?>
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<option value=""><?php _e( '-- Select --', 'super-hero-slider' ); ?></option>
					<?php if( ! empty( $taxonomies ) ) {
						foreach( $taxonomies as $taxonomy ) { ?>
							<option value="<?php echo $taxonomy->term_id; ?>" <?php selected( $field_value, $taxonomy->term_id ); ?>><?php echo $taxonomy->name; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}
		
		/**
		 * Metabox callback for taxonomies
		 * @since 1.0.0
		 */
		public function metabox_taxonomy_multi_output( $post, $field ) {
			
			global $post;
			
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			
			// Make an array for values
			$values = array();
			if( $field_value ) {
				$values = explode( ',', $field_value );
			}
			$taxonomies = get_terms( $field['taxonomy'] );
			?>
			
			<div class="restrictly_metafield <?php echo $field['class']; ?>">
				<?php if( ! empty( $taxonomies ) ) { ?>
					<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
					<select multiple id="<?php echo $field['name']; ?>-select" name="<?php echo $field['name']; ?>-select">
							<?php foreach( $taxonomies as $taxonomy ) { 
								$selected = in_array( $taxonomy->term_id, $values ); ?>
								<option value="<?php echo $taxonomy->term_id; ?>" <?php echo selected( 1, $selected ); ?>><?php echo $taxonomy->name; ?></option>
							<?php } ?>
					</select>
					<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_attr( $field_value ); ?>" >
				<?php } ?>
			</div>
			<script>
				jQuery(document).ready(function($){
					$('#<?php echo $field['name']; ?>-select').on('change',function(){
						$('#<?php echo $field['name']; ?>').val($('#<?php echo $field['name']; ?>-select').val());
					});
				});
			</script>
			
			<?php
		}
		
		/**
		 * Save
		 * @since 1.0.0
		 */
		public function save_metabox_data( $post_id ) {
			
			// Check the nonce is set
			if( ! isset( $_POST['restrictly_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify the nonce
			if( ! wp_verify_nonce( $_POST['restrictly_metabox_nonce'], 'save_metabox_data' ) ) {
				return;
			}
			
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			// Check the user's permissions.
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			// Save all our metaboxes
			$metaboxes = restrictly_metaboxes();
			foreach( $metaboxes as $metabox ) {
				if( $metabox['fields'] ) {
					foreach( $metabox['fields'] as $field ) {
						
						if( $field['type'] != 'divider' ) {
							
							if( isset( $_POST[$field['name']] ) ) {
								if( $field['type'] == 'wysiwyg' ) {
									$data = $_POST[$field['name']];
								} else {
									$data = sanitize_text_field( $_POST[$field['name']] );
								}
								update_post_meta( $post_id, $field['ID'], $data );
							} else {
								delete_post_meta( $post_id, $field['ID'] );
							}
						}
					}
				}
			}
		}
		
		/**
		 * Save attachment metafield
		 * @since 1.0.0
		 */
		public function save_attachment() {
			global $post;
			if( isset( $_POST['restrictly_restrict_attachment'] ) ) {
				update_post_meta( $post->ID, 'restrictly_restrict_attachment', $_POST['restrictly_restrict_attachment'] );
			}
		}
	
		/**
		 * Enqueue styles and scripts
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_media();
		}

	}
	
	$Restrictly_Metaboxes = new Restrictly_Metaboxes();
	$Restrictly_Metaboxes->init();
	
}