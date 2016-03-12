<?php  
namespace AmebaCPL;
use \WP_Widget;
use \WP_Query;
use \DirectoryIterator;

class widget_ambCPL extends WP_Widget {
	private $layouts = array();
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
            'amb-cpl-widget',
            'Ameba - Custom Post List',
            array(
                'classname'     => 'ambCPL_widget',
                'description'   => 'Displays a custom list of post'
            )
        );

        $this->getLayoutsFolders();
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
	}

	function getLayoutsFolders(){
		foreach (new DirectoryIterator(AMB_LAYOUT_DIR) as $dirInfo) {
		    if($dirInfo->isDir() && !$dirInfo->isDot()) {
		        $thisDirName = $dirInfo->getFilename();
		        array_push($this->layouts, $thisDirName);
		    }
		}
	}

	function widget( $args, $instance ) {
		//gets js
		$jsFilePath = $this->getFilesPaths('js');
		if( file_exists($jsFilePath) ){
			$jsEnqueueURL = $this->sanitizePathToURL($jsFilePath);
			wp_register_script( 'ambCPL-layout-js-1', $jsEnqueueURL, array('jquery') );
			wp_enqueue_script( 'ambCPL-layout-js-1');
		} 
		//gets css
		$cssFilePath = $this->getFilesPaths('css');
		if( file_exists($cssFilePath) ){
			$cssEnqueueURL = $this->sanitizePathToURL($cssFilePath);
			wp_register_style( 'ambCPL-layout-1', $cssEnqueueURL);
			wp_enqueue_style( 'ambCPL-layout-1');
		} 
		//gets Layout
		$layoutFilePath = $this->getFilesPaths('php');
		if( file_exists($layoutFilePath) ){
			require_once($layoutFilePath); } 
	}

	function getFilesPaths($type){
		$filePath = "";

		foreach (new DirectoryIterator(AMB_LAYOUT_DIR.$this->layouts[0]) as $filesInfo) {
		    if( $filesInfo->isFile() && $filesInfo->isReadable() && $filesInfo->getExtension() == $type ) {
		        $thisFilePath = $filesInfo->getPathname();
		        $thisFileName = $filesInfo->getFilename();

		        if( $filesInfo->getExtension() != 'php' ){
		        	$filePath = $thisFilePath;
		        }else if(strpos($thisFileName,'layout_') === 0){
		        	$filePath = $thisFilePath;
		        }
		    }
		}

		return $filePath;
	}

	function sanitizePathToURL($path){
		$pathURL = str_replace("\\", "/", $path);
		$pathURL = str_replace(get_stylesheet_directory(), "", $pathURL);
		$pathURL = get_stylesheet_directory_uri().$pathURL;

		return $pathURL;
	}

}

?>