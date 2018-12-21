<?php
add_shortcode( 'contacts-list-table', function() {
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

	$crm = \WeDevs\ERP\CRM\Customer_Relationship::init();
	$crm->load_contact_company_scripts( '', $contact_actvity_localize );

	$customer = new \WeDevs\ERP\CRM\Contact( null, 'contact' );
	$localize_script['customer_empty']    = $customer->to_array();
	$localize_script['statuses']          = erp_crm_customer_get_status_count( 'contact' );
	$localize_script['contact_type']      = 'contact';
	$localize_script['life_stages']       = erp_crm_get_life_stages_dropdown_raw();
	$localize_script['searchFields']      = erp_crm_get_serach_key( 'contact' );
	$localize_script['saveAdvanceSearch'] = erp_crm_get_save_search_item( [ 'type' => 'contact' ] );
	$localize_script['isAdmin']           = current_user_can( 'manage_options' );
	$localize_script['isCrmManager']      = current_user_can( 'erp_crm_manager' );
	$localize_script['isAgent']           = current_user_can( 'erp_crm_agent' );

	$country = \WeDevs\ERP\Countries::instance();
	wp_localize_script( 'erp-script', 'wpErpCountries', $country->load_country_states() );
	

	wp_localize_script( 'erp-vue-table', 'wpVueTable', [
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'wp-erp-vue-table' )
	] );
	wp_enqueue_script( 'erp-crm-contact' );
	wp_localize_script( 'erp-crm-contact', 'wpErpCrm', $localize_script );
	wp_enqueue_style( 'erp-tiptip' );
	wp_enqueue_style( 'erp-select2' );
	wp_enqueue_style( 'table-view' );
	
	$custom_data = [
		'searchFields' => array_keys( erp_crm_get_serach_key( 'contact' ) )
	];

	$template = '';

	$template .= '<div class="wrap erp-crm-customer erp-crm-customer-listing" id="wp-erp" v-cloak>';

	$template .= '<advance-search :show-hide-segment="showHideSegment"></advance-search>';

	$template .= '
		<vtable v-ref:vtable
			wrapper-class="erp-crm-list-table-wrap"
			table-class="customers"
			row-checkbox-id="erp-crm-customer-id-checkbox"
			row-checkbox-name="customer_id"
			action="erp-crm-get-contacts"
			:wpnonce="wpnonce"
			page = "' . add_query_arg( array('type' => 'contact'), home_url( '/dashboard/contacts' ) ). '"
			per-page="20"
			:fields=fields
			:item-row-actions=itemRowActions
			:search="search"
			:top-nav-filter="topNavFilter"
			:bulkactions="bulkactions"
			:extra-bulk-action="extraBulkAction"
			:additional-params="additionalParams"
			:remove-url-params="removeUrlParams"
			:custom-data = \'' . json_encode( $custom_data, JSON_UNESCAPED_UNICODE ) . '\'
		></vtable>';

	$template .= '</div>';

	return $template;
} );