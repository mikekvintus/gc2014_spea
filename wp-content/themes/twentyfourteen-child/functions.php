<?php 
function set_need_help_cookie() {
	if (is_page('need-help')) {
		setcookie("seencontact", "yes", time() + 30, "/"); 
	}
}
add_action( 'init', 'set_need_help_cookie');