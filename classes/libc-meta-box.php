<?php

/**
 *	Line In Meta Box Class
 *
 *	@version 0.1.3
 *	@author	Simon Fairbairn 
 */


class LIT_LIBC_Meta_Box implements LIT_Constants {
	static		$count = 0;
	
	private		$_allowed_positions = array("normal", "advanced", "side");
	private		$_allowed_priorities = array( 'high', 'core', 'default', 'low' );
	
	protected 	$plugin_id;
	protected 	$plugin_title;
	protected 	$plugin_nonce;
	
	protected 	$box_title;
	protected	$position;
	protected	$pages;
	protected	$priority;
	protected	$settings_api;
	
	/**
	 *	Pretty much automated meta box creation. You only need to worry about the form fields and the 
	 *	on-save validation
	 *
	 *
	 *	@param	array	$options	Array of available options. The options are:
	 *									title: The title of the meta box (default: plugin title)
	 *									position: Position. Defaults to 'normal' (normal|side|advanced)
	 *									page: Which pages to show it on (array) (defaults to both posts and pages)
	 *									priority: The priority of the boxes. Defaults to 'default' (high|core|default|ow)
	 *
	 *	@return void
	 */
	public function __construct( $options = false ) {

		$this->plugin_id 		= self::PLUGINID;			
		$this->plugin_title 	= self::TITLE;
		$this->plugin_nonce		= $this->plugin_id . '_nonce';
		
		$this->box_title	= ( isset( $options['title'] ) ) ? $options['title'] : $this->plugin_title;
		$this->position		= ( isset( $options['position'] ) ) ? $options['position'] : 'normal';
		$this->pages		= ( isset( $options['page'] ) ) ? $options['page'] : array( 'post', 'page' );
		$this->priority		= ( isset( $options['priority'] ) ) ? $options['priority'] : 'default';
		$this->settings_api = ( isset( $options['settings_api'] ) ) ? $options['settings_api'] : false;
		$this->callback		= ( isset( $options['callback'] ) && is_array( $options['callback'] ) ) ? $options['callback']  : array( &$this, 'load_post_meta' ) ;
		
		if (!in_array($this->position, $this->_allowed_positions)) {
			$this->position = 'normal';
		}
		if (!in_array($this->priority, $this->_allowed_priorities)) {
			$this->priority = 'default';
		}
		
		if ( !$this->settings_api ) {

			add_action( 'save_post', array( &$this, 'pre_save' ) );
			add_action( 'add_meta_boxes', array(&$this, 'load_meta_boxes') );			
		} else {

			add_action('load-' . $this->pages, array( &$this, 'load_meta_boxes' ) );
		}


	}
	
	private function add_boxes( $id, $page ) {

		add_meta_box( 
			$id, 
			$this->box_title, 
			$this->callback, 	
			$page, 
			$this->position, 
			$this->priority
		);
	}
	
	public function load_meta_boxes() {
		if ( $this->settings_api ) {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
		}
		// What to do about id for non-extended instances
		$id = $this->plugin_id . '-' . $this->self . '-' . self::$count;
		self::$count++;
		if ( is_array( $this->pages ) ) {
			foreach( $this->pages as $page ) {
				$id .= '-' . $page;
				$this->add_boxes( $id, $page );
			}
		} else {
			$this->add_boxes( $id, $this->pages );
		}
		
	}
	
	public function pre_save( $post_id ) {

		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return false;
		
		if ( !isset( $_POST[$this->plugin_nonce  . '_' . $post_id] ) ) {
			return false;
		}
		
		
		if ( !wp_verify_nonce( $_POST[$this->plugin_nonce . '_' . $post_id], $this->plugin_id ) ) {
			echo "Failing";
			return false;
		}
		  // Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) ) 
			return false;
		return $this->save_post( $post_id );
		
	}
	
	public function save_post( $post_id ) {
	
	}
	public function load_post_meta( $post ) {
	
	}
}


?>