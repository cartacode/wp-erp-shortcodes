<?php

// recently added contacts or companies
add_shortcode( 'crm_dashboard_recently_added', function() {
    $contacts  = erp_get_peoples( [ 'type' => 'contact', 'orderby' => 'created', 'order' => 'DESC', 'number' => 5 ] );
    $companies = erp_get_peoples( [ 'type' => 'company', 'orderby' => 'created', 'order' => 'DESC', 'number' => 5 ] );

    $crm_life_stages = erp_crm_get_life_stages_dropdown_raw();

    $template = '<h4>Contacts</h4>';

    if ( $contacts ) {
    	$template .= '<ul class="erp-list erp-latest-contact-list">';
		
		foreach ( $contacts as $contact ) : 
            
            $contact_obj = new WeDevs\ERP\CRM\Contact( (int)$contact->id );
            $life_stage = $contact_obj->get_life_stage();

            $template .= '<li><div class="avatar">' . $contact_obj->get_avatar(28) . '</div>';
            $template .= '<div class="details"><p class="contact-name"><a href="' . $contact_obj->get_details_url() . '">' . $contact_obj->get_full_name() . '</a></p><p class="contact-stage">' . (isset( $crm_life_stages[ $life_stage ] ) ? $crm_life_stages[ $life_stage ] : '') . '</p>
                    </div>';

            $template .= '<span class="contact-created-time erp-tips" title="' . sprintf( '%s %s', __( 'Created on', 'erp' ), erp_format_date( $contact->created ) ) . '"><i class="fa fa-clock-o"></i></span></li>';

		endforeach;

    	$template .= '</ul>';
    } else {
    	$template .= 'No contacts found';
    }

    $template .= '<hr/>';

    $template .= '<h4>Companies</h4>';

    if ( $companies ) {
        $template .= '<ul class="erp-list erp-latest-contact-list">';

        foreach ( $companies as $company ) :
            $company_obj = new WeDevs\ERP\CRM\Contact( intval( $company->id ) );
            $life_stage = $company_obj->get_life_stage();
                
            $template .= '<li><div class="avatar">' . $company_obj->get_avatar(28) . '</div>';

            $template .= '<div class="details"><p class="contact-name"><a href="' . $company_obj->get_details_url() . '">' . $company_obj->get_full_name() . '</a></p><p class="contact-stage">' . (isset( $crm_life_stages[ $life_stage ] ) ? $crm_life_stages[ $life_stage ] : '') . '</p></div>';

            $template .= '<span class="contact-created-time erp-tips" title="' . sprintf( '%s %s', __( 'Created on', 'erp' ), erp_format_date( $company->created ) ) . '"><i class="fa fa-clock-o"></i></span></li>';

        endforeach;
        
        $template .= '</ul>';
    } else {
    	$template .= 'No companies found';    	
    }

    return $template;
} );

// return total inbound emails
add_shortcode( 'crm_dashboard_total_inbound_emails', function() {
    $total_emails_count = get_option( 'wp_erp_inbound_email_count', 0 );

    return '<h1 style="text-align: center;">' . $total_emails_count . '</h1>';
} );

// todays' schedules
add_shortcode( 'crm_todays_schedules', function() {
	$todays_schedules = erp_crm_get_todays_schedules_activity( get_current_user_id() );
   
   	$template = '';

    if ( $todays_schedules ):

	    $template .= '<ul class="erp-list list-two-side list-sep erp-crm-dashbaord-todays-schedules">';

        foreach ( $todays_schedules as $key => $schedule ) :
            $template .= '<li>';
                
            $users_text   = '';
            $invite_users = isset( $schedule['extra']['invite_contact'] ) ? $schedule['extra']['invite_contact'] : [];

            if ( in_array( 'contact', $schedule['contact']['types'] ) ) {
                $contact_user = $schedule['contact']['first_name'] . ' ' . $schedule['contact']['last_name'];
            } else if( in_array( 'company', $schedule['contact']['types'] ) )  {
                $contact_user = $schedule['contact']['company'];
            }

            array_walk( $invite_users, function( &$val ) {
                $val = get_the_author_meta( 'display_name', $val );
            });

            if ( count( $invite_users) == 1 ) {
                $users_text = sprintf( '%s <span>%s</span>', __( 'and', 'erp' ), reset( $invite_users ) );
            } else if ( count( $invite_users) > 1 ) {
                $users_text = sprintf( '%s <span class="erp-tips" title="%s">%d %s</span>', __( 'and', 'erp' ), implode( '<br>', $invite_users ), count( $invite_users ), __( 'Others') );
            }


            switch ( $schedule['log_type'] ) {
                case 'meeting':
                    $icon = 'calendar';
                    $text = __( 'Meeting with', 'erp' );
                    $data_title = __( 'Log Activity - Meeting', 'erp' );
                    break;

                case 'call':
                    $icon = 'phone';
                    $text = __( 'Call', 'erp' );
                    $data_title = __( 'Log Activity - Call', 'erp' );
                    break;

                case 'email':
                    $icon = 'envelope-o';
                    $text = __( 'Send email to', 'erp' );
                    $data_title = __( 'Log Activity - Email', 'erp' );
                    break;

                case 'sms':
                    $icon = 'comment-o';
                    $text = __( 'Send sms to', 'erp' );
                    $data_title = __( 'Log Activity - SMS', 'erp' );
                    break;

                default:
                    $icon = '';
                    $text = '';
                    $data_title = '';
                    break;
            }


            $template .= sprintf(
                '<i class="fa fa-%s"></i> %s <a href="%s">%s</a> %s %s %s',
                $icon,
                $text,
                erp_crm_get_details_url( $schedule['contact']['id'], $schedule['contact']['types'] ),
                $contact_user,
                $users_text,
                __( 'at', 'erp' ),
                date( 'g:ia', strtotime( $schedule['start_date'] ) )
            );

            do_action( 'erp_crm_dashboard_widget_todays_schedules', $schedule );

            $data_title = apply_filters( 'erp_crm_dashboard_widget_todays_schedules_title', $data_title, $schedule );

       
        	$template .= ' | <a href="#" data-schedule_id="' . $schedule['id'] .'" data-title="' . $data_title . '" class="erp-crm-dashbaord-show-details-schedule">Details</a></li>';
        
        endforeach;
	    
	    $template .= '</ul>';
     
     else :

        $template .= 'No schedules found';

    endif;

    return $template;
} );

add_shortcode( 'crm_upcoming_schedules', function() {
	if ( function_exists( 'erp_crm_dashboard_widget_upcoming_schedules' ) ) {
		erp_crm_dashboard_widget_upcoming_schedules();
	}
} );