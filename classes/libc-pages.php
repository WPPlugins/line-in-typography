<?php
/**
 *	Line In Pages Classes
 *
 *	@version 0.1.3
 *	@author	Simon Fairbairn 
 */

abstract class LIT_LIBC_Page implements LIT_Constants {
	protected	$settings; 
	private 	$_do_settings = true;
	protected 	$page_slug = null;
	protected 	$plugin_id;
	

	public function __construct( $settings ) {
		$this->plugin_id = self::PLUGINID;
		$this->page_title = self::TITLE;
		$this->lang = self::LANG;
		$this->add_sideboxes = self::SIDEBOXES;	
		
		if ( defined( 'self::ICON' ) ) {
			$this->icon = self::ICON;
		} else {
			$this->icon = 'plugins';
		}
		
		if ( !$settings instanceof LIT_LI_Settings_Base ) {
			$this->set_do_settings( false );
		} else {
			$this->settings = $settings;
		}
		add_action('admin_init', array( &$this, 'setup' ), 12 );
	}
	protected function set_do_settings( $set ) {
		$this->_do_settings = $set;
	}
	protected function get_do_settings() {
		return $this->_do_settings;
	}

	public function set_page_title( $title ) {
		
		$this->page_title = $title;
		
	}
	public function get_options() {
		return $this->settings->get_option();
	}
	
	public function set_group( $group ) {
		$this->settings->set_group( $group );
	}

	/**
	 *	This page id is actually the page slug
	 *	
	 */
	public function set_page_slug( $id ) {
		$this->page_slug = $id;
	}
	public function set_page( $id ) {
		$this->page_id = $id;
		$this->settings->set_page( $id );
	}
	

	public function help() {
		if ( method_exists($this->settings, 'help') ) {
			return $this->settings->help();
		}
		
		return "Contextual help coming soon.";
	}

	abstract function the_page();

}


class LIT_LIBC_Tabs_Page extends LIT_LIBC_Page  {
	private $_tabs = null;
	
	public function setup() {
		if ( $this->get_do_settings() ) {
			while ( $this->settings->has_sections() ) {
				$this->_tabs[] = array(
					'title' => $this->settings->get_box_title(),
					'id' => $this->settings->get_box_id()
				);
				
			}
		}
		
	}

	public function get_page() {
		if ( $this->get_do_settings() ) {
			// Implement settings code
		}
				
	}
	

	
	
	protected function get_header() {
		//
		//
		//	MAYBE WE HAVE TO GET THE TITLE FROM THE SETTINGS API WE'RE HOLDING
		//
		//
	
		?>
		<div class='wrap line-in-plugin <?php echo $this->plugin_id; ?>'>
			<?php screen_icon( $this->icon ); ?>
		<h2 class='lif-title'>
			<span><?php _e( $this->page_title, $this->lang ); ?></span>
			<?php
			$i = 1;
			foreach ( $this->_tabs as $tab ) { ?>
				<a class="nav-tab hidden" href='#tab-<?php echo $i; ?>'><?php _e( $tab['title'], $this->lang ); ?></a>
				
			<?php 
			$i++;
			} ?>	
		</h2>
		
		<?php
	}
	
	protected function get_footer() {
	?>
		</div>
	
	<?php
	}
	
	public function the_page() {

		$this->get_header();
			 ?>
			 <form action="options.php" method="post">
			 	<?php $this->settings->get_settings_fields(); ?>
				
				 	<?php
				 	$i = 1;
				 	foreach ( $this->_tabs as $tab ) { ?>
				 		<div id='tab-<?php echo $i; ?>' class='lif-tab'>
				 			<?php //  ( $i == 1 ) ? $this->settings->get_settings_errors() : false; ?>
				 			<?php $this->settings->get_settings( $tab['id'] ); ?>
				 		</div>				 		
				 	<?php 
				 	$i++;
				 	} ?>	

					 <p class='submit'>
					 	<?php $this->settings->get_buttons(); ?>
					 </p>


			 </form>
		<?php 	
		$this->get_footer();
	}

}




class LIT_LIBC_Meta_Page extends LIT_LIBC_Page  {
	
	
//	public function setup() {
//		if ( $this->get_do_settings() ) {
//			while ( $this->settings->has_sections() ) {
//				$this->_tabs[] = array(
//					'title' => $this->settings->get_box_title(),
//					'id' => $this->settings->get_box_id()
//				);
//				
//			}
//		}
//
//	}
	
	
	public function setup() {

		if ( $this->get_do_settings() ) {
			$options['settings_api'] = true;
			$options['page'] = $this->page_slug;
			while ( $this->settings->has_sections() ) {
				$this->settings->get_box_title();
				$options['title'] = $this->settings->get_box_title();
				$options['callback'] = array( $this, $this->settings->get_box_id() );
				$this->_boxes['id'] = $this->settings->get_box_id();
				$this->_boxes['box'] = new LIT_LIBC_Meta_Box( $options );
			}
		}

	}

	public function __call($method, $args) {
		$this->_meta_head();
		$this->settings->get_settings( $method );

	}

	
	private function _meta_head() {
		echo "<style>#poststuff .postbox .inside h3 {display: none; background: none; border: 0; box-shadow: none; -moz-box-shadow: none; -webkit-box-shadow: none; -o-box-shadow: none; -ms-box-shadow: none; padding: 0; }</style>";
	}
	
	public function get_page() {
		if ( $this->get_do_settings() ) {
			// Implement settings code
		}
				
	}
	
	public function about_meta_html() {
		
		// This is where the default text for the About meta will go
		echo "
		<h3>My Dear Friends,</h3>";
		echo "<p class='alignright' style='margin-left: 8px'>" . get_avatar( 'simon@line-in.co.uk', $size = '96', $default = '<path_to_url>' ) . "</p>"; 
		echo "<p>My name is Simon Fairbairn and I have the great privilege of being employed as Chief Pixel Engineer and Executive Code Manipulator over at <a href='http://line-in.co.uk'>Line In Web Design</a>.</p>
		
		<p>Truly, it is a great time to be alive, what with all the fascinating new inventions being developed almost, it seems, on a daily basis. We have the motor car, electric light bulbs and (if I may be so bold, my current favourite) the Inter Web Network</p>
		
		<p>Of course, such major leaps in technology bring with them certain difficulties, which is why we here at <a href='http://line-in.co.uk'>Line In Web Design</a> have created a range of plugins and themes that we sincerely hope may assist you in navigating these strange and exciting new waters. </p>
		
		<p>It is our greatest wish that they prove to be useful to you, however, should you have any comments or suggestions then please don't hesitate to send me a digital telegraph to <a href='mailto:simon@line-in.co.uk'>simon@line-in.co.uk</a>.</p> 
		
		<p>And should you ever need any help with any sort of WordPress design or development, we are at your service.</p>
		
		<p>Yours With Deepest Regards,</p>
		
		<h3>Simon A. Fairbairn Esq.</h3>
		
		
			";
	}
	
		
	
	protected function get_header() {
		?>
		<div class='wrap line-in-plugin <?php echo $this->plugin_id; ?>'>
			<?php screen_icon( $this->icon ); ?>
			<h2><?php _e( $this->page_title,  $this->lang ); ?></h2>
		<?php
	}
	
	protected function get_footer() {
	?>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo  $this->page_slug; ?>');
			});
			//]]>
		</script>
	
	
	
	<?php
	
	
	}
	
	
	
	public function the_page() {

		$this->get_header();

			if ( true == $this->add_sideboxes ) {
				add_meta_box( $this->plugin_id . '-about', 'A Warm Letter To Our Users', array(&$this, 'about_meta_html'), $this->page_slug, 'side', 'core');
			}
			
			 ?>
			 <form action="options.php" method="post">
			 	<?php $this->settings->get_settings_fields(); ?>
				
				

				 <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				 <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
					
					
					<div id="before-meta"><?php // $this->before_meta(); ?></div>
				
					<div id="poststuff" class="metabox-holder<?php echo ' has-right-sidebar'; ?>">
						<div id="side-info-column" class="inner-sidebar">
							<?php  do_meta_boxes( $this->page_slug, 'side', null); ?>
						</div>
						<div id="post-body" class="has-sidebar">
							<div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes(  $this->page_slug, 'normal', null); ?>
							</div>
						</div>
					</div>
					<div id="after-meta"><?php // $this->after_meta(); ?></div>
				 <p class='submit clear'>

				 	
				 	<?php $this->settings->get_buttons(); ?>
				 </p>

			 </form>
		<?php 	
		$this->get_footer();
	}

}

?>