<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/


if ( file_exists( plugin_dir_path( __FILE__ ) . 'config.php' ) )
    require_once( 'config.php' );

require_once( 'functions.php' );
require_once( 'global-shortcodes.php' );

if ( is_admin() ) {
    if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wp-erp/wp-erp.php' ) )
        require_once( 'includes/admin/agent_office_manage.php' );

    return;
}

add_action( 'wp_enqueue_scripts', function() {
    define( 'WP_ERP_MODULES_URL', WPERP_URL . '/modules' );
    $suffix = SCRIPT_DEBUG ? '' : '.min';

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
    wp_register_script( 'erp-vue-table', WP_ERP_MODULES_URL . "/crm/assets/js/vue-table.js", array( 'erp-vuejs', 'jquery' ), date( 'Ymd' ), true );
    wp_register_script( 'erp-script', WPERP_ASSETS . '/js/erp.min.js', array( 'jquery', 'backbone', 'underscore', 'wp-util', 'jquery-ui-datepicker', 'erp-select2' ), '1.0.0', true );
    wp_register_script( 'erp-crm-contact', WP_ERP_MODULES_URL . "/crm/assets/js/crm-contacts.js", array( 'erp-vue-table', 'erp-script', 'erp-vuejs', 'underscore', 'erp-tiptip', 'jquery', 'erp-select2' ), date( 'Ymd' ), true );

    wp_register_style( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/tipTip.css', false, '1.0.0' );
    wp_register_script( 'erp-tiptip', WPERP_ASSETS . '/vendor/tiptip/jquery.tipTip.min.js', array( 'jquery' ), '1.0.0', true );

    wp_register_script( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.min.js', array( 'jquery', 'erp-momentjs' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-timepicker', WPERP_ASSETS . '/vendor/timepicker/jquery.timepicker.css', false, date( 'Ymd' ) );
    
    wp_register_script( 'erp-crm', WP_ERP_MODULES_URL . "/crm/assets/js/crm.js", array( 'erp-script', 'erp-timepicker' ), date( 'Ymd' ), true );

    // for activities
    wp_register_script( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-nprogress', WPERP_ASSETS . '/vendor/nprogress/nprogress.css', false, date( 'Ymd' ) );

    wp_register_script( 'wp-erp-crm-vue-component', WP_ERP_MODULES_URL . "/crm/assets/js/crm-components.js", array( 'erp-nprogress', 'erp-script', 'erp-vuejs', 'underscore', 'erp-select2', 'erp-tiptip' ), date( 'Ymd' ), true );
    wp_register_script( 'erp-email-campaign-contact-activity', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/contact-activity-email-campaign-component.js', [ 'wp-erp-crm-vue-component' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_register_script( 'wp-erp-crm-vue-customer', WP_ERP_MODULES_URL . "/crm/assets/js/crm-app.js", array( 'erp-email-campaign-contact-activity' ), date( 'Ymd' ), true );

    wp_enqueue_style( 'jquery-ui', WPERP_ASSETS . '/vendor/jquery-ui/jquery-ui-1.9.1.custom.css' );
    // Scripts for HR Section
    wp_register_style( 'erp-sweetalert', WPERP_ASSETS . '/vendor/sweetalert/sweetalert.css', false, '1.4.1' );
    wp_register_script( 'erp-sweetalert', WPERP_ASSETS . '/vendor/sweetalert/sweetalert.min.js', array( 'jquery' ), '1.4.1', true );
    wp_register_script( 'wp-erp-hr', WP_ERP_MODULES_URL . "/hrm/assets/js/hrm.min.js", array( 'erp-script' ), date( 'Ymd' ), true );

    // calendar
    wp_register_script( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.js', array( 'jquery' ), date( 'Ymd' ), true );
    wp_register_style( 'erp-trix-editor', WPERP_ASSETS . '/vendor/trix/trix.css', false, date( 'Ymd' ) );

    // email campaign
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

    wp_register_style( 'erp-styles', WPERP_ASSETS . '/css/admin.css', false, date( 'Ymd' ) );
    wp_register_style( 'erp-shortcode-styles', plugins_url( '/assets/css/styles.css', __FILE__ ), [ 'erp-styles', 'erp-nprogress' ] );

    // tags in single contact or company
    wp_register_script( 'tags-suggest', "/wp-admin/js/tags-suggest$suffix.js", array( 'jquery-ui-autocomplete', 'wp-a11y' ), false, 1 );
    wp_localize_script( 'tags-suggest', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    wp_localize_script( 'tags-suggest', 'tagsSuggestL10n', array(
        'tagDelimiter' => _x( ',', 'tag delimiter' ),
        'removeTerm'   => __( 'Remove term:' ),
        'termSelected' => __( 'Term selected.' ),
        'termAdded'    => __( 'Term added.' ),
        'termRemoved'  => __( 'Term removed.' )
    ) );
    wp_register_script( 'tags-box', "/wp-admin/js/tags-box$suffix.js", array( 'jquery', 'tags-suggest' ), false, 1 );

    wp_register_script( 'password-strength-js', plugin_dir_url( __FILE__ ) . 'assets/js/password-strength/password.min.js', array( 'jquery', 'jquery-migrate' ), '1.0.0', true );
    wp_register_style( 'password-strength-css', plugin_dir_url( __FILE__ ) . 'assets/js/password-strength/password.min.css' );
    
    wp_register_script( 'settings-js', plugin_dir_url( __FILE__ ) . 'assets/js/settings.js', array( 'password-strength-js' ), '1.0.0', true );
    wp_register_style( 'settings-css', plugin_dir_url( __FILE__ ) . 'assets/css/settings.css', array( 'password-strength-css' ) );

    global $wp;
    if ( $wp->request == 'dashboard/settings' ) {
        wp_enqueue_style( 'settings-css' );
    }
} );

add_action( 'init', function() {
    if ( 
        !function_exists( 'is_plugin_active' ) || 
        !is_plugin_active( 'wp-erp/wp-erp.php' ) ||
        !is_plugin_active( 'erp-email-campaign/wp-erp-email-campaign.php' ) ||
        !is_plugin_active( 'erp-deals/wp-erp-deals.php' )
    )
        return;

    require_once( 'includes/hr/dashboard.php' );
    require_once( 'includes/hr/employees.php' );

    require_once( 'includes/crm/dashboard.php' );
    require_once( 'includes/crm/contacts.php' );
    require_once( 'includes/crm/companies.php' );
    require_once( 'includes/crm/activities.php' );
    require_once( 'includes/crm/circles.php' );
    require_once( 'includes/crm/schedules.php' );
    require_once( 'includes/crm/email_connect.php' );
  
    require_once( 'includes/tools/init.php' );

    require_once( 'includes/crm/emarketing.php' );

    require_once( 'includes/settings/update-profile.php' );

    // require_once( 'includes/deals/init.php' );    
} );

add_action( 'init', function() {
    if ( !isset( $_POST['_wpnonce'] ) || !is_user_logged_in() )
        return;

    if ( wp_verify_nonce( $_POST['_wpnonce'], 'update-profile' ) ) {
        $is_updated = update_user_profile( $_POST, $_FILES['avatar'] );
    }

    if ( wp_verify_nonce( $_POST['_wpnonce'], 'update-office-profile' ) ) {
        $is_updated = update_office_profile( $_POST, $_FILES['office_logo'] );
    }
} );