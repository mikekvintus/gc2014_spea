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
	return $output . ' manifest="'. get_stylesheet_directory_uri() .'/cache.manifest"';
}
//add the filter
add_filter( 'language_attributes', 'la_add_cache_manifest_html' );


?>