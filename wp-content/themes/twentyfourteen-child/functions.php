<?php 
add_action( 'get_header', 'set_need_help_cookie');
function set_need_help_cookie() {
	if (is_page('need-help')) {
		setcookie("seencontact", "yes", time() + 2629740, "/");
	}
}