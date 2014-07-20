  <?php
/*
Plugin Name: Live Act Offline Cache
Version: 0.1
Description: View the mobile version of Live Act offline
Author: Andy Zolyak
Author Email: ajz13@case.edu
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

    //get all the posts and cache them. I like this
    //modify to use with pages?
	// $myposts = get_posts( $args );
	// foreach( $myposts as $post ) :	setup_postdata($post);
	// 	array_push($cache,"\n" . post_permalink($post->ID) );
	// endforeach;




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
add_action('wp_handle_upload', 'la_update_manifest_file');
add_action( 'switch_theme', 'la_update_manifest_file' );
register_activation_hook( __FILE__, 'la_update_manifest_file' );

//remove the manifest file(filename)
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