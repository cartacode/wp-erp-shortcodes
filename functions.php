<?php
// check request is from admin panel
if ( !function_exists( 'is_admin_request' ) ) {
    function is_admin_request() {
        if ( isset( $_SERVER['HTTP_REFERER'] ) )
            return ( strpos( $_SERVER['HTTP_REFERER'], 'admin.php' ) !== false ) || ( strpos( $_SERVER['HTTP_REFERER'], '/wp-admin' ) !== false );
        return false;
    }
}

// check current wp erp user is broker or staff
if ( !function_exists( 'current_wp_erp_user_is' ) ) {
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
}

// localize scripts for loading wp erp assets
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

// default cotnact acitivity localize for wp erp assets settings
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

// update user profile
if ( !function_exists( 'update_user_profile' ) ) {
    function update_user_profile( $user_data, $avatar = null ) {
        $user_id = wp_update_user( array( 
            'ID'            => get_current_user_id(), 
            'first_name'    => $user_data['first_name'],
            'last_name'     => $user_data['last_name'],
            'display_name'  => $user_data['first_name'] . ' ' . $user_data['last_name'],
            'user_email'    => $user_data['user_email'],
            'user_pass'     => $user_data['password']
        ) );

        if ( is_wp_error( $user_id ) )
            return false;

        $wordpress_upload_dir = wp_upload_dir();

        $new_file_path = $wordpress_upload_dir['path'] . '/' . $avatar['name'];
        $new_file_mime = mime_content_type( $avatar['tmp_name'] );
         
        if( empty( $avatar ) ) {
            error_log( 'File is not selected.' );
        } elseif( $avatar['error'] ) {
            error_log( $avatar['error'] );
        } elseif( $avatar['size'] > wp_max_upload_size() ) {
            error_log( 'It is too large than expected.' );
        } elseif( !in_array( $new_file_mime, get_allowed_mime_types() ) ) {
            error_log( 'WordPress doesn\'t allow this type of uploads.' );
        } else {
            while( file_exists( $new_file_path ) ) {
                $i++;
                $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $avatar['name'];
            }
         
            // looks like everything is OK
            if( move_uploaded_file( $avatar['tmp_name'], $new_file_path ) ) {            
             
                $upload_id = wp_insert_attachment( array(
                    'guid'           => $new_file_path, 
                    'post_mime_type' => $new_file_mime,
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $avatar['name'] ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ), $new_file_path );
             
                // wp_generate_attachment_metadata() won't work if you do not include this file
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
             
                // Generate and save the attachment metas into the database
                wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );


                $employee = new WeDevs\ERP\HRM\Employee( $user_id );
                $data = $employee->to_array();
                $data['personal']['photo_id'] = $upload_id;
                $employee->create_employee( $data );
            }
        }

        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id );

        wp_logout();
        wp_redirect( home_url( '/home/login' ) );
        exit;
    }
}

// get agent user's brokerage_office
if ( !function_exists( 'get_brokerage_office_by_agent_user_id' ) ) {
    function get_brokerage_office_by_agent_user_id( $user_id = null ) {
        if ( empty( $user_id ) || !intval( $user_id ) ) return '';

        $brokerage_office = get_user_meta( $user_id, 'brokerage_office', true );

        if ( $brokerage_office ) return $brokerage_office;

        $owner_id = get_user_meta( $user_id, 'created_by', true );

        if ( !user_can( $owner_id, 'administrator' ) ) {
            $depth = 15;

            while($depth > 0) {
                $o_owner_id = get_user_meta( $owner_id, 'created_by', true );

                if ( !empty( $o_owner_id) ) {
                    if ( user_can( $o_owner_id, 'administrator' ) ) {
                        break;
                    } else {
                        $owner_id = $o_owner_id;
                    }
                } 
                
                $depth --;
            }
        }

        $owner = get_user_by( 'id', $owner_id );
        $owner_name = $owner ? $owner->user_firstname . ' ' . $owner->user_lastname : 'unknown';

        if ( array_key_exists( $owner_name, BROKERAGE_OFFICES ) ) {
            $brokerage_office = BROKERAGE_OFFICES[$owner_name];
            update_user_meta( $user_id, 'brokerage_office', $brokerage_office );

            return $brokerage_office;
        }

        return '';
    }
}