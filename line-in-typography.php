<?php
/*
Plugin Name: Line In Typography
Plugin URI: http://line-in.co.uk/
Description: This magical piece of markup monkery will make your mission to muster magnificent missives on your monitor much more manageable.
Author: Simon Fairbairn
Version: 0.3.5
Requires at least: 3.0
Author URI: http://line-in.co.uk/
License: GPL
*/
/**
 *	Imports the required base class full of plugin goodies
 */
$x = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$file = $x . 'plugin-options.php';
include "classes/libc-meta-box.php"; 
include "classes/libc-menus.php"; 
include "classes/libc-pages.php"; 
include "classes/libc-settings-api.php"; 
if ( file_exists( $file ) ) {
	include "plugin-options.php";
}

interface LIT_Constants {
	const VERSION	= '0.3.5';
	const PLUGINID	= 'line-in-typography';
	const TITLE		= 'Line In Typography';
	const SHORTNAME	= 'LIT';
	const ABBR		= 'LIT';
	const PARENT	= 'options-general.php';
	const LANG		= 'lineintypography';
	const SIDEBOXES	= true;
	const SCRIPTFILENAME	= 'line-in-typography';
	// Comma separated lists of dependencies
	const SCRIPTDEPS		= 'jquery';
	// False for no styles. Extension optional.
	const STYLEFILENAME		= false;		
}

/**
 *	Run on plugin activation. You can load your default options into the db at this point, but the Settings API
 *	will also take care of loading default options and it'll reduce redundancy if you only declare them once.
 */
register_activation_hook(__FILE__, array( 'LIT_LI_Plugin', 'install_plugin' ) );

/**
 *	Run on plugin deactivation. It's up to you whether you want to delete options at this point, but remember
 *	some users will deactivate plugins just to test for bugs so it's not usually recommended. 
 */
register_deactivation_hook(__FILE__, array( 'LIT_LI_Plugin', 'uninstall_plugin') );



/**
 * 	Initiate the class
 *	@return	object	My_Test_Plugin object
 */
function LIT_class_call() 
{
	
    return new LIT_LI_Plugin();
}
add_action( 'init', 'LIT_class_call' );

/**
 *  The main plugin class
 */
if ( !class_exists( 'LIT_LI_Plugin' ) ) {
	class LIT_LI_Plugin extends LIT_LIBC_Menus  {
		public $self = __CLASS__;
		public $file = __FILE__;
		
		public function _setup_plugin() {
			/** 
			 *	Prepare scripts  (load_scripts just registers them. They'll be 
			 *	enqueued as needed later).
			 *	Takes two paramaters: the first is required, and is the name of your JS file (no extension)
			 *	The second is an optional array of dependencies.
			 */
			if ( !is_admin() ) {
				$this->enqueue_scripts();
			}

			// $this->main_page = new LIT_LIBC_Meta_Page( new LIT_Main_Menu() );
			// $this->set_menu_page( $this->main_page );
			
				
			// Create classes (to use as a namespace) with all the HTML callbacks enclosed
			// Remember to pass the ID 
			$this->settings = new LIT_LIBC_Meta_Page( new LIT_Settings() );

			$submenu_page_options = array(
				'title' 	=> 'Line In Typography'
			);

			
			$this->set_submenu_page( $this->plugin_id, $this->settings, $submenu_page_options );
			
			if ( !is_admin() ) {
				add_action('wp_head', array( &$this, 'head' ) );
				add_action('wp_footer', array( &$this, 'foot' ) );
			
			} 
			// END EXAMPLE STUFF
		}
		
		
		
		public function _help() {
			return "Contextual help coming soon";
		}	
				
		
			

				
		static function install_plugin() {
			// Code for installation
		}
		
		static function uninstall_plugin() {
			// Code for deactivation
		}

		function head() { 
			self::$add_script = true; 
			
			$options = $this->settings->get_options();

			if ( isset( $options['upload_column_1'] ) &&  $options['upload_column_1'] != '' ) {
				$column_1 = $options['upload_column_1'];
			} else {
				$column_1 = $this->plugin_url . 'css/img/960-12px.png';
			}
			
			if ( isset( $options['upload_column_2'] ) &&  $options['upload_column_2'] != '' ) {
				$column_2 = $options['upload_column_2'];
			} else {
				$column_2 = $this->plugin_url . 'css/img/960-16px.png';
			}
						
			
			?>
			<style>
				.gr960-12 {
					background: url(<?php echo $column_1; ?>) 0 -4px !important;
					background-size: 100% !important;
				}
				.gr960-16 {
					background: url(<?php echo $column_2; ?>) 0 -4px !important;
					background-size: 100% !important;
				}
				
				#lit-vertical-rhythm {
					width: 100%;
					z-index: 100;
					position: absolute;
					top: 0;
					left: 0;	
				}
				#lit-buttons {
					position: fixed;
					top: 50px;
					right: 0;
					z-index: 200;
					background: #fff;
					padding: 15px;
					border: 1px solid #000;
					-moz-border-radius: 10px;
					-webkit-border-radius: 10px;
					border-radius: 10px;
					width: 400px;
					
				}
				
				.switchImages embed, .switchImages img, .switchImages object, .switchImages video, .switchImages iframe  {
					display: none !important;	
				}
				
								
				/* =ENDDEL */
				            
            
            </style>
		<?php }
		
		
		function foot() { 
			if ( ! self::$add_script )
				return;
			wp_print_scripts($this->plugin_id . '-scripts'); 
			
			$options = $this->settings->get_options();
	
		?>

			<script>
				/*
				 *	Variables:
				 *
				 *	containers are what the plugin is attached to
				 *
				 *	fontSizeContainer (default: body), for most of my sites it'll be wrap now
				 *	
				 *	Lines offset (default: 0), just in case you need to offset the lines
				 *
				 */
				jQuery(document).ready(function($) {
					var myOptions = {	
						pluginUrl			: "<?php echo $this->plugin_url; ?>",
						lineHeightContainer	: 'body',
						gridState			: 'on',
						lineState			: 'off',
						testHtml			: "<p><?php echo $options['paragraph-text']; ?></p>",
						showTestHtml		: '<?php echo $options['show-paragraph']; ?>',
						backgroundOffset	: <?php echo $options['background-offset']; ?>,
						lineHeight			: <?php echo $options['line-height']; ?>
					};
					$('<?php echo $options['elements']; ?>' ).LineInTypography(myOptions);
				});
			</script>

		<?php
		}
		
		public function save_postdata( $post_id ) {
		
		} 
	}
	
}

?>