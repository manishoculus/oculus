<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class bp_shortcode extends bp_hooks{
	
	function __construct(){
		parent::__construct();
		add_action( 'init', array($this, 'addShortCodes') );
	}

	public function addShortCodes(){
        add_shortcode('map',array($this ,'get_map'));
	}
    public function get_map()
    {
        ob_start();
        include_once(BP_BASE_PATH.'/templates/frontend/map.php');
        $content=ob_get_clean();
        return $content;
    }
}