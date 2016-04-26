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
            'Ameba - Custom Post List (alpha)',
            array(
                'classname'     => 'ambCPL_widget',
                'description'   => '(Under development) Displays a custom list of post'
            )
        );

        $this->getLayoutsFolders();
	}

	function form( $instance ) {
		$instance = wp_parse_args(
            (array)$instance,
            array(
                'title'      => '',
                'layout' => '',
                'postType' => 'post',
                /*'postCat' => '',*/
                'offset' => '0',
                'limit' => '-1'
            )
        );

        $allPTArray = $this->getPostTypes();

        ?>
        <p>
            <label>Title: </label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ) ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ) ?>" value="<?php echo  esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label>Select Post Type: </label>
            <select id="<?php echo $this->get_field_id( 'postType' ) ?>" name="<?php echo $this->get_field_name( 'postType' ) ?>" class="widefat fpl_admin_pt_select">
            <?php foreach ($allPTArray as $selPTData): ?>
                <option value="<?php echo $selPTData['postTypeSlug']; ?>" <?php selected( $selPTData['postTypeSlug'], $instance['postType'], true ); ?>><?php echo $selPTData['postTypeName']; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label>Limit Query: </label>
            <input type="number" id="<?php echo $this->get_field_id( 'limit' ) ?>" class="widefat" name="<?php echo $this->get_field_name( 'limit' ) ?>" <?php if(!empty($instance['limit']) ){ echo 'checked'; } ?> value="<?php echo  esc_attr( $instance['limit'] ); ?>" />
        </p>
        <p class="fpl_layout_all">
            <label>Query offset: </label>
            <input type="number" id="<?php echo $this->get_field_id( 'offset' ) ?>" class="widefat" name="<?php echo $this->get_field_name( 'offset' ) ?>" <?php if(!empty($instance['offset']) ){ echo 'checked'; } ?> value="<?php echo  esc_attr( $instance['offset'] ); ?>" />
        </p>
        <p>
            <label>Layout: </label>
            <select id="<?php echo $this->get_field_id( 'layout' ) ?>" name="<?php echo $this->get_field_name( 'layout' ) ?>" class="widefat fpl_layout_select">
            	<option value="" <?php  echo selected( $layoutOption, $instance['layout'], true ); ?> >Default</option>
         	<?php 
	         	foreach ($this->layouts as $layoutOption){
	         		echo '<option value="'.$layoutOption.'" '.selected( $layoutOption, $instance['layout'], true ).'>'.$layoutOption.'</option>';
	         	}
         	?>
            </select>
        </p>
        <?php
	}

	function update( $new_instance, $old_instance ) {
		$old_instance['title'] = strip_tags( stripcslashes($new_instance['title']) );
        $old_instance['postType'] = strip_tags( stripcslashes($new_instance['postType']) );
        $old_instance['limit'] = strip_tags( stripcslashes($new_instance['limit']) );
        $old_instance['offset'] = strip_tags( stripcslashes($new_instance['offset']) );
        $old_instance['layout'] = strip_tags( stripcslashes($new_instance['layout']) );

        return $old_instance;
	}

	function getLayoutsFolders(){
		if(is_dir(AMB_LAYOUT_DIR)){
			foreach (new DirectoryIterator(AMB_LAYOUT_DIR) as $dirInfo) {
			    if($dirInfo->isDir() && !$dirInfo->isDot()) {
			        $thisDirName = $dirInfo->getFilename();
			        array_push($this->layouts, $thisDirName);
			    }
			}
		}
	}

	function widget( $args, $instance ) {
		//open widget
			echo $args['before_widget'];
		//store layout name (from form method)
			$layoutFolder = $instance['layout'];

		//gets js (optional)
			$jsFilePath = $this->getFilesPaths('js', $layoutFolder);
			if( file_exists($jsFilePath) ){
				$jsEnqueueURL = $this->sanitizePathToURL($jsFilePath);
				wp_register_script( 'ambCPL-layout-js-1', $jsEnqueueURL, array('jquery') );
				wp_enqueue_script( 'ambCPL-layout-js-1');
			} 
		//gets css (optional)
			$cssFilePath = $this->getFilesPaths('css', $layoutFolder);
			if( file_exists($cssFilePath) ){
				$cssEnqueueURL = $this->sanitizePathToURL($cssFilePath);
				wp_register_style( 'ambCPL-layout-1', $cssEnqueueURL);
				wp_enqueue_style( 'ambCPL-layout-1');
			} 


		//before loop content
			$beforeFilePath = $this->getFilesPaths('php', $layoutFolder, 'before');
			if( file_exists($beforeFilePath) ){
				require_once($beforeFilePath); 
			}else{ echo "<ul>"; }
		// The loop
			global $post;
			$args = array( 
				'numberposts' 	=> $instance['limit'],
				'offset' 		=> $instance['offset'],
				'post_type' 	=>  $instance['postType']
			);
			$ambCPL_posts = get_posts( $args );
			foreach( $ambCPL_posts as $post ) :  setup_postdata($post);

			//gets Layout
				$layoutFilePath = $this->getFilesPaths('php', $layoutFolder);
				if( file_exists($layoutFilePath) ){
					include($layoutFilePath); 
				}else{
					echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
				}

			endforeach; wp_reset_postdata();
		//after loop content
			$afterFilePath = $this->getFilesPaths('php', $layoutFolder, 'after');
			if( file_exists($afterFilePath) ){
				require_once($afterFilePath); 
			}else{ echo "</ul>"; }

		//close widget
			echo $args['after_widget'];
	}

	function getFilesPaths($type, $folderName, $location = "loop"){
		$filePath = "";
		if(is_dir(AMB_LAYOUT_DIR.$folderName)){
			foreach (new DirectoryIterator(AMB_LAYOUT_DIR.$folderName) as $filesInfo) {
			    if( $filesInfo->isFile() && $filesInfo->isReadable() && $filesInfo->getExtension() == $type ) {
			        $thisFilePath = $filesInfo->getPathname();
			        $thisFileName = $filesInfo->getFilename();

			        if( $filesInfo->getExtension() != 'php' ){
			        	$filePath = $thisFilePath;
			        }else if(strpos($thisFileName, $location.'_') === 0){
			        	$filePath = $thisFilePath;
			        }
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

	function getPostTypes(){
		$PTArray = array();
	    $allPostTypes = get_post_types();
	    foreach ($allPostTypes as $select_post_type):
	        $thisPTname = get_post_type_object($select_post_type)->label;
	        $ptTaxArray = array();

	        $excludePostTypes = array('attachment','revision','nav_menu_item');
	        if(!in_array($select_post_type, $excludePostTypes)){
	            $thisPTArray = array(
	                'postTypeSlug' => $select_post_type,
	                'postTypeName' => $thisPTname
	            );
	            array_push($PTArray, $thisPTArray);
	        }
	    endforeach;
	    return $PTArray;
	}

}

?>