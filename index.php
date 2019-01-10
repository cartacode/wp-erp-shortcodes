<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

add_action( 'wp_enqueue_scripts', function() {
    // css for all shortcodes
    $css_files = array(
        'crm' => array( 'recently-added', 'todays-schedules', 'upcoming-schedules', 'w-all-contacts', 'table-view', 'activities', 'circles', 'schedules', 'emarketing' )
    );

    foreach ( $css_files as $type => $files ) {
        foreach ( $files as $file ) {
           wp_register_style( $file, plugins_url( '/assets/css/' . $type . '/' . $file . '.css', __FILE__ ), false );
        }
    }

    $WP_ERP_MODULES_URL = WPERP_URL . '/modules';

    wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js' );
    // global scripts
    wp_register_style( 'erp-select2', WPERP_ASSETS . '/vendor/select2/select2.min.css', false, '1.0.0' );
    wp_register_script( 'erp-select2',  WPERP_ASSETS . '/vendor/select2/select2.full.min.js', array( 'jquery' ), '1.0.0', true );

    // js resources for schedules calendar
    wp_register_script( 'erp-momentjs', WPERP_ASSETS . '/vendor/moment/moment.min.js', false, '1.0.0', true );
    wp_register_script( 'erp-popup', WPERP_ASSETS . '/js/jquery-popup.js', array( 'jquery' ), '1.0.0', true );
    
    wp_register_style( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.css', false, '1.0.0' );
    wp_register_script( 'erp-fullcalendar', WPERP_ASSETS . '/vendor/fullcalendar/fullcalendar.min.js', array( 'jquery', 'erp-momentjs' ), '1.0.0', true );

    wp_register_script( 'erp-js', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'erp-fullcalendar', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );

    // js resources for contacts list table
    wp_register_script( 'erp-vuejs', WPERP_ASSETS . '/vendor/vue/vue.min.js', array( 'jquery' ), '1.0.0', true );
    wp_register_script( 'erp-vue-table', $WP_ERP_MODULES_URL . "/crm/assets/js/vue-table.js", array( 'erp-vuejs', 'jquery' ), date( 'Ymd' ), true );
    wp_register_script( 'erp-script', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );
    wp_register_script( 'erp-crm-contact', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-contacts.js", array( 'erp-vue-table', 'erp-script', 'erp-vuejs', 'underscore', 'erp-tiptip', 'jquery', 'erp-select2' ), date( 'Ymd' ), true );

    wp_register_style( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/tipTip.css', false, '1.0.0' );
    wp_register_script( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/jquery.tipTip.min.js', array( 'jquery' ), '1.0.0', true );

    wp_register_script( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.min.js', array( 'jquery', 'erp-momentjs' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.css', false, date( 'Ymd' ) );
    
    wp_register_script( 'erp-crm', $WP_ERP_MODULES_URL . "/crm/assets/js/crm.js", array( 'erp-script', 'erp-timepicker' ), date( 'Ymd' ), true );

    // for activities
    wp_register_script( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.css', false, date( 'Ymd' ) );

    wp_register_script( 'wp-erp-crm-vue-component', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-components.js", array( 'erp-nprogress', 'erp-script', 'erp-vuejs', 'underscore', 'erp-select2', 'erp-tiptip' ), date( 'Ymd' ), true );

    wp_register_script( 'wp-erp-crm-vue-customer', $WP_ERP_MODULES_URL . "/crm/assets/js/crm-app.js", array( 'wp-erp-crm-vue-component' ), date( 'Ymd' ), true );

    wp_enqueue_style( 'jquery-ui', WPERP_ASSETS . '/vendor/jquery-ui/jquery-ui-1.9.1.custom.css' );
    // Scripts for HR Section
    wp_register_style( 'erp-sweetalert', WPERP_ASSETS . '/vendor/sweetalert/sweetalert.css', false, '1.4.1' );
    wp_register_script( 'erp-sweetalert', WPERP_ASSETS . '/vendor/sweetalert/sweetalert.min.js', array( 'jquery' ), '1.4.1', true );
   wp_register_script( 'wp-erp-hr', $WP_ERP_MODULES_URL . "/hrm/assets/js/hrm.min.js", array( 'erp-script' ), date( 'Ymd' ), true );

    // calendar
    wp_register_script( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.css', false, date( 'Ymd' ) );

    // email campaign
    wp_register_style( 'erp-styles', WPERP_ASSETS . '/css/admin.css', false, date( 'Ymd' ) );
    wp_register_style( 'erp-fontawesome', WPERP_ASSETS . '/vendor/fontawesome/font-awesome.min.css', false, date( 'Ymd' ) );

    wp_register_script( 'erp-sortable', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/Sortable.js', [], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_register_script( 'wp-color-picker', admin_url( '/js/color-picker.min.js' ), [], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_register_style(
        'erp-email-campaign-editor',
        WPERP_EMAIL_CAMPAIGN_ASSETS . '/css/erp-email-campaign-editor.css', [
        'wp-color-picker', 'erp-styles', 'erp-timepicker',
        'erp-fontawesome', 'erp-sweetalert', 'erp-nprogress', 'erp-email-campaign-template-style'
    ], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_register_script(
        'erp-email-campaign-editor',
        WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/erp-email-campaign-editor.js', [
        'erp-vuejs', 'jquery', 'jquery-ui-datepicker', 'wp-color-picker',
        'erp-timepicker', 'erp-sortable', 'erp-tiptip', 'erp-select2', 'erp-sweetalert', 'erp-nprogress'
    ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_register_script( 'erp-flotchart',  WPERP_ASSETS . '/vendor/flot/jquery.flot.min.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_script( 'erp-flotchart-pie', WPERP_ASSETS . '/vendor/flot/jquery.flot.pie.min.js', array( 'jquery' ), date( 'Ymd' ), true );

    wp_register_style( 'erp-flotchart-valuelabel-css', WPERP_ASSETS . '/vendor/flot/plot.css', false, date( 'Ymd' ) );

    wp_register_style( 'erp-email-campaign-vendor', WPERP_EMAIL_CAMPAIGN_ASSETS . '/css/erp-email-campaign-vendor.css', [], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_register_script( 'erp-email-campaign-vendor', WPERP_EMAIL_CAMPAIGN_ASSETS . "/js/erp-email-campaign-vendor.js", [ 'jquery' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_register_style(
        'erp-email-campaign',
        WPERP_EMAIL_CAMPAIGN_ASSETS . '/css/erp-email-campaign.css',
        [ 'erp-styles', 'erp-nprogress', 'erp-flotchart-valuelabel-css', 'erp-email-campaign-vendor' ],
        WPERP_EMAIL_CAMPAIGN_VERSION
    );
    wp_register_script( 
        'erp-email-campaign', 
        WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/erp-email-campaign.js', [ 
            'jquery', 'erp-vuejs', 'erp-nprogress', 'erp-flotchart', 'erp-flotchart-pie', 'erp-tiptip', 'erp-email-campaign-vendor', 'erp-momentjs' 
    ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    // for global
    wp_enqueue_script( 'erp-select2' );
    wp_enqueue_script( 'erp-popup' );
    wp_enqueue_script( 'erp-script' );
} );



if ( is_plugin_active('wp-erp/wp-erp.php') ) {
    require_once( dirname( __FILE__ ) . '/includes/hr/dashboard.php' );
    require_once( dirname( __FILE__ ) . '/includes/hr/employees.php' );

    require_once( dirname( __FILE__ ) . '/includes/crm/dashboard.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/contacts.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/companies.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/activities.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/circles.php' );
    require_once( dirname( __FILE__ ) . '/includes/crm/schedules.php' );
  
    require_once( dirname( __FILE__ ) . '/includes/crm/import.php' );


    if ( is_plugin_active('erp-email-campaign/wp-erp-email-campaign.php') ) {
        require_once( dirname( __FILE__ ) . '/includes/crm/emarketing.php' );
    }
}