<?php
add_shortcode( 'circle_list', function() {
	if( ! is_admin() ){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	   	require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}

	if(!isset($_REQUEST['paged'])) {
		$_REQUEST['paged'] = explode('/page/', $_SERVER['REQUEST_URI'], 2);
		if(isset($_REQUEST['paged'][1])) list($_REQUEST['paged'],) = explode('/', $_REQUEST['paged'][1], 2);
		if(isset($_REQUEST['paged']) and $_REQUEST['paged'] != '') {
			$_REQUEST['paged'] = intval($_REQUEST['paged']);
			if($_REQUEST['paged'] < 2) $_REQUEST['paged'] = '';
		} else {
			$_REQUEST['paged'] = '';
		}
	}
	
	$localize_script = get_default_localize_script();

    $contact_actvity_localize = get_default_contact_actvity_localize();

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
        $template .= '<a href="#" id="erp-new-contact-group" class="erp-new-contact-group add-new-h2" title="Add New Circle">Add New Circle</a>';

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
	                $customer_table->search_box( __( 'Search Circles', 'erp' ), 'erp-crm-contact-group-search' );
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

	if(!isset($_REQUEST['paged'])) {
		$_REQUEST['paged'] = explode('/page/', $_SERVER['REQUEST_URI'], 2);
		if(isset($_REQUEST['paged'][1])) list($_REQUEST['paged'],) = explode('/', $_REQUEST['paged'][1], 2);
		if(isset($_REQUEST['paged']) and $_REQUEST['paged'] != '') {
			$_REQUEST['paged'] = intval($_REQUEST['paged']);
			if($_REQUEST['paged'] < 2) $_REQUEST['paged'] = '';
		} else {
			$_REQUEST['paged'] = '';
		}
	}
	
	$localize_script = get_default_localize_script();

    $contact_actvity_localize = get_default_contact_actvity_localize();

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