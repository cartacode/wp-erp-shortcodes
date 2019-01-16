<?php
add_shortcode( 'circle_list', function() {
	if( ! is_admin() ){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}
	
	$localize_script = apply_filters( 'erp_crm_localize_script', array(
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
		erp_get_js_template( WPERP_MODULES . '/crm/views/js-templates/new-contact-group.php', 'erp-crm-new-contact-group' );
	}

	wp_enqueue_script( 'common' );

	wp_enqueue_script( 'erp-crm' );
	wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
	
	wp_enqueue_script( 'erp-crm-contact' );
	wp_localize_script( 'erp-crm-contact', 'wpErpCrm', $localize_script );

    wp_enqueue_style( 'erp-shortcode-styles' );

	$template = '';

	$template .= '<div class="wrap erp-crm-contact-group" id="wp-erp">';

	$template .= '<h2>';

    if ( current_user_can( 'erp_crm_create_groups' ) )
        $template .= '<a href="#" id="erp-new-contact-group" class="erp-new-contact-group add-new-h2" title="Add New Contact Group">Add New Contact Group</a>';

    // $template .= '<a href="' . add_query_arg( [ 'page'=>'erp-crm', 'section' => 'contact-groups', 'groupaction' => 'view-subscriber' ], home_url('dashboard/circles') ) . '" class="add-new-h2" title="View all subscriber contact">View all subscriber</a>';
    
    $template .= '</h2>';

    $template .= '
	    <div class="list-table-wrap erp-crm-contact-group-list-table-wrap">
	        <div class="list-table-inner erp-crm-contact-group-list-table-inner">

	            <form method="get">
	                <input type="hidden" name="page" value="erp-crm">
	                <input type="hidden" name="section" value="contact-groups">';

					ob_start();
					
	                $customer_table = new \WeDevs\ERP\CRM\Contact_Group_List_Table();
	                $customer_table->prepare_items();
	                $customer_table->search_box( __( 'Search Contact Group', 'erp' ), 'erp-crm-contact-group-search' );
	                $customer_table->views();

	                $customer_table->display();
	                
	                $template .= ob_get_contents(); 

					ob_end_clean();



    $template .= '
				</form>

        	</div><!-- .list-table-inner -->
    	</div><!-- .list-table-wrap -->
	</div>';

	$template .= file_get_contents( dirname( __FILE__ ) . '/demo-modal.php' );

	return $template;

} );


// view all subscribers in single circle
add_shortcode( 'subscriber_list', function() {
	if( ! is_admin() ){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}
	
	$localize_script = apply_filters( 'erp_crm_localize_script', array(
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
        erp_get_js_template( WPERP_MODULES . '/crm/views/js-templates/new-subscriber-contact.php', 'erp-crm-assign-subscriber-contact' );
    }

	wp_enqueue_script( 'common' );

	wp_enqueue_script( 'erp-crm' );
	wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
	
	wp_enqueue_script( 'erp-crm-contact' );
	wp_localize_script( 'erp-crm-contact', 'wpErpCrm', $localize_script );

	wp_enqueue_style( 'erp-select2' );
    wp_enqueue_style( 'erp-shortcode-styles' );

	$template = '';

	$template .= '<div class="wrap erp-crm-subscriber-contact" id="wp-erp">';

	ob_start();
?>
	<div class="wrap erp-crm-subscriber-contact" id="wp-erp">

	    <h2><?php _e( 'Subscribed Contacts', 'erp' ); ?>
	        <a href="#" id="erp-new-subscriber-contact" class="erp-new-subscriber-contact add-new-h2" title="<?php _e( 'Assign a Contact', 'erp' ); ?>"><?php _e( 'Assign a Contact', 'erp' ); ?></a>
	        <a href="<?php _e( home_url( '/crmdashboard/circles/' ) ) ?>" class="add-new-h2" title="<?php _e( 'Back to Contact Group', 'erp' ); ?>"><?php _e( 'Back to Contact Group', 'erp' ); ?></a>
	    </h2>

	    <div class="list-table-wrap erp-crm-subscriber-contact-list-table-wrap">
	        <div class="list-table-inner erp-crm-subscriber-contact-list-table-inner">

	            <form method="get">
	                <input type="hidden" name="page" value="erp-crm">
	                <input type="hidden" name="section" value="contact-groups">
	                <input type="hidden" name="groupaction" value="view-subscriber">
	                <?php
	                $customer_table = new \WeDevs\ERP\CRM\Contact_Subscriber_List_Table();
	                $customer_table->prepare_items();
	                // $customer_table->search_box( __( 'Search Contact Group', 'erp' ), 'erp-crm-contact-group-search' );
	                $customer_table->views();

	                $customer_table->display();
	                ?>
	            </form>

	        </div><!-- .list-table-inner -->
	    </div><!-- .list-table-wrap -->
	</div>
<?php	                
    $template .= ob_get_contents(); 

	ob_end_clean();

	$template .= '</div>';

	$template .= file_get_contents( dirname( __FILE__ ) . '/demo-modal.php' );
	
	return $template;
} );