<?php
/**
 *	Line In Menus Base Class
 *
 *	You shouldn't need to touch this class other than to change the prefix.
 *
 *	@version 0.5.4
 *	@author	Simon Fairbairn 
 */



if ( !class_exists('LIT_LIBC_Menus') ) {
	
	abstract class LIT_LIBC_Menus implements LIT_Constants  {
		/**
		 * A way to keep track of whether this plugin is being used and only loading scripts on 
		 * those pages
		 * @access 	static
		 * @var 	boolean
		 */		
		static 		$add_script = false;
		static 		$add_style = false;	
		protected	$options = array();
		protected 	$options_page = null;
		protected 	$custom_parent_page = false;
		private		$submenu_page_boxes = null;
		
		/**
		 * An array of standard meta boxes. Only used if the add_meta_box method is used.
		 * @access  private
		 * @var 	array
		 */	
		private		$_standard_meta_boxes = null;
		private		$submenu_count = 0;
		/**
		 * The URL of the plugin
		 * @access 	protected
		 * @uses	plugins_url()
		 * @var 	string
		 */		
		protected 	$plugin_url = null;
		/**
		 * The plugin basename
		 * @access 	protected
		 * @uses	plugin_basename()
		 * @var 	string
		 */		
		protected 	$plugin_basename;
		/**
		 * The url of the scripts for this plugin
		 * @access 	protected
		 * @uses	Line_In_Typography::$plugin_url
		 * @var 	string
		 */		
		protected	$scripts_url;
		/**
		 * The url of the stylesheet for this plugin
		 * @access 	protected
		 * @uses	Line_In_Typography::$plugin_url
		 * @var 	string
		 */		
		protected	$styles_url;
		/**
		 * An arbitrary id identifier for this plugin.
		 * @access	protected
		 * @var		string
		 */ 
		protected 	$plugin_id;
		/**
		 * The name of the options key to be stored in the wp_options table.
		 * @access 	protected
		 * @uses	Line_In_Typography::$plugin_id
		 * @var 	string
		 */		
		protected	$options_name;
		/**
		 * The parent page that a submenu page is attached to. Needs to be set if using 
		 * @access 	protected
		 * @var 	string
		 */		
		protected 	$parent_page = null;
		/**
		 * The title for the plugin, to be used as a heading on the Options Page. 
		 * @access 	protected
		 * @usedby	LIPBC_Settings_API_With_Page::add_submenu_page()
		 * @var 	string
		 */		
		protected 	$plugin_title;
		/**
		 * An abbreviation of the title to be used on the menu itself
		 * @access 	protected
		 * @var 	string
		 */		
		protected 	$plugin_shortname;
		/**
		 * The lanugage identifier to be used when creating translations
		 * @access 	protected
		 * @var 	string
		 */		
		protected 	$lang;
		/**
		 * The version of the plugin, taken from the constant set at the top of the page
		 * @access 	protected
		 * @var 	array
		 */		
		protected 	$ver;
		/**
		 * Can be populated with an array of meta boxes, or left null if you don't want to add any
		 * @access 	protected
		 * @var 	array
		 */		
		protected 	$meta_boxes = null;
		/**
		 * Whether or not to show the settings link on the Plugins page.
		 * @access 	protected
		 * @var 	boolean
		 */		
		protected	$show_settings_link = true;
		/**
		 * Can be populated with an array menu page options 
		 * @access 	protected
		 * @var 	array
		 */		
		private 	$menu_page = null;
		/**
		 * Can be populated with an array of multiple submenu page options 
		 * @access 	protected
		 * @var 	array
		 */		
		protected 	$submenu_pages = null;
		/**
		 * Whether or not to add the sideboxes (about, etc.)
		 * @access 	protected
		 * @var 	array
		 */		
		protected 	$add_sideboxes = true;
		
		private		$_before_meta = false;
		private		$_after_meta = false;
		private		$_current_settings = 0;
		private		$_current_key = 0;
		private		$fileDir = false;
		private		$icon = false;
	
		private		$_menu_page_settings = null;
		private		$_menu_page_options = null;
	
	
		function __construct() {
			
			/**
			 *	Make a note of what page we're on.
			 */
			if ( !isset( $this->file ) ) {
				$this->_die("Your extending class must declare a property called \$file, which you should set equal to __FILE__, and this property must have minimum access rights of protected");
			}

			
			
			$this->plugin_id 		= self::PLUGINID;			
			$this->plugin_title 	= self::TITLE;
			$this->plugin_shortname = self::SHORTNAME;
			$this->parent_page		= self::PARENT;
			$this->add_sideboxes	= self::SIDEBOXES;
			$this->script_name		= self::SCRIPTFILENAME;
			$this->style_name		= self::STYLEFILENAME;
			
			$this->script_deps		= self::SCRIPTDEPS;
			
			if ( strstr(__CLASS__, 'LIT_') ) {
				//$this->_die("<p>You need to change the prefix on all of the pages of this (<strong>" . $this->plugin_title . "</strong>) plugin. I know, I know, it sucks and you just want to get on with the good stuff but until the world supports PHP5.3's namespacing, we can't have hundreds of plugins out there all with classes beginning with LIT_.</p><p>The simplest thing to do is open up every file and do a find and replace for 'LIT_'. Replace it with something more descriptive (the longer the better) so your prefix doesn't clash with other developer's prefixes</p>");
				
			}	
			
						
			
			if ( $this->script_deps ) {
				$this->script_deps = explode( ',', self::SCRIPTDEPS );
			}


			if ( defined( 'self::TYPE' ) ) {
				$this->type = self::TYPE;
			}
			if ( defined( 'self::FILEDIR' ) ) {
				$this->fileDir = self::FILEDIR;
			}
			if ( defined( 'self::ICON' ) ) {
				$this->icon = self::ICON;
			} else {
				$this->icon = 'plugins';
			}
			
		
			if ( isset( $this->type ) && $this->type == 'theme'  ) {
				$this->plugin_url		= get_template_directory_uri() .'/' . $this->fileDir;
			} else {
				$this->plugin_url		= plugins_url('', $this->file) . '/' . $this->fileDir;
			}
		
			$this->plugin_nonce		= $this->plugin_id . '_nonce';
			$this->options_name 	= $this->plugin_id . '_options';
			$this->scripts_url 		= $this->plugin_url . 'js/';
			$this->styles_url 		= $this->plugin_url . 'css/';
			/** 
			 *	Prepare scripts  (load_scripts just registers them. They'll be 
			 *	enqueued as needed later).
			 *	Takes two paramaters: the first is required, and is the name of your JS file (no extension)
			 *	The second is an optional array of dependencies.
			 */
			if ( $this->script_name ) 
				$this->load_scripts($this->script_name, $this->script_deps );			
			/** 
			 *	Prepare styles  (load_scripts just registers them. They'll be 
			 *	enqueued as needed later).
			 *	Takes two paramaters: the first is required, and is the name of your CSS file (no extension)
			 *	The second is an optional array of dependencies.
			 */
			if ( $this->style_name )
				$this->load_styles( $this->style_name );			
			
			if ( method_exists($this, '_setup_plugin') ) {
				$this->_setup_plugin();
			} else {
				wp_die("The class that extends LIBC_Pages must have a method _setup_plugin where you set up your plugin's options. This method must be declared either 'protected' or 'public'.");
			}
			
			
			/**
			 *	Most of this stuff is admin side only, so check that we're in the admin side.
			 */
			if ( is_admin() ) {
				if ( isset( $_GET['page'] ) ) 
					$this->page_id = $_GET['page'];
				if ( $this->_menu_page_settings !== null ) {

					$this->custom_parent_page = true;
					add_action('admin_menu', array( &$this, 'add_menu_page' ) );
				}
				if ( $this->submenu_pages !== null ) {
					add_action('admin_menu', array( &$this, 'add_submenu_pages' ) );
				}
				
				if ( $this->show_settings_link ) {

					if ( $this->plugin_basename != '' ) {
						add_filter("plugin_action_links_" . $this->plugin_basename, array( &$this, 'add_settings_link' ) );
					} 
				} 
			}
		}
		
		private function _die( $message ) {
			require_once ABSPATH.'/wp-admin/includes/plugin.php';
			deactivate_plugins( $this->file );
			
			wp_die( "<h3>This plugin has been deactivated because of the following error: </h3>" . $message );
		}
		
		
		/**
		 *	Load a custom 'settings' link.
		 *
		 *	@return array $links
		 */
		public function add_settings_link($links) { 
			if ( $this->custom_parent_page ) {
				$settings_link = '<a href="admin.php?page=' . $this->plugin_id . '">Settings</a>';
			} else {
				$settings_link = '<a href="options-general.php?page=' . $this->plugin_id . '">Settings</a>'; 
			}
			array_unshift($links, $settings_link); 
			return $links; 
		}
		
		/**
		 *	Load in your settings for your main menu page. Using this will override the 
		 *	parent page you have set in the Plugin Settings
		 *
		 *	@param	object	$settngs		A page settings object of type LIBC_Settings_API
		 *	@param	array	$options		Array of options for the menu page
		 *
		 *	@return void
		 */
		protected function set_menu_page( $settings, $options = false ) {
			if ( !$settings instanceof LIT_LIBC_Page ) {
				$this->_die( "<p>Your class " . get_class($settings) . " is not an extension of the LIT_Line_In_Page_Class. Please add the following to your class name: <code>class " . get_class( $settings ) . " <strong>extends LIT_Line_In_Page_Class</strong></code></p><p> The settings object passed must extend the LIT_Line_In_Page_Class</p>" );
			}
		
			$this->_menu_page_options['title'] 		= ( isset( $options['title'] ) 		) ? $options['title'] : $this->plugin_title;
			$this->_menu_page_options['icon_url'] 	= ( isset( $options['icon_url'] ) 	) ? $options['icon_url'] : false;
			$this->_menu_page_options['position']	= ( isset( $options['position'] ) 	) ? $this->menu_page['position'] : 100;		
			$this->_menu_page_settings = $settings;
		}		
		
		/* Menu Pages */
		public function add_menu_page() {
			// Menu slug is the plugin_id.
			$menu_page = add_menu_page( 
				$this->_menu_page_options['title'],
				$this->plugin_shortname,
				'manage_options',
				$this->plugin_id,
				array( $this->_menu_page_settings, 'the_page'),
				$this->_menu_page_options['icon_url'],
				$this->_menu_page_options['position'] 
			);
			
			
			$this->_menu_page_settings->set_page_slug( $menu_page );
			$this->_menu_page_settings->set_page_title( $this->_menu_page_options['title'] );

			//$options_page = add_menu_page( $title, $title, 'manage_options', $this->plugin_id, array( &$this, 'parent_page'), $icon_url , $this->menu_page['position']);		
			$this->parent_page = $this->plugin_id;

			add_action('admin_print_styles-' . $menu_page, array( &$this, 'enqueue_styles') );
			add_action('admin_print_styles-widgets.php', array( &$this, 'enqueue_styles') );
			add_action('admin_print_styles-' . $menu_page, array( &$this, 'enqueue_scripts') );		
			
			add_contextual_help( $menu_page, $this->_menu_page_settings->help()  );			
			
			/**	
			 *	Make a note 
			 */	
			// $this->selected_page[$this->plugin_id]['id'] = $options_page;
			// $this->selected_page[$this->plugin_id]['settings'] = $this->menu_page_settings;
		}
		
		
		
		protected function set_submenu_page( $page_id, $settings, $options ) {
				


			$this->submenu_pages[$this->submenu_count]['settings'] = $settings; 
			$this->submenu_pages[$this->submenu_count]['options'] = $options;
			$this->submenu_pages[$this->submenu_count]['options']['id'] = $page_id;
			$this->submenu_pages[$this->submenu_count]['options']['title']	= ( isset( $options['title'] ) ) ? $options['title'] : $this->plugin_title;
			$this->submenu_count++;
		}
		
		public function add_submenu_pages() {
			$pages = 0;
			foreach( $this->submenu_pages as $submenu_key => $page ) {
				
				
				if ( isset ( $page['options']['special'] ) && $page['options']['special' ] == 'theme' ) {

					$page_slug = add_theme_page(
						__( $page['options']['title'], $this->lang ),   // Name of page
						__( $page['options']['title'], $this->lang),   // Label in menu
						'edit_theme_options',                    // Capability required
						'theme_options',                         // Menu slug, used to uniquely identify the page
						array( $page['settings'], 'the_page' ) // Function that renders the options page
					);
					$page['id'] = 'theme_options';
				} else {
					$page_slug = add_submenu_page(
						$this->parent_page, 
						$page['options']['title'], 
						$page['options']['title'], 
						'manage_options', 
						$page['options']['id'], 
						array( $page['settings'], 'the_page' )
					);
				}

				$page['settings']->set_group( $page_slug );
				
				$page['settings']->set_page_title( $page['options']['title'] );
				$page['settings']->set_page_slug( $page_slug );
				
				add_action('admin_print_styles-' . $page_slug, array( &$this, 'enqueue_styles') );
				add_action('admin_print_styles-widgets.php', array( &$this, 'enqueue_styles') );
				add_action('admin_print_styles-' . $page_slug, array( &$this, 'enqueue_scripts') );		

				add_contextual_help( $page_slug, $page['settings']->help()  );	
				
				// $this->selected_page[$page['id']]['id'] = $page_slug;
				// $this->selected_page[$page['id']]['settings'] = $this->submenu_page_settings[$submenu_key]; 
				// $this->selected_page[$page['id']]['options'] = $page;
			

			}
		}
		
			
		
		public function load_scripts( $file_name, $deps = false ) {
			

			if ( WP_DEBUG ) {
				$ver = time();
			} else {
				$ver = $this->ver;
			}
			
			if ( !isset( $this->scripts_url ) ) {
				$this->scripts_url = $this->plugin_url . 'js/';
			}
			
			if ( is_admin() ) {
				wp_enqueue_script( 'libc-base-script', $this->scripts_url . 'libs/libc.js', $deps, '0.5.4', true );
			}
			
			wp_register_script( $this->plugin_id . '-scripts', $this->scripts_url  . $file_name . '.js', $deps, $ver, true );
			
		}
		public function load_styles( $file_name, $deps = false ) {
			if ( WP_DEBUG ) {
				$ver = time();
			} else {
				$ver = $this->ver;
			}
			if ( !isset( $this->styles_url ) ) {
				$this->styles_url = $this->plugin_url . 'css/';
			}
			wp_register_style( $this->plugin_id . '-styles', $this->styles_url  . $file_name . '.css', $deps, $ver, 'all');
			
		}
		
		public function enqueue_scripts() {
			if ( !self::$add_script ) 
				wp_enqueue_script( $this->plugin_id . '-scripts' );
			self::$add_script = true;
		}
		
		public function enqueue_styles() {
			if ( !self::$add_style ) 
				wp_enqueue_style( $this->plugin_id . '-styles' );
			self::$add_style = true;			
		}
		
	}
}









?>