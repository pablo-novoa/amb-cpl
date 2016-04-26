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
		add_action( 'widgets_init', array($this, 'register_plugin_widget') );
	}

	public static function activate() {
		if(!is_dir(AMB_LAYOUT_DIR)){
			mkdir(AMB_LAYOUT_DIR, 0777, true);
			chmod(AMB_LAYOUT_DIR, 0777);
		}
	}

	public static function deactivate() {}


	public static function uninstall() {
		if ( __FILE__ != WP_UNINSTALL_PLUGIN )
			return;
	}


	public static function register_plugin_widget() {
		register_widget( 'AmebaCPL\widget_ambCPL' );
	}

}


?>