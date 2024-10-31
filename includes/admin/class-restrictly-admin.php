<?php
/**
 * Restrictly admin class
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin admin class
 **/
if( ! class_exists( 'Restrictly_Admin' ) ) {
	
	class Restrictly_Admin {
		
		public function __construct() {
			//
		}
		
		/**
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'admin_menu', array( $this, 'add_settings_submenu' ) );
			add_action( 'admin_init', array( $this, 'register_options_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			// add_filter( 'plugin_action_links_restrictly/restrictly.php', array( $this, 'filter_action_links' ), 10, 1 );
		}
		
		public function enqueue_scripts() {
			wp_enqueue_style( 'restrictly-admin-style', RESTRICTLY_PLUGIN_URL . 'assets/css/admin-style.css', array(), RESTRICTLY_PLUGIN_VERSION );
		}
		
		// Add the menu item
		public function add_settings_submenu() {
			add_options_page( __( 'Restrictly', 'restrictly' ), __( 'Restrictly', 'restrictly' ), 'manage_options', 'restrictly', array( $this, 'options_page' ) );
		}
		
		public function register_options_init() {
			register_setting( 'restrictly_options', 'restrictly_options_settings' );
			
			add_settings_section( 'restrictly_options_section', __( 'General settings', 'restrictly' ), array( $this, 'options_section_callback' ), 'restrictly_options' );
			
			$settings = restrictly_settings();
			if( ! empty( $settings ) ) {
				foreach( $settings as $setting ) {
					add_settings_field( 
						$setting['id'], 
						$setting['label'], 
						$setting['callback'],
						'restrictly_options',
						'restrictly_options_section',
						$setting
					);
				}
			}
			
		}
		
		public function options_section_callback() { 
			// echo '<p>' . __( 'Do you have a few seconds to <a target="_blank"  href="https://translate.wordpress.org/projects/wp-plugins/restrictly/stable">help with translating Restrictly into other languages</a>? Even if you just translated a couple of words, that would really help.', 'restrictly' ) . '</p>';
		}
		
		public function options_page() {
			$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'options';
			$title =  __( 'Restrictly', 'restrictly' );
			$tabs = array(
				'options'	=>	__( 'General', 'restrictly' )
			);
			$tabs = apply_filters( 'restrictly_settings_tabs', $tabs );
			?>			
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
				<div class="restrictly-outer-wrap">
					<div class="restrictly-inner-wrap">
						<h2 class="nav-tab-wrapper">
							<?php foreach( $tabs as $tab => $name ) {
								$class = ( $tab == $current ) ? ' nav-tab-active' : '';
								echo "<a class='nav-tab$class' href='?page=restrictly&tab=$tab'>$name</a>";
							} ?>
						</h2>
						
						<form action='options.php' method='post'>
							<?php
							settings_fields( 'restrictly_' . strtolower( $current ) );
							do_settings_sections( 'restrictly_' . strtolower( $current ) );
							submit_button();
							?>
						</form>
					</div><!-- .restrictly-inner-wrap -->
					<div class="restrictly-banners">
						<div class="restrictly-banner">
							<a target="_blank" href="https://catapultthemes.com/downloads/restrictly-pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=restrictly&utm_campaign=restrictly-pro"><img src="<?php echo RESTRICTLY_PLUGIN_URL . 'assets/images/restrictly-pro-ad-banner.png'; ?>" alt="" ></a>
						</div>
						<div class="restrictly-banner hide-dbpro">
							<a target="_blank" href="https://discussionboard.pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=restrictly&utm_campaign=dbpro"><img src="<?php echo RESTRICTLY_PLUGIN_URL . 'assets/images/discussion-board-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="restrictly-banner">
							<a target="_blank" href="https://gallery.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=restrictly&utm_campaign=gallery"><img src="<?php echo RESTRICTLY_PLUGIN_URL . 'assets/images/mgs-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="restrictly-banner">
							<a target="_blank" href="http://superheroslider.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=restrictly&utm_campaign=superhero"><img src="<?php echo RESTRICTLY_PLUGIN_URL . 'assets/images/shs-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="restrictly-banner">
							<a target="_blank" href="https://singularitytheme.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=ctdb&utm_campaign=singularity"><img src="<?php echo RESTRICTLY_PLUGIN_URL . 'assets/images/singularity-banner-ad.png'; ?>" alt="" ></a>
						</div>		
					</div>
				</div><!-- .restrictly-outer-wrap -->
			</div><!-- .wrap -->
			<?php
		}
	}
	
}

function restrictly_admin_init() {
	$RESTRICTLY_Admin = new RESTRICTLY_Admin();
	$RESTRICTLY_Admin -> init();
	do_action( 'restrictly_init' );
}
add_action( 'plugins_loaded', 'restrictly_admin_init' );
