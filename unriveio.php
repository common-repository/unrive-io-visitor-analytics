<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: Unrive.io - Visitor Analytics
Plugin URI: https://unrive.io/pages/unrive-wordpress-plugin
Description: A free privacy preserving, cookie-free visitor tracking and traffic counter plugin for your wordPress blog. The plugin integrates into unrive.io API and generates web traffic statistics directly into your wordPress admin through widgets.
Version: 1.1
Author: Unrive.io
Author URI: https://unrive.io
License: GPL2
*/

define( 'UNRIVEIO_VERSION', '1.1' );
define( 'UNRIVEIO__MINIMUM_WP_VERSION', '6.0' );
define( 'UNRIVEIO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


//check 
if(isset($_GET['page'])) {
if($_GET['page'] == 'unriveio_stats') {
add_action( 'admin_head',  'unriveio_stats_admin_head' );
} else {
remove_action( 'admin_head',  'unriveio_stats_admin_head' );
}
}

//Require the dependencies
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	require_once(ABSPATH . 'wp-admin/includes/screen.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
    require_once(ABSPATH . 'wp-admin/includes/template.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'class.unriveio-admin.php' );
	require_once(UNRIVEIO__PLUGIN_DIR . 'class.unriveio.php' );
	require_once(UNRIVEIO__PLUGIN_DIR . 'utils/class.utils.php' );
	require_once(UNRIVEIO__PLUGIN_DIR . 'utils/class.count_Iterator.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'html/base.php' );
	require_once(UNRIVEIO__PLUGIN_DIR . 'html/Unriveio_DataTable.php' );
	require_once(UNRIVEIO__PLUGIN_DIR . 'widgets/class.visits.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'widgets/class.visitsDays.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'widgets/class.MostReadDays.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'widgets/class.TrafficSourcesDays.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'widgets/class.TrafficRegionDays.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'api/unriveio_wp_remote_static.php');
	require_once(UNRIVEIO__PLUGIN_DIR . 'api/unriveio_wp_remote.php');
	

	add_action( 'init', array( 'Unriveio_Admin', 'init' ) );	
	add_action( 'init', array( 'Unriveio', 'init' ) );	
	add_action( 'init', array( 'Unriveio', 'init' ) );
}


/**
 * Style and Typography for The Admin  
 */
function unriveio_stats_admin_head() {
  wp_register_style( 'unriveio-fonts-googleapi', 'https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap' );
  wp_register_style( 'unriveio-style', esc_url( plugins_url( 'unriveio/css/style.css', dirname(__FILE__) ) )  );
  wp_enqueue_style( 'unriveio-fonts-googleapi' );
  wp_enqueue_style( 'unriveio-style');
}

add_action( 'admin_enqueue_scripts', 'unriveio_stats_admin_head');


/**
 * JS tracking code for unrive analytics 
 */
function unriveio_js_tracking() {
	wp_register_script( 'unriveiotracking', 'https://unrive.io/js/script.js' );
	wp_enqueue_script( 'unriveiotracking' );
}

add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	if ( 'unriveiotracking' !== $handle ) {
		return $tag;
	}
	return str_replace( ' src', ' data-host="https://unrive.io" data-dnt="false" id="ZwSg9rf6GA" async defer src', $tag ); // defer the script
}, 10, 2 );

//Adds default visitor tracking code
add_action('wp_head','unriveio_js_tracking');
