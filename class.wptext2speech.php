<?php
	
class wpText2speech{
	
	public function __construct(){
		
		$this->menuPageTitle = "wpText2speech Options";
		$this->menuPageLabel = "wpT2S Options";
		
		//add menu page
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );

	}
	
	public function admin_menu_page(){
		add_menu_page( $this->menuPageTitle, $this->menuPageLabel, 'manage_options', get_class($this), array($this,'wpText2speech_options_page'), 'dashicons-megaphone', 100 );
	}
		
	public function wpText2speech_options_page(){
		?>
		<div class="wrap">
			<h1><?php echo $this->menuPageTitle; ?></h1>
		</div>
		<?php
	}

}