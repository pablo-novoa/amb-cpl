<?php 
/**
* Plugin Name: Ameba - Custom Post List
* Description: Description
* Plugin URI: http://#
* Author: Author
* Author URI: http://#
* Version: 1.0
* License: GPL3
* Text Domain: Text Domain
* Domain Path: Domain Path
*/

if ( ! defined( 'ABSPATH' ) ) die( '<h1>Error: Access Denied</h1><h4>AH, AH, Ah, you should not be here
</h4>' );

/**
 * Constants
 */

//plugin directions
define( 'AMB_CPL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AMB_CPL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//layout directions
define( 'AMB_LAYOUT_DIR', get_stylesheet_directory().'/amb-cpl-layouts/' );
define( 'AMB_LAYOUT_URL', get_stylesheet_directory_uri().'/amb-cpl-layouts/' );

/**
 *	Classes Autoloader
 */
spl_autoload_register(function($class){
	$segments = array_filter( explode("\\", $class) );
	if( array_shift($segments) === "AmebaCPL" ){
		$path = __DIR__ . "/" . implode("/", $segments) . ".php";
		if(file_exists($path)){
			include $path;
		}
	}
});

/**
 * Plugin Hooks
 */
add_action( 'plugins_loaded', array( 'AmebaCPL\ambCPL', 'get_instance' ) );
register_activation_hook( __FILE__, array( 'AmebaCPL\ambCPL', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'AmebaCPL\ambCPL', 'deactivate' ) );
//register_uninstall_hook( __FILE__, array( 'AmebaCPL\ambCPL', 'uninstall' ) );

?>