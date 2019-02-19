<?php
add_shortcode( 'employees-list-table', function() {
    if( ! is_admin() ){
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        require_once( ABSPATH . 'wp-admin/includes/screen.php' );
        require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
        require_once( ABSPATH . 'wp-admin/includes/template.php' );
    }

    wp_list_table_pagination();
    
    wp_enqueue_media();
       
    $localize_script = apply_filters( 'erp_hr_localize_script', array(
            'nonce'                  => wp_create_nonce( 'wp-erp-hr-nonce' ),
            'popup'                  => array(
                'dept_title'        => __( 'New Department', 'erp' ),
                'dept_submit'       => __( 'Create Department', 'erp' ),
                'location_title'    => __( 'New Location', 'erp' ),
                'location_submit'   => __( 'Create Location', 'erp' ),
                'dept_update'       => __( 'Update Department', 'erp' ),
                'desig_title'       => __( 'New Designation', 'erp' ),
                'desig_submit'      => __( 'Create Designation', 'erp' ),
                'desig_update'      => __( 'Update Designation', 'erp' ),
                'employee_title'    => __( 'New Employee', 'erp' ),
                'employee_create'   => __( 'Create Employee', 'erp' ),
                'employee_update'   => __( 'Update Employee', 'erp' ),
                'employment_status' => __( 'Employment Status', 'erp' ),
                'update_status'     => __( 'Update', 'erp' ),
                'policy'            => __( 'Leave Policy', 'erp' ),
                'policy_create'     => __( 'Create Policy', 'erp' ),
                'holiday'           => __( 'Holiday', 'erp' ),
                'holiday_create'    => __( 'Create Holiday', 'erp' ),
                'holiday_update'    => __( 'Update Holiday', 'erp' ),
                'new_leave_req'     => __( 'Leave Request', 'erp' ),
                'take_leave'        => __( 'Send Leave Request', 'erp' ),
                'terminate'         => __( 'Terminate', 'erp' ),
                'leave_reject'      => __( 'Reject Reason', 'erp' ),
                'already_terminate' => __( 'Sorry, this employee is already terminated', 'erp' ),
                'already_active'    => __( 'Sorry, this employee is already active', 'erp' )
            ),
            'asset_url'              => WPERP_ASSETS,
            'emp_upload_photo'       => __( 'Upload Photo', 'erp' ),
            'emp_set_photo'          => __( 'Set Photo', 'erp' ),
            'confirm'                => __( 'Are you sure?', 'erp' ),
            'delConfirmDept'         => __( 'Are you sure to delete this department?', 'erp' ),
            'delConfirmPolicy'       => __( 'If you delete this policy, the leave entitlements and requests related to it will also be deleted. Are you sure to delete this policy?', 'erp' ),
            'delConfirmHoliday'      => __( 'Are you sure to delete this Holiday?', 'erp' ),
            'delConfirmEmployee'     => __( 'Are you sure to delete this employee?', 'erp' ),
            'restoreConfirmEmployee' => __( 'Are you sure to restore this employee?', 'erp' ),
            'delConfirmEmployeeNote' => __( 'Are you sure to delete this employee note?', 'erp' ),
            'delConfirmEntitlement'  => __( 'Are you sure to delete this Entitlement? If yes, then all leave request under this entitlement also permanently deleted', 'erp' ),
            'make_employee_text'     => __( 'This user already exists, Do you want to make this user as a employee?', 'erp' ),
            'employee_exit'          => __( 'This employee already exists', 'erp' ),
            'employee_created'       => __( 'Employee successfully created', 'erp' ),
            'create_employee_text'   => __( 'Click to create employee', 'erp' ),
            'empty_entitlement_text' => sprintf( '<span>%s <a href="%s" title="%s">%s</a></span>', __( 'Please create entitlement first', 'erp' ), add_query_arg( [
                'page'          => 'erp-hr',
                'section'       => 'leave',
                'sub-section'   => 'assignment'
            ], admin_url( 'admin.php' ) ), __( 'Create Entitlement', 'erp' ), __( 'Create Entitlement', 'erp' ) ),
        ) );

    
    wp_localize_script( 'erp-vue-table', 'wpVueTable', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'wp-erp-vue-table' )
    ] );

   if( function_exists( 'erp_get_js_template' ) ) {
        erp_get_js_template( WPERP_MODULES . '/hrm/views/js-templates/new-employee.php', 'erp-new-employee' );
        erp_get_js_template( WPERP_MODULES . '/hrm/views/js-templates/row-employee.php', 'erp-employee-row' );
    }
    $employee  = new \WeDevs\ERP\HRM\Employee();
    $localize_script['employee_empty'] = $employee->to_array();

 
    wp_enqueue_script( 'erp-tiptip' );
    wp_enqueue_style( 'erp-sweetalert' );
    wp_enqueue_script( 'erp-sweetalert' );
    wp_enqueue_script( 'wp-erp-hr' );
    wp_localize_script( 'wp-erp-hr', 'wpErpHr', $localize_script );
    wp_enqueue_style( 'erp-shortcode-styles' );
    wp_enqueue_style( 'erp-select2' );



    
  

    $template = '';

    $template .= '<div class="wrap erp-hr-employees" id="wp-erp">';

   

    $template .= '<div class="list-table-wrap erp-hr-employees-wrap">
        <div class="list-table-inner erp-hr-employees-wrap-inner">

            <form method="get">
                <input type="hidden" name="page" value="erp-hr">
                <input type="hidden" name="section" value="employee">';
                ob_start();
                    
                    $employee_table = new \WeDevs\ERP\HRM\Employee_List_Table();
                    $employee_table->prepare_items();
                    $employee_table->search_box( __( 'Search Employee', 'erp' ), 'erp-employee-search' );
                    $employee_table->views();
                    $employee_table->display();
                    
                    $template .= ob_get_contents(); 

                    ob_end_clean();

            $template .= '</form>

        </div><!-- .list-table-inner -->
    </div><!-- .list-table-wrap -->
   </div>';


    $template .= file_get_contents( dirname( __FILE__ ) . '/demo-modal.php' );


    return $template;

} );

add_shortcode( 'single-employee-view', function() {
    $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

    $employee = new WeDevs\ERP\HRM\Employee( $id );

    if ( ! $employee->get_user_id() ) {
        wp_die( __( 'Employee not found!', 'erp' ) );
    }
    $localize_script = apply_filters( 'erp_hr_localize_script', array(
        'nonce'                  => wp_create_nonce( 'wp-erp-hr-nonce' ),
        'popup'                  => array(
            'dept_title'        => __( 'New Department', 'erp' ),
            'dept_submit'       => __( 'Create Department', 'erp' ),
            'location_title'    => __( 'New Location', 'erp' ),
            'location_submit'   => __( 'Create Location', 'erp' ),
            'dept_update'       => __( 'Update Department', 'erp' ),
            'desig_title'       => __( 'New Designation', 'erp' ),
            'desig_submit'      => __( 'Create Designation', 'erp' ),
            'desig_update'      => __( 'Update Designation', 'erp' ),
            'employee_title'    => __( 'New Employee', 'erp' ),
            'employee_create'   => __( 'Create Employee', 'erp' ),
            'employee_update'   => __( 'Update Employee', 'erp' ),
            'employment_status' => __( 'Employment Status', 'erp' ),
            'update_status'     => __( 'Update', 'erp' ),
            'policy'            => __( 'Leave Policy', 'erp' ),
            'policy_create'     => __( 'Create Policy', 'erp' ),
            'holiday'           => __( 'Holiday', 'erp' ),
            'holiday_create'    => __( 'Create Holiday', 'erp' ),
            'holiday_update'    => __( 'Update Holiday', 'erp' ),
            'new_leave_req'     => __( 'Leave Request', 'erp' ),
            'take_leave'        => __( 'Send Leave Request', 'erp' ),
            'terminate'         => __( 'Terminate', 'erp' ),
            'leave_reject'      => __( 'Reject Reason', 'erp' ),
            'already_terminate' => __( 'Sorry, this employee is already terminated', 'erp' ),
            'already_active'    => __( 'Sorry, this employee is already active', 'erp' )
        ),
        'asset_url'              => WPERP_ASSETS,
        'emp_upload_photo'       => __( 'Upload Photo', 'erp' ),
        'emp_set_photo'          => __( 'Set Photo', 'erp' ),
        'confirm'                => __( 'Are you sure?', 'erp' ),
        'delConfirmDept'         => __( 'Are you sure to delete this department?', 'erp' ),
        'delConfirmPolicy'       => __( 'If you delete this policy, the leave entitlements and requests related to it will also be deleted. Are you sure to delete this policy?', 'erp' ),
        'delConfirmHoliday'      => __( 'Are you sure to delete this Holiday?', 'erp' ),
        'delConfirmEmployee'     => __( 'Are you sure to delete this employee?', 'erp' ),
        'restoreConfirmEmployee' => __( 'Are you sure to restore this employee?', 'erp' ),
        'delConfirmEmployeeNote' => __( 'Are you sure to delete this employee note?', 'erp' ),
        'delConfirmEntitlement'  => __( 'Are you sure to delete this Entitlement? If yes, then all leave request under this entitlement also permanently deleted', 'erp' ),
        'make_employee_text'     => __( 'This user already exists, Do you want to make this user as a employee?', 'erp' ),
        'employee_exit'          => __( 'This employee already exists', 'erp' ),
        'employee_created'       => __( 'Employee successfully created', 'erp' ),
        'create_employee_text'   => __( 'Click to create employee', 'erp' ),
        'empty_entitlement_text' => sprintf( '<span>%s <a href="%s" title="%s">%s</a></span>', __( 'Please create entitlement first', 'erp' ), add_query_arg( [
            'page'          => 'erp-hr',
            'section'       => 'leave',
            'sub-section'   => 'assignment'
        ], admin_url( 'admin.php' ) ), __( 'Create Entitlement', 'erp' ), __( 'Create Entitlement', 'erp' ) ),
    ) );

    $localize_script['employee_empty'] = $employee->to_array();

    erp_get_js_template( WPERP_HRM_JS_TMPL . '/work-experience.php', 'erp-employment-work-experience' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/education-form.php', 'erp-employment-education' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/dependents.php', 'erp-employment-dependent' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/performance-reviews.php', 'erp-employment-performance-reviews' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/performance-comments.php', 'erp-employment-performance-comments' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/performance-goals.php', 'erp-employment-performance-goals' );    
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/new-employee.php', 'erp-new-employee' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/row-employee.php', 'erp-employee-row' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/employment-status.php', 'erp-employment-status' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/compensation.php', 'erp-employment-compensation' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/job-info.php', 'erp-employment-jobinfo' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/new-dept.php', 'erp-new-dept' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/new-designation.php', 'erp-new-desig' );
    erp_get_js_template( WPERP_HRM_JS_TMPL . '/employee-terminate.php', 'erp-employment-terminate' );

    wp_enqueue_script( 'erp-tiptip' );
    wp_enqueue_script( 'wp-erp-hr' );
    wp_localize_script( 'wp-erp-hr', 'wpErpHr', $localize_script );
    wp_enqueue_style( 'erp-shortcode-styles' );

    ob_start();
    
    include dirname( __FILE__ ) . '/single-employee.php';
    
    $template = ob_get_contents();

    ob_end_clean();
    
    $template .= file_get_contents( dirname( __FILE__ ) . '/demo-modal.php' );

    return $template;
} );