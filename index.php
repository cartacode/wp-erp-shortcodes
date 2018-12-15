<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'styles', plugins_url( '/assets/css/styles.css', __FILE__ ), false ); 
} );


require_once( dirname( __FILE__ ) . '/includes/crm-dashboard.php' );