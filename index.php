<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'styles', plugins_url( '/assets/css/styles.css', __FILE__ ), false ); 
} );



add_shortcode( 'crm_dashboard_recently_added', function() {
	if ( function_exists( 'erp_crm_dashboard_widget_latest_contact' ) ) {
		erp_crm_dashboard_widget_latest_contact();
	}
} );

add_shortcode( 'crm_dashboard_total_inbound_emails', function() {
	if ( function_exists( 'erp_crm_dashboard_widget_inbound_emails' ) )
		erp_crm_dashboard_widget_inbound_emails();
} );