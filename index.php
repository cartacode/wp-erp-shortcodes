<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'styles', plugins_url( '/assets/css/styles.css', __FILE__ ), false );

    wp_register_script( 'erp-select2',  WPERP_ASSETS . '/vendor//select2/select2.full.min.js', array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'erp-momentjs', WPERP_ASSETS . '/vendor/moment/moment.min.js', false, '1.0.0', true );
	wp_register_script( 'erp-popup', WPERP_ASSETS . '/js/jquery-popup.min.js', array( 'jquery' ), '1.0.0', true );
    wp_register_script( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.js', array( 'jquery', 'erp-momentjs', 'erp-popup' ), '1.0.0', true );
    wp_register_script( 'erp-js', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'erp-fullcalendar', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );
    wp_enqueue_script( 'erp-js' );

    wp_register_style( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.css', false, '1.0.0' );
    wp_enqueue_style( 'erp-fullcalendar' );
    
    wp_register_style( 'erp-select2', WPERP_ASSETS . '/vendor/select2/select2.min.css', false, '1.0.0' );
    wp_enqueue_style( 'erp-select2' );

	erp_get_js_template( WPERP_CRM_JS_TMPL . '/single-schedule-details.php', 'erp-crm-single-schedule-details' );
} );



if ( is_plugin_active('wp-erp/wp-erp.php') ) {
	require_once( dirname( __FILE__ ) . '/includes/crm-dashboard.php' );
}