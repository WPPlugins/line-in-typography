<?php
/**
 *	Line In Settings API base class.
 *
 *	Here Be Dragons! Other than the Prefix, don't change this unless you know what you're doing!
 *
 *	@version 0.5.6
 */
 
if ( !class_exists('LIT_LI_Settings_Base') ) {
	abstract class LIT_LI_Settings_Base implements LIT_Constants  {
		private		$_display_count = 0;
		/**
		 * Current group count for multiple settings groups (not currently used)
		 * @access  private
		 * @var 	int
		 */	
		private		$_group_count = 0;
		/**
		 * Keeps track of the currently selected admin display type
		 * @access  private
		 * @var 	string
		 */	
		private		$_type = 'meta'; // meta, tabs or none
		/**
		 * Array of allowed meta positions
		 * @access  private
		 * @var 	array
		 */	
		private		$_allowed_positions = array( 'normal', 'advanced', 'side' );
		/**
		 * Array of allowed display types using the standard WordPress admin display (meta|tabs|none)
		 * @access  private
		 * @var 	array
		 */	
		private		$_allowed_types = array( 'meta', 'tabs', 'none' );
		/**
		 * Array of options retreived from the database. Only accessible through getters and setters.
		 * @access  private
		 * @var 	array
		 */	
		private		$_options;
		
		private		$_sections;
		
		private		$_buttons;
		
		private		$_current_section = 0;
		private		$_tiny_mce;
		private		$_media_uploader = false;

		/**
		 * The overall plugin id. Set by the constants file.
		 * @access  protected
		 * @var 	string
		 */	
		protected	$plugin_id;
		/**
		 * The language id of the plugin. Set by the constants file
		 * @access  protected
		 * @var 	string
		 */	
		protected	$lang;
		/**
		 * An array options groups (currently limited to 1).
		 * @access  protected
		 * @var 	array
		 */	
		protected	$options_group;
		/**
		 * An array of tags NOT to be filtered by wp_kses. Used to allow <p> tags on tiny_mce input fields.
		 * @access  protected
		 * @var 	array
		 */	
		protected	$allowedtags;
		
		// When dealing with multi-page plugins
		protected	$default_page 		= false;
		protected	$default_section 	= 'default';

		
		
		public function __construct( $group = false, $section = false ) {
			$this->plugin_id = self::PLUGINID;
			$this->lang = self::LANG;

			if ( isset( $this->type ) && in_array($this->type, $this->_allowed_types) ) {
				$this->set_type( $this->type );
			}
			$this->_add_settings_group('validate');
			
			$this->default_group = ( $group ) ? $group : $this->plugin_id;
			$this->default_section = ( $section ) ? $section : 'default';


			
			add_action( 'init', array( &$this, 'options_init'), 9 );
			add_action( 'admin_init', array( &$this, 'load_settings'), 8 );
			add_action( 'admin_init', array( &$this, 'additional_settings'), 9 );
			
			add_action('admin_init', array( &$this, 'register_settings'), 8 );
			add_action('admin_head',  array( &$this,'load_tiny_mce'));	
		}
		
				
		/**
		 *	Loop through the sections 
		 */
		public function has_sections() {
			if ( !isset( $this->registered_sections ) ) {
				global $wp_settings_sections;
				$i = 1;
				foreach ( $wp_settings_sections[$this->default_group] as $id => $section ) {
					$this->registered_sections[$i]['id'] = $id;
					$this->registered_sections[$i]['title'] = $section['title'];
					$i++;
				}
			}		
			$this->_display_count++;
			
			if ( isset( $this->registered_sections[$this->_display_count] ) ) {
				return true;
			} else {
				$this->_display_count = 0;
				return false;
			}
		}	


		public function get_box_title() {
			return $this->registered_sections[$this->_display_count]['title'];
		}
		public function get_box_id() {
			return $this->registered_sections[$this->_display_count]['id'];
		}
		
	
		/**
		 * May be made protected in future. Right now, only supports 1 settings group per Settings Class
		 */
		private function _add_settings_group($callback) {

			$this->_group_count++;
			if ( !method_exists($this, $callback) ) {
				wp_die( $this->_self . " does not have a validate callback for this group. You have passed a method '$callback' that you have not defined in your extending settings class." );
			}	

			// Use the extending class name for the group name. Create an array to allow multiple register_settings call
			// on the same page.
			$this->options_group[] = array(
				//	'group' => $this->self . '-group-' . $this->_group_count,
				// Plugin ID + extending class name for the db options
				
				'options_name' => $this->plugin_id . '-' . $this->self . '-group-' . $this->_group_count,
				'callback' => $callback
			);
		}		
			
		protected function add_section( $section_name, $intro_callback, $meta_position = 'normal' ) {
			if ( in_array($meta_position, $this->_allowed_positions) ) {
				$position = $meta_position;
			} else {
				$position = 'normal';
			}
			if ( !method_exists($this, $intro_callback) ) {
				wp_die("You passed a callback to the " . $this->self . "->add_section() method that doesn't exist in your extending class " . $this->self . ". You'll need to define this callback method and it needs to be set to public.");
			}
			$this->_current_section++;
			// This should load in all of the sections and their associated fields.
			// The title should be used for the meta box title as well as the section title
			// The intro callback can also be used for the meta box id.
			$id = sanitize_title_with_dashes( $section_name );
			
			$this->_sections[$this->_current_section] = array(
				'id'		=> $id,
				'callback'	=> $intro_callback,
				'title' 	=> $section_name,
				'position' 	=> $position
			);


		}
		protected function add_setting( $id , $title = false, $label = true ) {

			if ( $label ) {
				$label =  $id;
			} else {
				$label = false;		
			}
			
			$this->_settings[$this->_current_section][] = array(
				'id' => $id,
				'callback' => $id,
				'title' => $title, 
				'label' => $label
			);
		}
		
		
		/**
		 *	Initiate the API
		 */
		
		public function register_settings() {


			register_setting( 
				$this->default_group,
				$this->options_group[0]['options_name'],
				array( &$this, $this->options_group[0]['callback'] )
			);
			$this->register_sections();
		}
		public function register_sections() {

			// If it's not an array, no sections have been defined.
			// Use default
			if ( !is_array( $this->_sections ) ) {
				$this->register_fields( $this->default_section, 0 );
				return;
			}
			$i = 1;
			foreach ( $this->_sections as $key => $section ) {
				$page = $this->default_group;

				add_settings_section(
					$section['id'],					
					$section['title'],
					array( &$this, $section['callback']),
					$this->default_group
				);
				$this->register_fields( $section['id'], $key );
				$i++;
			}
		}
		public function register_fields( $section_id, $key, $existing_page = false) {
			if ( $existing_page ) {
				$page = $this->default_page;
			} else {
				$page = $section_id;		
			}
			
			if ( isset( $this->_settings[$key] ) ) {
				foreach( $this->_settings[$key] as $setting ) {
					$label = false;
					if ( $setting['label'] ) {
						$label = array( 'label_for' =>  $this->get_id( $setting['label'] ) );
					}
					
					add_settings_field( 
						$setting['id'], 
						$setting['title'], 
						array( $this, $setting['callback']),
						$this->default_group, 
						$section_id,
						$label
					);
				}
				
				
			}
		}
		
		
		protected function set_type( $type ) {
			if ( !in_array($type, $this->_allowed_types) ) {
				return false;
			} else {
				$this->_type = $type;
				return true;
			}
		}
		public function get_type() {
			return $this->_type;
		}
		
		
		public function set_group( $new_page = false ) {
			$this->default_group = $new_page;
		}
		
		public function options_init() {
			

			// Update the allowed tags to include the paragraph tas
			global $allowedtags;
			$allowedtags['p'] = array();
			$this->allowedtags = $allowedtags;

		    // set options equal to defaults
		    $this->_options = get_option( $this->options_group[0]['options_name'] );
		    if ( false === $this->_options ) {
				$this->_options = $this->get_defaults();
		    }
		    if ( isset( $_GET['undo'] ) && !isset( $_GET['settings-updated'] ) && is_array( $this->get_option('previous') ) ) {
		    	$this->_options = $this->get_option('previous');
		    }
		    update_option( $this->options_group[0]['options_name'], $this->_options );		
		   
		}
		
		public function get_name( $name = false ) {
			
			if ( $name ) {
				return $this->options_group[0]['options_name'] . "[$name]";
			} else {
				return $this->options_group[0]['options_name'];
			}
			
		}
				
		public function get_id( $field = false ) {
			if ( $field ) {
				return $this->options_group[0]['options_name'] . "-$field";
			} else {
				return $this->_plugin_id;
			}
		}
		public function get_option( $name = false ) {
			if ( $name ) {
				if ( isset( $this->_options[$name] ) ) {
					return $this->_options[$name];
				} else {

					return false;
				}
			} else {
				return $this->_options;
			}
		
		}
		
		public function get_settings_fields() {
			settings_fields( $this->default_group );
		}
		
		public function get_settings( $method ) {
			
			foreach ( $this->registered_sections as $section ) {
			
				if ( $section['id'] == $method ) {
					
					LIT_do_settings_section( $this->default_group, $section['id'] );
				}
			} 
			
			
		}
		
		
		protected function _add_tiny_mce( $element ) {
			$this->_tiny_mce[] = $this->get_id( $element );
		}
		
		public function load_tiny_mce() {
		
			if ( $this->_tiny_mce != null ) {

			
				$elements = implode(',', $this->_tiny_mce);
				if (function_exists('wp_tiny_mce')) {
				  add_filter(
				  	'teeny_mce_before_init', 
				  		create_function('$a', '
						    $a["theme"] = "advanced";
						    $a["skin"] = "wp_theme";
						    $a["height"] = "200";
						    $a["width"] = "600";
						    $a["onpageload"] = "";
						    $a["mode"] = "exact";
						    $a["elements"] = "' . $elements . '";
						    $a["editor_selector"] = "mceEditor";
						    $a["plugins"] = "safari,inlinepopups,spellchecker";
							$a["theme_advanced_buttons1"] = "bold, italic, blockquote, separator, strikethrough,  undo, redo, link, unlink";
						    $a["forced_root_block"] = false;
						    $a["force_br_newlines"] = true;
						    $a["force_p_newlines"] = true;
						    $a["convert_newlines_to_brs"] = true;
					    return $a;')
					);
				
				 wp_tiny_mce(true);
				};

			}
		}
		
		public function add_button($type, $label, $id) {
			$this->_buttons[] = array(
				'type' 	=> $type,
				'label'	=> $label,
				'id'	=> $id
			);
		}
		
		public function get_buttons() {
			if ( $this->_buttons == null ) {
				submit_button('Update options', 'primary','submit', false); 
				submit_button('Reset options', 'secondary', 'reset', false); 
			} else {
				foreach ( $this->_buttons as $button ) {
					submit_button($button['label'], $button['type'], $button['id'], false);
				}
			}	
		}	
		
		public function update_cancel() {
			$this->add_button( 'primary', 'Update Options', $this->get_name('update') );
			$this->add_button( 'secondary', 'Cancel', $this->get_name('cancel') ); 
		}
		
		
		public function edit_delete($suffix = false) {
			
			
			echo "<input type='submit' value='Edit' class='button-secondary' name='" . $this->get_name('edit') . "[$suffix]' />";
			echo "<input type='submit' value='Delete' class='button-secondary' name='" . $this->get_name('delete') . "[$suffix]' />";
		}
		
		public function enqueue_thickbox() {
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}
		
		protected function set_media_uploader( ) {
			$this->_media_uploader = true;
			
		}
		
		protected function uploader_button($id) {
			
			?>
			<input id="btn-<?php echo $this->get_id( $id ); ?>" class='li-upload' type="submit" name="<?php echo $this->get_name( $id . '-button' ); ?>" value="Upload Image" />
			
			<?php
			
		}
		


		
		// This is run after all of the other initilisation. Good for actions or methods  that require
		// everything else to be set up first.
		public function additional_settings() {

			if ( $this->_media_uploader ) {

				$this->enqueue_thickbox();
			}
		}
		
		

		abstract function load_settings();
		abstract function get_defaults();
		abstract function validate( $data );
	}
	
	
}


if ( !function_exists('LIT_do_settings_section') ) {
	function LIT_do_settings_section($page, $section) {
		global $wp_settings_sections, $wp_settings_fields;
		
		if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
			return;
		
		echo "<h3>{$wp_settings_sections[$page][$section]['title']}</h3>\n";		
		call_user_func($wp_settings_sections[$page][$section]['callback'], $wp_settings_sections[$page][$section]);
		if ( !isset($wp_settings_fields) || 
			!isset($wp_settings_fields[$page]) || 
			!isset($wp_settings_fields[$page][$wp_settings_sections[$page][$section]['id']]) )   
		return;
		echo '<table class="form-table">';
		do_settings_fields($page, $wp_settings_sections[$page][$section]['id']);
		echo '</table>';
	}
}

	

?>