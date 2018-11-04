<?php
/*
Plugin Name: RS Gravity Flow Notes
Version:     1.0.0
Description: Adds an admin notes fields to gravity flow entries/workflows. A custom filter allows you to add multiple note fields, if desired.
Plugin URI:  http://radleysustaire.com/
Author:      Radley Sustaire
Author URI:  mailto:radleygh@gmail.com
License:     GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/

if ( !defined( 'ABSPATH' ) ) exit;

define( 'GFN_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'GFN_PATH', dirname(__FILE__) );
define( 'GFN_VERSION', '1.0.1' );

add_action( 'plugins_loaded', 'gfn_init_plugin' );

// Initialize plugin: Load plugin files
function gfn_init_plugin() {
	if ( !class_exists( 'GFForms' ) ) {
		add_action( 'admin_notices', 'gfn_warn_no_gforms' );
		return;
	}
	
	if ( !defined( 'GRAVITY_FLOW_VERSION' ) ) {
		add_action( 'admin_notices', 'gfn_warn_no_gflow' );
		return;
	}
	include_once( GFN_PATH . '/includes/enqueue.php' );
	include_once( GFN_PATH . '/includes/gravityflow.php' );
	
}

// Require Gravity Forms
function gfn_warn_no_gforms() {
	?>
	<div class="error">
		<p><strong>RS Gravity Flow Notes:</strong> This plugin requires Gravity Forms in order to operate. Please install and activate Gravity Forms, or disable this plugin.</p>
	</div>
	<?php
}
// Require Gravity Flow
function gfn_warn_no_gflow() {
	?>
	<div class="error">
		<p><strong>RS Gravity Flow Notes:</strong> This plugin requires Gravity Flow in order to operate. Please install and activate Gravity Flow, or disable this plugin.</p>
	</div>
	<?php
}