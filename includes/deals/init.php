<?php
use WeDevs\ERP\CRM\Deals\Helpers;

add_action( 'wp_enqueue_scripts', function() {
	$time_format = get_option( 'time_format', 'g:i a' );
    $erp_deals_global = [
        'ajaxurl'           => admin_url( 'admin-ajax.php' ),
        'nonce'             => wp_create_nonce( 'erp-deals' ),
        'scriptDebug'       => defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : false,
        'date'              => [
            'format'        => Helpers::js_date_format(),
            'placeholder'   => erp_format_date( 'now' )
        ],
        'time'              => [
            'format'        => $time_format,
            'placeholder'   => date( $time_format )
        ],
    ];
    
    wp_enqueue_script( 'erp-moment-tz', WPERP_DEALS_ASSETS . '/vendor/moment/moment-timezone-with-data.js', ['erp-momentjs'], WPERP_DEALS_VERSION, true );

    $style_deps = [
        'erp-styles', 'erp-timepicker', 'erp-fontawesome',
        'erp-sweetalert', 'erp-nprogress', 'erp-trix-editor'
    ];

    $script_deps = [
        'jquery', 'erp-vuejs', 'jquery-ui-datepicker', 'jquery-ui-sortable',
        'erp-timepicker', 'erp-sweetalert', 'erp-nprogress', 'erp-trix-editor',
        'erp-moment-tz'
    ];

    $erp_deals_global['isUserAnAdmin']  = current_user_can( 'administrator' );
    $erp_deals_global['isUserAManager'] = erp_crm_is_current_user_manager();
    $erp_deals_global['isUserAnAgent']  = erp_crm_is_current_user_crm_agent();
    $erp_deals_global['currentUserId']  = get_current_user_id();
    $erp_deals_global['i18n']           = $this->i18n();
    $erp_deals_global['activityTypes']  = Helpers::get_activity_types();
    $erp_deals_global['lostReasons']    = Helpers::get_lost_reasons();
    $erp_deals_global['pipelineURL']    = Helpers::admin_url( [] );
    $erp_deals_global['pluginURL']      = WPERP_DEALS_URL;
    $erp_deals_global['singlePageURL']  = Helpers::admin_url( [ 'sub-section' => 'all-deals', 'action' => 'view-deal', 'id' => 'DEALID' ] );
    $erp_deals_global['pipes']          = Helpers::get_pipelines_with_stages();
    $erp_deals_global['wpTimezone']     = Helpers::get_wp_timezone();
    $erp_deals_global['subSection']    = $sub_section;

    if ( "dashboard" === $sub_section ) { // overview page
        $erp_deals_global['crmAgents'] = Helpers::get_crm_agents( [], true, true );

        $script_deps[] = 'erp-flotchart';
        $script_deps[] = 'erp-flotchart-categories';
        $script_deps[] = 'erp-flotchart-stack';
    }

    if ( "activities" === $sub_section ) { // activities page
        $erp_deals_global['users']          = Helpers::get_crm_agents_with_current_user();
        $erp_deals_global['activitiesURL']  = Helpers::admin_url( [] );
    }

    if ( isset( $_GET['action'] ) && 'view-deal' === $_GET['action'] ) { // single deal page
        wp_enqueue_style( 'tiny-mce', site_url( '/wp-includes/css/editor.css' ), [], WPERP_DEALS_VERSION );
        $style_deps[] = 'tiny-mce';

        wp_enqueue_script( 'tiny-mce', site_url( '/wp-includes/js/tinymce/tinymce.min.js' ), [] );
        wp_enqueue_script( 'tiny-mce-code', WPERP_DEALS_ASSETS . '/vendor/tinymce/plugins/code/plugin.min.js', [ 'tiny-mce' ], WPERP_DEALS_VERSION, true );
        wp_enqueue_script( 'tiny-mce-hr', WPERP_DEALS_ASSETS . '/vendor/tinymce/plugins/hr/plugin.min.js', [ 'tiny-mce' ], WPERP_DEALS_VERSION, true );
        $script_deps[] = 'tiny-mce';
        $script_deps[] = 'tiny-mce-code';
        $script_deps[] = 'tiny-mce-hr';

        $erp_deals_global['emailTemplates'] = \WeDevs\ERP\CRM\Models\Save_Replies::orderBy( 'name', 'asc' )->get();
        $erp_deals_global['shortcodes'] = deal_shortcodes()->shortcodes();
    }

    // countUp js
    wp_enqueue_script( 'erp-deals-countup', WPERP_DEALS_ASSETS . '/vendor/countUp/countUp.js', [], WPERP_DEALS_VERSION, true );
    $script_deps[] = 'erp-deals-countup';

    // pipedings
    wp_enqueue_style( 'erp-deals-pipedings', WPERP_DEALS_ASSETS . '/vendor/pipedings/css/pipedings.css', [], WPERP_DEALS_VERSION );
    $style_deps[] = 'erp-deals-pipedings';

    // plugin assets
    wp_enqueue_style( 'erp-deals', WPERP_DEALS_ASSETS . '/css/erp-deals.css', $style_deps, WPERP_DEALS_VERSION );
    wp_enqueue_script( 'erp-deals', WPERP_DEALS_ASSETS . '/js/erp-deals.js', $script_deps, WPERP_DEALS_VERSION, true );
    wp_localize_script( 'erp-deals', 'erpDealsGlobal', $erp_deals_global );
} );