<?php
	
class wpText2speech{
	
	public function __construct(){
		
		$this->menuPageTitle = "wpText2speech Options";
		$this->menuPageLabel = "wpT2S Options";
		
		//add menu page
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
		add_action( 'admin_init', array( $this, 'wpText2speech_settings' ) );

		//enqueue script
		add_action( 'wp_enqueue_scripts', array( $this, 'wpText2speech_script'), 0 );

		//ajax capture
		add_action( 'wp_ajax_wpT2S', array( $this, 'ajax_wpT2S' ) );
		add_action( 'wp_ajax_nopriv_wpT2S',array( $this,  'ajax_wpT2S' ) );

	}
	
	public function admin_menu_page(){
		add_menu_page( $this->menuPageTitle, $this->menuPageLabel, 'manage_options', get_class($this), array($this,'wpText2speech_options_page'), 'dashicons-megaphone', 100 );
	}
		
	public function wpText2speech_options_page(){
		?>
		<div class="wrap">
			<h1><?php echo $this->menuPageTitle; ?></h1>
			<?php
				$this->options = get_option( 'wpT2S_options' );
			?>
			<div class="wrap">
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'wpT2S_group' );
					do_settings_sections( get_class($this) );
					submit_button();
				?>
				</form>
			</div>
		</div>
		<?php
	}

    public function sanitize( $input ){
        $new_input = array();

        if( isset( $input['wpT2S_API_Point'] ) )
            $new_input['wpT2S_API_Point'] = $input['wpT2S_API_Point'];

        if( isset( $input['wpT2S_Selector'] ) )
            $new_input['wpT2S_Selector'] = sanitize_text_field( $input['wpT2S_Selector'] );

        return $new_input;
    }

	public function wpText2speech_settings(){
		register_setting(
            'wpT2S_group',
            'wpT2S_options',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'wpT2S_section',
            'Configuration :',
            '',
            get_class($this)
        );  

        add_settings_field(
            'wpT2S_API_Point',
            'API Point (endpoint of service)',
            array( $this, 'field_api_point' ),
            get_class($this),
            'wpT2S_section'     
        );      

        add_settings_field(
            'wpT2S_Selector', 
            'Content class selector', 
            array( $this, 'field_content_class_selector' ), 
            get_class($this),
            'wpT2S_section'
        );
	}

    public function field_api_point(){
        printf(
            '<input type="text" id="wpT2S_API_Point" name="wpT2S_options[wpT2S_API_Point]" value="%s" />', 
            isset( $this->options['wpT2S_API_Point'] ) ? esc_attr( $this->options['wpT2S_API_Point']) : ''
        );
    }

    public function field_content_class_selector(){
        printf(
            '<input type="text" id="wpT2S_Selector" name="wpT2S_options[wpT2S_Selector]" value="%s" />',
            isset( $this->options['wpT2S_Selector'] ) ? esc_attr( $this->options['wpT2S_Selector']) : ''
        );
    }

	public function wpText2speech_script(){
		
		//get options
		$this->options = get_option( 'wpT2S_options' );

		wp_register_script( 'wpT2S-js', plugin_dir_url( __FILE__ ) . '/js/wpT2S.js', array('jquery'), "0.1", true);	
		wp_localize_script( 'wpT2S-js', 'wpT2S_ajaxURL', admin_url( 'admin-ajax.php' ));
		wp_localize_script( 'wpT2S-js', 'wpT2S_content_class_selector', $this->options['wpT2S_Selector']);
		wp_enqueue_script( 'wpT2S-js' );

	}

	public function ajax_wpT2S() {
		
		wp_send_json(__("text2speech_empty_text","wpT2S"));

	}
}//end class