<?php

class LIT_Main_Menu extends LIT_LI_Settings_Base {
	public $self = __CLASS__;
	
	public function get_defaults() {
		$this->add_section('Line In Typography', 'get_intro');
	}
	
	public function get_intro() {
		echo "Just a test";
	}	
	
	public function load_settings() {
	
	}
	
	public function validate( $data ) {
		return $data;
	}	
}


class LIT_Settings extends LIT_LI_Settings_Base  {
	public $self = __CLASS__;
	
	public function get_defaults() {
		$defaults = array(
			'line-height' 		=> 21,
			'font-size'			=> 14,
			'elements'			=> '.container',
			'paragraph-text'	=> 'Paragraph to test line height',
			'show-paragraph'	=> 'true',
			'background-offset'	=> 0,
			'plugin-version'	=> $this->lang,
			'upload_column_1'	=> false,
			'upload_column_2'	=> false
			
		
		);
		return $defaults;
	}
	
	public function load_settings() {
		
		
		$this->add_section('Line In Typography', 'get_intro');
		$this->add_setting( 'line_height', 'Set Line Height' );
		$this->add_setting( 'elements', 'DOM Elements' );
		$this->add_setting( 'test_paragraph', 'Test Paragraph Text' );
		$this->add_setting( 'show_paragraph', 'Show Paragraph' );
		$this->add_setting( 'background_offset', 'Background Offset' );
		$this->add_section( 'Custom Columns', 'columns_intro');
		$this->add_setting( 'upload_column_1', 'Upload Column Image 1');
		$this->add_setting( 'upload_column_2', 'Upload Column Image 2');
	}
	
	/* Intro callbacks */


	public function get_intro() {
		
		?>
		 <p><?php _e( 'Manage options for the Line In Typography plugin. Refer to contextual help for more information.', $this->lang ); ?></p>
		<?php
		
	}

	public function line_height() {
		?>
		<input type='text' name='<?php echo $this->get_name('line-height'); ?>' value='<?php echo intval($this->get_option('line-height') ); ?>' id='<?php echo $this->get_id('line-height'); ?>' size='5' /> <span class='description'>Explicitly set your line height here, or set to 0 to have the plugin try and figure it out from your theme's body styles</span>
		
		<?php
	}
	function li_typography_font_size() {
		?>
		<input type='text' name='<?php echo $this->get_name('font-size'); ?>' value='<?php echo intval($this->get_option('line-height') ); ?>' id='<?php echo $this->get_id('font-size'); ?>' size='5' /> <span class='description'>Explicitly set your font size here, or set to 0 to have the plugin try and figure it out from your theme's body styles</span>
		<?php
	}
	function elements() {
		?>
		<input type='text' name='<?php echo $this->get_name('elements'); ?>' value='<?php echo esc_attr($this->get_option('elements') ); ?>' id='<?php echo $this->get_id('elements'); ?>' /> <span class='description'>Comma separated list of DOM elements to attach the grids to</span>
		<?php
	}
	function test_paragraph() {
		?>
		<input type='text' name='<?php echo $this->get_name('paragraph-text'); ?>' value='<?php echo esc_attr($this->get_option('paragraph-text')); ?>' id='<?php echo $this->get_id('paragraph-text'); ?>' size='50' /> <span class='description'>A test paragraph to show above all content. Indicates the correct positioning on the line that all paragraphs should adhere to.</span>
		<?php
	}
					
	
	function show_paragraph() {
		?>
		<input type="radio" name="<?php echo $this->get_name('show-paragraph'); ?>" value="true"<?php checked( 'true' == $this->get_option('show-paragraph') ); ?> id='<?php echo $this->get_id('show-paragraph-true'); ?>' /><label for='<?php echo $this->get_id('show-paragraph-true'); ?>'>Yes</label>
		<input type="radio" name="<?php echo $this->get_name('show-paragraph'); ?>" value="false"<?php checked( 'false' == $this->get_option('show-paragraph') ); ?> id='<?php echo $this->get_id('show-paragraph-false'); ?>' /><label for='<?php echo $this->get_id('show-paragraph-false'); ?>'>No</label>
		
		<?php
	}
	function background_offset() {
		?>
		<input type='text' name='<?php echo $this->get_name('background-offset'); ?>' value='<?php echo intval( $this->get_option('background-offset') ); ?>' id='<?php echo $this->get_id('background-offset'); ?>' size='5' /> <span class='description'>Shift the lines up or down to get your test paragraph sitting exactly on them.</span>
		
		<?php
	}


	function columns_intro() {
		?>
		<p class='description'>Use these fields if you want to upload your own custom columns instead of using the standard 12 or 16 column images. Images can be any size, but they will be stretched along the x-axis so it's usually a good idea to make 'em pretty wide</p>		
		<?php
	}
	
	function upload_column_1() {
	?>
		<input id="<?php echo $this->get_id('upload_column_1'); ?>" type="text" size="36" name="<?php echo $this->get_name('upload_column_1'); ?>" value="<?php echo $this->get_option('upload_column_1'); ?>" />
				
	<?php	$this->uploader_button( 'upload_column_1' ); ?>
		<p class='description'>To reset it to the default, simply delete this entry.</p>
	<?php
	}
	
	function upload_column_2() {
	?>
		<input id="<?php echo $this->get_id('upload_column_2'); ?>" type="text" size="36" name="<?php echo $this->get_name('upload_column_2'); ?>" value="<?php echo $this->get_option('upload_column_2'); ?>" />
				
	<?php	$this->uploader_button( 'upload_column_2' ); ?>
		<p class='description'>To reset it to the default, simply delete this entry.</p>
	<?php
	}
	

	function validate( $input ) {
		$valid_input = $this->get_option();
		$default_options = $this->get_defaults();
		
		if ( isset( $_POST['reset'] ) ) {
			$input = $default_options;
			add_settings_error('li_typography_settings_section', 'reset-message', "Options reset to default settings", 'updated');
		} else if ( isset( $_POST['submit'] ) ) {
			$error = false;
			if ( (int) $input['line-height'] < 0 ) {
				$input['line-height'] = $valid_input['line-height'];
				$error[] = "Line height not a valid positive integer, not updated";
			}
//				if ( (int) $input['font-size'] < 0 ) {
//					$input['font-size'] = $valid_input['font-size'];
//					$error[] = "Font size not a valid positive integer, not updated";
//				}
			$input['elements'] = wp_kses($input['elements'], array());
			if ( '' == $input['elements'] ) {
				$input['elements'] = $valid_input['elements'];
				$error[] = "You have not set a valid element to attach the grids to. Setting not updated.";	
				
			}
			
			
			$input['paragraph-text'] = wp_kses($input['paragraph-text'], array());
			$input['show-paragraph'] = ( 'true' == $input['show-paragraph'] ) ? 'true' : 'false';
			if ( 'true' == $input['show-paragraph'] && '' == $input['paragraph-text'] ) {
				$input['paragraph-text'] = $default_options['paragraph-text'];
				$error[] = "If you want to show the paragraph, you'll need some paragraph text. Set to default";
			}
			
			$input['background-offset'] = (int) $input['background-offset'];
			
			if ( '' == wp_kses( $input['upload_column_1'], array() ) ) {
				$input['upload_column_1'] = false;
			}
			if ( '' == wp_kses( $input['upload_column_2'], array() ) ) {
				$input['upload_column_2'] = false;
			}
						
			
			if ( false !== $error ) {
				$i = 0;
				$output = false;
				foreach ( $error as $err ) {
					
					if ( $i > 0 ) {
						$output .= "<br />";
					}
					$output .= $err;
					$i++;
				
				}
				add_settings_error('li_typography_settings_section', 'error-message', $output, 'error');
				add_settings_error('li_typography_settings_section', 'updated-message', "All other settings updated", 'updated');
			}
		}
		return $input;
	}
}



?>