<?php

/**
 * Get all the function linked with a hook
 * @param $name string the name of the hook
 * @author Justal "Latsuj" Kevin
 **/
function kv_debug_hook($name) {
	global $wp_filter;

	echo '<pre>';
	var_dump( $wp_filter[$name] );
	echo '</pre>';
}
