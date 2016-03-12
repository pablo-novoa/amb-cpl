<?php 
namespace AmebaCPL;

class ambCPL{

	private static $instance = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self;

		return self::$instance;
	}

	private function __construct() {
		
	}

	public static function activate() {
		if(!is_dir(AMB_LAYOUT_DIR)){
			mkdir(AMB_LAYOUT_DIR);
		}
	}

	public static function deactivate() {}


	public static function uninstall() {
		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
			return;
	}


}


?>