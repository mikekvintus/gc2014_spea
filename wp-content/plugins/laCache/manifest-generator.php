  <?php
/*
Plugin Name: Live Act Offline Cache
Version: 0.5
Description: View the mobile version of Live Act offline. Deactivate and reactivate this plugin when making significant administration changes
Author: Andy Zolyak
Author Email: ajz13@case.edu
Author URI: http://designedbyz.com
*/

defined('ABSPATH') or die("No script kiddies please!");

// check WordPress version
global $wp_version;
if( version_compare( $wp_version, "3.1", "<" ) ) {
	exit(
		'This plugin requires php5 and WordPress 3.1 or newer. 
		<a href="http://codex.wordpress.org/Upgrading_WordPress" target="_blank">Please update!</a>'
	);
}

//add application cache to head elements

// manage <html> element
function la_add_cache_manifest_html( $output ) {
	if ( ! is_admin() ) {
        return $output . ' manifest="'. get_stylesheet_directory_uri() .'/offline.appcache"';
    }
}
//add the filter
add_filter( 'language_attributes', 'la_add_cache_manifest_html' );


//run this to update the manifest
//Since the argunment is never needed here, and I just the hook, I'm abusing dynamic typing.
//handle upl,oads passes an array, and the save/delete post pass a string...
function la_update_manifest_file( $post_id ) {
	//regenerate manifest here
	$network = array("\n\nNETWORK:");
	$cache = array("\n\nCACHE:");
	$stylesheet_path = get_stylesheet_directory()."/";
	$template_path = get_template_directory()."/";
	$stylesheet_dir = new RecursiveDirectoryIterator( $stylesheet_path );
    $template_dir = new RecursiveDirectoryIterator( $template_path );

    //cache the current theme
    foreach(new RecursiveIteratorIterator($stylesheet_dir) as $file) {
		if ($file->IsFile() && isAllowedExtension($file)) {
				array_push($cache,"\n" . str_replace($stylesheet_path, './', $file));
		}
	}
    
    //if the current theme is a child theme, cache the parent theme too
    if(0 != strcmp ( $stylesheet_path , $template_path )) {
        foreach(new RecursiveIteratorIterator($template_dir) as $file) {
			if ($file->IsFile() && isAllowedExtension($file)) {
				array_push($cache,"\n" . str_replace($template_path, get_template_directory_uri().'/', $file));
			}
		}
    }



    $fh = fopen( get_stylesheet_directory().'/offline.appcache', 'w' );
	fwrite($fh,'CACHE MANIFEST');
	//drop a utc timestamp here to make sure the cache always changes :)
    date_default_timezone_set("UTC");	
	fwrite($fh, "\n\n# " . date("Y-m-d H:i:s", time())); 

    //whitelist everything that isn't in the cache
	array_push($network,"\n*");

    //cache the 20 most recent pages and 20 most recent posts by default
	$postArgs = array(
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => 20);
	$myposts = get_posts( $postArgs );
	foreach( $myposts as $post ) :	setup_postdata($post);
		$postURL = post_permalink($post->ID);
		array_push($cache,"\n" . $postURL );
		if(0 == strcmp(substr($postURL, -1) , "/")) {
			array_push($cache, "\n" . substr($postURL, 0, -1));
		}

	endforeach;


	$pageArgs = array(
			'sort_order' => 'DESC',
			'sort_column' => 'post_modified',
			'number' => 20);
	$mypages = get_pages($pageArgs);
	foreach( $mypages as $page) {
		$pageURL = get_page_link($page->ID);
		// error_log($pageURL);
		// error_log(substr($pageURL, -1));
		array_push($cache, "\n" . $pageURL);
		if(0 == strcmp(substr($pageURL, -1) , "/")) {
			// error_log("true");
			array_push($cache, "\n" . substr($pageURL, 0, -1));
		}
	}




	foreach($cache as $file){ fwrite($fh,$file); }
	foreach($network as $file){ fwrite($fh,$file);}
	fclose($fh);
}

function la_update_manifest_file_noarg( ) {
	la_update_manifest_file('dontcare');
}

//update manifest hooks
add_action( 'save_post', 'la_update_manifest_file' );
add_action( 'deleted_post', 'la_update_manifest_file');
add_action( 'wp_handle_upload', 'la_update_manifest_file');
add_action( 'switch_theme', 'la_update_manifest_file' );
//should also fire after added and deleted
register_activation_hook( __FILE__, 'la_update_manifest_file' );


//add javascript to handle updating the cache in real time.
function la_register_javascript_cache_handlers() {
	wp_enqueue_script( 'la_appcache_manager_script', plugins_url( 'manifest-loader.js' , __FILE__ ), array( 'jquery' ));
}
//register the hook
add_action( 'wp_enqueue_scripts', 'la_register_javascript_cache_handlers' );



//remove the manifest file. Typically done when the plugin is deactiviated
function la_remove_manifest_file(){
	$files = array( get_stylesheet_directory().'/offline.appcache' );
	foreach( $files as $file ) {
		if( file_exists( $file ) )
		unlink( $file );
	}
}

//remove manifest when the plugin is deactiviated
register_deactivation_hook( __FILE__, 'la_remove_manifest_file' );



//helper functions
function isAllowedExtension($file) {
	$allowedExtensions = array("htm", "html", "gif", "png", "jpg", "js", "css");
	$strings = explode(".", $file);
	return in_array(end($strings), $allowedExtensions);
}
?>
