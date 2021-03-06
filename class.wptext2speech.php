<?php
	
class wpText2speech{
	
	public function __construct(){
		
		$this->menuPageTitle 	= "wpText2speech Options";
		$this->menuPageLabel 	= "wpT2S Options";
		$this->folderName	 	= "wpT2S";
		$this->cssPathFile   	= dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "css";
		$this->cssPathFileName  = "wpT2S_dynamic.css";

		//add menu page
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
		add_action( 'admin_init', array( $this, 'wpText2speech_settings' ) );

		//enqueue script
		add_action( 'wp_enqueue_scripts', array( $this, 'wpText2speech_script'), 0 );

		//ajax capture
		add_action( 'wp_ajax_wpT2S', array( $this, 'ajax_wpT2S' ) );
		add_action( 'wp_ajax_nopriv_wpT2S', array( $this,  'ajax_wpT2S' ) );

		//admin script
		add_action('admin_enqueue_scripts', array( $this, 'wpText2speech_options_enqueue_scripts' ) );

		//hook for save icon
		add_action('update_option', array( $this, 'wpText2speech_save_options' ), 10, 3 );
	}

	public function wpText2speech_save_options( $option, $old_value, $value ){
		if( $option == "wpT2S_options" ){

			//generate css
			$css = "";
			$css .= ".wpT2S_Icon_Base{background: url(" . $value["wpT2S_Icon_Base"] . ") transparent no-repeat;}";
			$css .= ".wpT2S_Icon_Loading{background: url(" . $value["wpT2S_Icon_Loading"] . ") transparent no-repeat;}";
			$css .= ".wpT2S_Icon_Play{background: url(" . $value["wpT2S_Icon_Play"] . ") transparent no-repeat;}";
			$css .= ".wpT2S_Icon_Pause{background: url(" . $value["wpT2S_Icon_Pause"] . ") transparent no-repeat;}";

			//general css
			$css .= ".titleT2S{ position:relative; }";
			$css .= ".titleT2S .playerT2S{ width:50px; height:50px; padding-right:60px; cursor: pointer; position: absolute; right: 0; top: 0; display: block;}"; //calcule dynamic size of icon


			//test if dir exist
			if( !is_dir( $this->cssPathFile ) ){
				try{
					mkdir( $this->cssPathFile, 0777);
				}catch(Exception $e){
					//error to create dir
					echo $e;
				}
			}

			//test if file exist
			if( !file_exists( $this->cssPathFile . DIRECTORY_SEPARATOR . $this->cssPathFileName ) ){
				try{
					$file = fopen( $this->cssPathFile . DIRECTORY_SEPARATOR . $this->cssPathFileName, "w");
				}catch(Exception $e){
					//error open file
					echo $e;
				}
			}else{
				try{
					$file = fopen( $this->cssPathFile . DIRECTORY_SEPARATOR . $this->cssPathFileName, "r+");
				}catch(Exception $e){
					//error open file
					echo $e;
				}
			}

			//erase file
			ftruncate($file, 0);
			rewind($file);

			//write file
			fputs($file, $css);
			fclose($file);
		}
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

		if( isset( $input['wpT2S_Icon_Base'] ) )
			$new_input['wpT2S_Icon_Base'] = esc_url_raw( $input['wpT2S_Icon_Base'] );
		
		if( isset( $input['wpT2S_Icon_Loading'] ) )
			$new_input['wpT2S_Icon_Loading'] = esc_url_raw( $input['wpT2S_Icon_Loading'] );

		if( isset( $input['wpT2S_Icon_Play'] ) )
			$new_input['wpT2S_Icon_Play'] = esc_url_raw( $input['wpT2S_Icon_Play'] );

		if( isset( $input['wpT2S_Icon_Pause'] ) )
			$new_input['wpT2S_Icon_Pause'] = esc_url_raw( $input['wpT2S_Icon_Pause'] );

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
		
        add_settings_field(
            'wpT2S_Icon_Base', 
            'Icon base', 
            array( $this, 'field_icon_base' ), 
            get_class($this),
            'wpT2S_section'
		);
		
        add_settings_field(
            'wpT2S_Icon_Loading', 
            'Icon loading', 
            array( $this, 'field_icon_loading' ), 
            get_class($this),
            'wpT2S_section'
		);
		
        add_settings_field(
            'wpT2S_Icon_Play', 
            'Icon play', 
            array( $this, 'field_icon_play' ), 
            get_class($this),
            'wpT2S_section'
		);

        add_settings_field(
            'wpT2S_Icon_Pause', 
            'Icon pause', 
            array( $this, 'field_icon_pause' ), 
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
	
	public function field_icon_base(){
        printf(
			'<input type="text" id="wpT2S_Icon_Base" name="wpT2S_options[wpT2S_Icon_Base]" value="%1$s" />
			<input class="upload_icon_button" type="button" class="button" value="' . __( 'Upload Icon', 'wpT2S' ) . '" />
			<img src="%1$s" alt="" />',
			isset( $this->options['wpT2S_Icon_Base'] ) ? esc_url( $this->options['wpT2S_Icon_Base'] ) : '' 
		);
	}

	public function field_icon_loading(){
        printf(
			'<input type="text" id="wpT2S_Icon_Loading" name="wpT2S_options[wpT2S_Icon_Loading]" value="%s" />
			<input class="upload_icon_button" type="button" class="button" value="' . __( 'Upload Icon', 'wpT2S' ) . '" />
			<img src="%1$s" alt="" />',
			isset( $this->options['wpT2S_Icon_Loading'] ) ? esc_url( $this->options['wpT2S_Icon_Loading'] ) : '' 
		);
	}

	public function field_icon_play(){
        printf(
			'<input type="text" id="wpT2S_Icon_Play" name="wpT2S_options[wpT2S_Icon_Play]" value="%s" />
			<input class="upload_icon_button" type="button" class="button" value="' . __( 'Upload Icon', 'wpT2S' ) . '" />
			<img src="%1$s" alt="" />',
			isset( $this->options['wpT2S_Icon_Play'] ) ? esc_url( $this->options['wpT2S_Icon_Play'] ) : '' 
		);
	}

	public function field_icon_pause(){
        printf(
			'<input type="text" id="wpT2S_Icon_Pause" name="wpT2S_options[wpT2S_Icon_Pause]" value="%s" />
			<input class="upload_icon_button" type="button" class="button" value="' . __( 'Upload Icon', 'wpT2S' ) . '" />
			<img src="%1$s" alt="" />',
			isset( $this->options['wpT2S_Icon_Pause'] ) ? esc_url( $this->options['wpT2S_Icon_Pause'] ) : '' 
		);
	}

	public function wpText2speech_script(){
		
		//get options
		$options = get_option( 'wpT2S_options' );

		wp_register_script( 'wpT2S-js', plugin_dir_url( __FILE__ ) . '/js/wpT2S.js', array('jquery'), "0.1", true);	
		wp_localize_script( 'wpT2S-js', 'wpT2S_ajaxURL', admin_url( 'admin-ajax.php' ));
		wp_localize_script( 'wpT2S-js', 'wpT2S_content_class_selector', $options['wpT2S_Selector']);
		wp_enqueue_script( 'wpT2S-js' );

		//load dynamique css
		if( file_exists( $this->cssPathFile . DIRECTORY_SEPARATOR . $this->cssPathFileName ) ){
			wp_enqueue_style( 'wpT2S-dyn-css',  plugin_dir_url( __FILE__ ) . "css" . DIRECTORY_SEPARATOR . $this->cssPathFileName, array(), uniqid() );
		}

	}

	public function wpText2speech_options_enqueue_scripts(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
		wp_enqueue_script('media-upload');
		
		//custom admin script
		wp_register_script( 'wpT2S-admin-js', plugin_dir_url( __FILE__ ) . '/admin-js/wpT2SAdmin.js', array('jquery'), "0.1", true);	
		wp_enqueue_script( 'wpT2S-admin-js' );
	}

	public function ajax_wpT2S() {

		//get options
		$options = get_option( 'wpT2S_options' );

		if( isset($_POST["text"]) && $_POST["text"] != ""){
	
			$text 		= urlencode( $_POST["text"] );
			$fileName 	= md5($text) . ".mp3";
			$pathFile	= get_template_directory() . DIRECTORY_SEPARATOR . $this->folderName . DIRECTORY_SEPARATOR . $fileName;

			//is file exist
			if( file_exists($pathFile) ){
				//return file url
				wp_send_json( array( "url" => get_template_directory_uri() . DIRECTORY_SEPARATOR . $this->folderName . DIRECTORY_SEPARATOR . $fileName ) );
			}else{
				
				//lang
				if( isset($_POST["lang"]) ){
					
					if( $_POST["lang"] == "fr" ){
						$voice = "fr-FR_ReneeVoice";
					}elseif( $_POST["lang"] == "en" ){
						$voice = "en-US_LisaVoice";
					}elseif( $_POST["lang"] == "de" ){
						$voice = "de-DE_BirgitVoice";
					}elseif( $_POST["lang"] == "pt" ){
						$voice = "pt-BR_IsabelaVoice";
					}
				}else{
					$voice = "fr-FR_ReneeVoice";
				}
				
				//construct file
				$url = $options["wpT2S_API_Point"] . "?accept=audio%2Fmp3&voice=" . $voice . "&text=" . $text;
				
				//curl
				$ch = curl_init();	
			    curl_setopt($ch, CURLOPT_URL, $url);
			    curl_setopt($ch, CURLOPT_HEADER, 0);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
			    $output = curl_exec($ch);
			    curl_close($ch);

				//check dir adn try to create dir
				try{
					mkdir( dirname( $pathFile ), 0777);
				}catch(Exception $e){
					//error to create dir
				}

				//write file
				if( file_put_contents($pathFile, $output) ){
					wp_send_json( array( "url" => get_template_directory_uri() . DIRECTORY_SEPARATOR . $this->folderName . DIRECTORY_SEPARATOR . $fileName ) );
				}else{
					wp_send_json(__("text2speech_error_writing_file","wpT2S"));
				}
			}
			
		}else{
			wp_send_json(__("text2speech_empty_text","wpT2S"));
		}
	}
}//end class