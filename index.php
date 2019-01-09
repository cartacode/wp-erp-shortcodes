<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

add_action( 'wp_enqueue_scripts', function() {
    // css for all shortcodes
    $css_files = array(
        'crm' => array( 'recently-added', 'todays-schedules', 'upcoming-schedules', 'w-all-contacts', 'table-view', 'activities', 'circles', 'schedules' )
    );

    foreach ( $css_files as $type => $files ) {
        foreach ( $files as $file ) {
    	   wp_register_style( $file, plugins_url( '/assets/css/' . $type . '/' . $file . '.css', __FILE__ ), false );
        }
    }

    // global scripts
    wp_register_script( 'erp-select2',  WPERP_ASSETS . '/vendor/select2/select2.full.min.js', array( 'jquery' ), '1.0.0', true );

    // js resources for schedules calendar
	wp_register_script( 'erp-momentjs', WPERP_ASSETS . '/vendor/moment/moment.min.js', false, '1.0.0', true );
	wp_register_script( 'erp-popup', WPERP_ASSETS . '/js/jquery-popup.js', array( 'jquery' ), '1.0.0', true );
    wp_register_script( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.js', array( 'jquery', 'erp-momentjs' ), '1.0.0', true );
    wp_register_script( 'erp-js', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'erp-fullcalendar', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );

    // css for schedules calendar
    wp_register_style( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.css', false, '1.0.0' );
    wp_register_style( 'erp-select2', WPERP_ASSETS . '/vendor/select2/select2.min.css', false, '1.0.0' );

    // js resources for contacts list table
    $WP_ERP_MODULES_URL = WPERP_URL . '/modules';
    wp_register_script( 'erp-vuejs', WPERP_ASSETS . '/vendor/vue/vue.min.js', array( 'jquery' ), '1.0.0', true );
    wp_register_script( 'erp-vue-table', $WP_ERP_MODULES_URL . "/crm/assets/js/vue-table.js", array( 'erp-vuejs', 'jquery' ), date( 'Ymd' ), true );
    wp_register_script( 'erp-script', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );
    wp_register_script( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/jquery.tipTip.min.js', array( 'jquery' ), '1.0.0', true );
    wp_register_script( 'erp-crm-contact', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-contacts.js", array( 'erp-vue-table', 'erp-script', 'erp-vuejs', 'underscore', 'erp-tiptip', 'jquery', 'erp-select2' ), date( 'Ymd' ), true );

    // css for contact list table
    wp_register_style( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/tipTip.css', false, '1.0.0' );

    wp_register_script( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.min.js', array( 'jquery', 'erp-momentjs' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.css', false, date( 'Ymd' ) );
    wp_register_script( 'erp-crm', $WP_ERP_MODULES_URL . "/crm/assets/js/crm.js", array( 'erp-script', 'erp-timepicker' ), date( 'Ymd' ), true );

    // for activities
    wp_register_script( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.css', false, date( 'Ymd' ) );

    wp_register_script( 'wp-erp-crm-vue-component', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-components.js", array( 'erp-nprogress', 'erp-script', 'erp-vuejs', 'underscore', 'erp-select2', 'erp-tiptip' ), date( 'Ymd' ), true );

    wp_register_script( 'wp-erp-crm-vue-customer', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-app.js", array( 'wp-erp-crm-vue-component' ), date( 'Ymd' ), true );

    wp_enqueue_style( 'jquery-ui', WPERP_ASSETS . '/vendor/jquery-ui/jquery-ui-1.9.1.custom.css' );

    // calendar
    wp_register_script( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.css', false, date( 'Ymd' ) );

    // for global
    wp_enqueue_script( 'erp-select2' );
    wp_enqueue_script( 'erp-popup' );
    wp_enqueue_script( 'erp-script' );
} );



if ( is_plugin_active('wp-erp/wp-erp.php') ) {
    require_once( dirname( __FILE__ ) . '/includes/hr/dashboard.php' );

    require_once( dirname( __FILE__ ) . '/includes/crm/dashboard.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/contacts.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/companies.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/activities.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/circles.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/schedules.php' );
}