<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class bp_hooks extends bp_core{
	public function __construct(){
		parent::__construct();
		$this->executeHooks();
	}
	public function executeHooks(){

	}
}