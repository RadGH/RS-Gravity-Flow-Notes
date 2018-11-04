<?php

if ( !defined( 'ABSPATH' ) ) exit;

// Admin js
function rs_gfn_admin_enqueue() {
	// $version = GFN_VERSION;
	$version = max( filemtime( GFN_PATH . '/assets/rs-gfn-admin.css' ), filemtime( GFN_PATH . '/assets/rs-gfn-admin.js' ) );
	
	wp_enqueue_style( 'rs-gfn-admin', GFN_URL . '/assets/rs-gfn-admin.css', array(), $version );
	wp_enqueue_script( 'rs-gfn-admin', GFN_URL . '/assets/rs-gfn-admin.js', array('jquery'), $version );
}
add_action( 'admin_enqueue_scripts', 'rs_gfn_admin_enqueue' );