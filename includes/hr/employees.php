<?php
add_shortcode( 'employees-list-table', function() {
   if( ! is_admin() ){
       require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
       require_once( ABSPATH . 'wp-admin/includes/screen.php' );
       require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
       require_once( ABSPATH . 'wp-admin/includes/template.php' );
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

    $contact_actvity_localize = apply_filters( 'erp_crm_contact_localize_var', [
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

	wp_localize_script( 'erp-vue-table', 'wpVueTable', [
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'wp-erp-vue-table' )
	] );

	if( function_exists( 'erp_get_js_template' ) ) {
		erp_get_js_template( WPERP_MODULES . '/hrm/views/js-templates/new-employee.php', 'erp-hrm-new-employee' );
	}
wp_enqueue_style( 'erp-sweetalert' );
wp_enqueue_script( 'erp-sweetalert' );
	wp_enqueue_script( 'erp-crm' );
	wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
	
	wp_enqueue_script( 'erp-crm-contact' );
	wp_localize_script( 'erp-crm-contact', 'wpErpCrm', $localize_script );

	wp_enqueue_style( 'erp-tiptip' );
	wp_enqueue_style( 'erp-select2' );
	wp_enqueue_style( 'table-view' );

	//wp_enqueue_style( 'circles' );

	$template = '';

	$template .= '<div class="wrap erp-crm-contact-group" id="wp-erp">';

	$template .= '<h2>';

    if ( current_user_can( 'erp_create_employee' ) ) 
            
             $template .= '<a href="#" @click.prevent="addEmployee( \'employee\', \'Add New Employee\' )" id="erp-employee-new" class="erp-employee-new add-new-h2">Add New Employee</a>';
           
 

    // $template .= '<a href="' . add_query_arg( [ 'page'=>'erp-crm', 'section' => 'contact-groups', 'groupaction' => 'view-subscriber' ], home_url('dashboard/circles') ) . '" class="add-new-h2" title="View all subscriber contact">View all subscriber</a>';
    
    $template .= '</h2>';

    $template .= '<div class="list-table-wrap erp-hr-employees-wrap">
        <div class="list-table-inner erp-hr-employees-wrap-inner">

            <form method="get">
                <input type="hidden" name="page" value="erp-hr">
                <input type="hidden" name="section" value="employee">'.
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