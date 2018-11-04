<?php

if ( !defined( 'ABSPATH' ) ) exit;

// Admin js
function rs_gfn_admin_enqueue() {
	if ( !function_exists('get_current_screen') ) return;
	
	$screen = get_current_screen();
	if ( !$screen || $screen->id != 'toplevel_page_gravityflow-inbox' ) return;
	
	$version = GFN_VERSION;
	
	wp_enqueue_style( 'rs-gfn-admin', GFN_URL . '/assets/rs-gfn-admin.css', array(), $version );
	wp_enqueue_script( 'rs-gfn-admin', GFN_URL . '/assets/rs-gfn-admin.js', array('jquery'), $version );
}
add_action( 'admin_enqueue_scripts', 'rs_gfn_admin_enqueue' );