<?php
/**
 * Plugin Name: WP ERP Shortcodes
 * Description: Shortcodes for WP ERP
 * Author: Square 1 Group
**/

if ( is_admin() )
    return;

add_action( 'wp_enqueue_scripts', function() {
    $WP_ERP_MODULES_URL = WPERP_URL . '/modules';
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
} );

add_action( 'init', function() {
    if ( function_exists( 'is_plugin_active' ) && is_plugin_active('wp-erp/wp-erp.php') ) {
        require_once( dirname( __FILE__ ) . '/includes/hr/dashboard.php' );
        require_once( dirname( __FILE__ ) . '/includes/hr/employees.php' );

        require_once( dirname( __FILE__ ) . '/includes/crm/dashboard.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/contacts.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/companies.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/activities.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/circles.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/schedules.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/email_connect.php' );
      
        require_once( dirname( __FILE__ ) . '/includes/crm/import.php' );
        require_once( dirname( __FILE__ ) . '/includes/crm/export.php' );

        if ( is_plugin_active('erp-email-campaign/wp-erp-email-campaign.php') ) {
            require_once( dirname( __FILE__ ) . '/includes/crm/emarketing.php' );
        }
    }
} );


if ( !function_exists( 'is_admin_request' ) ) {
    function is_admin_request() {
        return ( strpos( $_SERVER['HTTP_REFERER'], 'admin.php' ) !== false ) || ( strpos( $_SERVER['HTTP_REFERER'], '/wp-admin' ) !== false );
    }
}


if ( !function_exists( 'get_default_localize_script' ) ) {
    function get_default_localize_script() {
        return apply_filters( 'erp_crm_localize_script', array(
            'ajaxurl'               => admin_url( 'admin-ajax.php' ),
            'nonce'                 => wp_create_nonce( 'wp-erp-crm-nonce' ),
            'popup'                 => array(
                'customer_title'         => __( 'Add New Customer', 'erp' ),
                'customer_update_title'  => __( 'Edit Customer', 'erp' ),
                'customer_social_title'  => __( 'Customer Social Profile', 'erp' ),
                'customer_assign_group'  => __( 'Add to Contact groups', 'erp' ),
            ),
            'add_submit'                  => __( 'Add New', 'erp' ),
            'update_submit'               => __( 'Update', 'erp' ),
            'save_submit'                 => __( 'Save', 'erp' ),
            'customer_upload_photo'       => __( 'Upload Photo', 'erp' ),
            'customer_set_photo'          => __( 'Set Photo', 'erp' ),
            'confirm'                     => __( 'Are you sure?', 'erp' ),
            'delConfirmCustomer'          => __( 'Are you sure to delete?', 'erp' ),
            'delConfirm'                  => __( 'Are you sure to delete this?', 'erp' ),
            'checkedConfirm'              => __( 'Select atleast one group', 'erp' ),
            'contact_exit'                => __( 'Already exists as a contact or company', 'erp' ),
            'make_contact_text'           => __( 'This user already exists! Do you want to make this user as a', 'erp' ),
            'wpuser_make_contact_text'    => __( 'This is wp user! Do you want to create this user as a', 'erp' ),
            'create_contact_text'         => __( 'Create new', 'erp' ),
            'current_user_id'             => get_current_user_id(),
            'successfully_created_wpuser' => __( 'WP User created successfully', 'erp' ),
        ) );
    }
}

if ( !function_exists( 'get_default_contact_actvity_localize' ) ) {
    function get_default_contact_actvity_localize() {
        return apply_filters( 'erp_crm_contact_localize_var', [
            'ajaxurl'              => admin_url( 'admin-ajax.php' ),
            'nonce'                => wp_create_nonce( 'wp-erp-crm-customer-feed' ),
            'current_user_id'      => get_current_user_id(),
            'isAdmin'              => current_user_can( 'manage_options' ),
            'isCrmManager'         => current_user_can( 'erp_crm_manager' ),
            'isAgent'              => current_user_can( 'erp_crm_agent' ),
            'confirm'              => __( 'Are you sure?', 'erp' ),
            'date_format'          => get_option( 'date_format' ),
            'timeline_feed_header' => apply_filters( 'erp_crm_contact_timeline_feeds_header', '' ),
            'timeline_feed_body'   => apply_filters( 'erp_crm_contact_timeline_feeds_body', '' ),
        ] );
    }
}

add_shortcode( 'current-user-avatar', function( $atts ) {
    extract(shortcode_atts(array(
        'size' => 32
    ), $atts));

    $employee = new \WeDevs\ERP\HRM\Employee( get_current_user_id() );

    $template = '';
    ob_start();
?>
    <div class="current-user-avatar">
        <?php _e( $employee->get_avatar( $size ) ); ?>
    </div>

    <style type="text/css">
    /* avatar in nav */
    .sidr-class-current-user-avatar,
    .current-user-avatar {
        text-align: center;
    }

    .sidr-class-current-user-avatar img,
    .current-user-avatar img {
        border-radius: 50%;
    }
    </style>
<?php
    $template = ob_get_contents();
    ob_end_clean();

    return $template;
} );

// wp list table pagination
if ( !function_exists( 'wp_list_table_pagination' ) ) {
    function wp_list_table_pagination() {
        if ( !isset( $_REQUEST['paged'] ) ) {
            $_REQUEST['paged'] = explode( '/page/', $_SERVER['REQUEST_URI'], 2 );
        
            if ( isset( $_REQUEST['paged'][1] ) )
                list( $_REQUEST['paged'], ) = explode( '/', $_REQUEST['paged'][1], 2 );

            if ( isset( $_REQUEST['paged'] ) && $_REQUEST['paged'] != '' ) {
                $_REQUEST['paged'] = $_REQUEST['paged'] < 2 ? '' : intval( $_REQUEST['paged'] );
            } else {
                $_REQUEST['paged'] = '';
            }
        }
    }
}

function current_wp_erp_user_is( $user_role ) {
    $owner_id = get_user_meta( get_current_user_id(), 'created_by', true );
    
    switch ( $user_role ) {
        case 'broker':
            return $owner_id ? user_can( $owner_id, 'administrator' ) : false;

        case 'staff':
            $is_staff_or_team_user = get_user_meta( get_current_user_id(), 'is_staff_or_team_user', true );
            $o_owner_id = get_user_meta( $owner_id, 'created_by', true );

            return ( $o_owner_id ? user_can( $o_owner_id, 'administrator' ) : false ) && $is_staff_or_team_user == 'on';
        
        default:
            return false;
    }
}

add_shortcode( 'switch_back_link', function() {
    if ( !class_exists( 'user_switching') )
        return;

    $old_user = user_switching::get_old_user();
    if ( !$old_user )
        return;

    $link = sprintf(
        /* Translators: 1: user display name; 2: username; */
        __( 'Switch back to %1$s (%2$s)', 'user-switching' ),
        $old_user->display_name,
        $old_user->user_login
    );
    $url = add_query_arg( array( 'redirect_to' => urlencode( user_switching::current_url() ) ), user_switching::switch_back_url( $old_user ) );

    return '<a href="' . esc_url( $url ) . '" style="display: block; color: #fff; text-align: center;">' . esc_html( $link ) . '</a>';
} );