<?php
/*
	Plugin Name: Plugin boilerplate
	Plugin URI: 
	Description: Bolierplate for starting a new plugin
	Author: manish
	Version: 1.0.0
	Author URI: 
*/
session_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$bpPluginUrl = plugin_dir_url(__FILE__);
$bpPluginPath = plugin_dir_path(__FILE__);
if(substr($bpPluginUrl, -1) == "/" || substr($bpPluginUrl, -1) == "\\" ){
	$bpPluginUrl  = substr($bpPluginUrl, 0, strlen($bpPluginUrl)-1 );
}
if(substr($bpPluginPath, -1) == "/" || substr($bpPluginPath, -1) == "\\" ){
	$bpPluginPath  = substr($bpPluginPath, 0, strlen($bpPluginPath)-1 );
}

define("BP_BASE_URL", $bpPluginUrl);
define("BP_BASE_PATH", $bpPluginPath);
define("BP_ADMIN_URL", get_admin_url().'admin.php?page=lmapps');
define("BP_PLUGIN_VERSION", "1.0.0");

define("BP_TIMEOUT_SECONDS", 30);
define("BP_FILE", __FILE__);

include_once('library/core.php');
include_once('library/hooks.php');
include_once('library/shortcode.php');
include_once('library/plugin.php');
$BP_plugin = new BP_plugin;
