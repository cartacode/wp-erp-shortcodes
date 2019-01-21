<?php

// recently added contacts or companies
add_shortcode( 'crm_dashboard_recently_added', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

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
            $template .= '<div class="details"><p class="contact-name"><a href="' . home_url( '/crmdashboard/contacts/view-contact/?action=view&id=' . (int)$contact->id ) . '">' . $contact_obj->get_full_name() . '</a></p><p class="contact-stage">' . (isset( $crm_life_stages[ $life_stage ] ) ? $crm_life_stages[ $life_stage ] : '') . '</p>
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

            $template .= '<div class="details"><p class="contact-name"><a href="' . home_url( '/crmdashboard/companies/view-company/?action=view&id=' . (int)$company->id ) . '">' . $company_obj->get_full_name() . '</a></p><p class="contact-stage">' . (isset( $crm_life_stages[ $life_stage ] ) ? $crm_life_stages[ $life_stage ] : '') . '</p></div>';

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

    return '<h1 style="text-align: center; font-size: 50px;">' . $total_emails_count . '</h1>';
} );

// todays' schedules
add_shortcode( 'crm_todays_schedules', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

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

// upcoming schedules
add_shortcode( 'crm_upcoming_schedules', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

	$upcoming_schedules = erp_crm_get_next_seven_day_schedules_activities( get_current_user_id() );

	$template = '';

    if ( $upcoming_schedules ): 
        $template .= '<ul class="erp-list list-two-side list-sep erp-crm-dashbaord-upcoming-schedules">';

        foreach ( $upcoming_schedules as $key => $schedule ) : 
            $template .= '<li>';

                $users_text   = '';
                $invite_users = isset( $schedule['extra']['invite_contact'] ) ? $schedule['extra']['invite_contact'] : [];
                $contact_user = $schedule['contact']['first_name'] . ' ' . $schedule['contact']['last_name'];

                array_walk( $invite_users, function( &$val ) {
                    $val = get_the_author_meta( 'display_name', $val );
                });

                if ( count( $invite_users) == 1 ) {
                    $users_text = sprintf( '%s <span>%s</span>', __( 'and', 'erp' ), reset( $invite_users ) );
                } else if ( count( $invite_users) > 1 ) {
                    $users_text = sprintf( '%s <span class="erp-tips" title="%s">%d %s</span>', __( 'and', 'erp' ), implode( '<br>', $invite_users ), count( $invite_users ), __( 'Others') );
                }

                if ( $schedule['log_type'] == 'meeting' ) {
                    $template .= sprintf( '%s <a href="%s">%s</a> %s %s %s %s %s', __( '<i class="fa fa-calendar"></i> Meeting with', 'erp' ), erp_crm_get_details_url( $schedule['contact']['id'], $schedule['contact']['types'] ), $contact_user, $users_text, __( 'on', 'erp' ), erp_format_date( $schedule['start_date'] ), __( 'at', 'erp' ), date( 'g:ia', strtotime( $schedule['start_date'] ) ) ) . " <a href='#' data-schedule_id=' " . $schedule['id'] . " ' data-title='" . $schedule['extra']['schedule_title'] . "' class='erp-crm-dashbaord-show-details-schedule'>" . __( 'Details &rarr;', 'erp' ) . "</a>";
                }

                if ( $schedule['log_type'] == 'call' ) {
                    $template .= sprintf( '%s <a href="%s">%s</a> %s %s %s %s %s', __( '<i class="fa fa-phone"></i> Call to', 'erp' ), erp_crm_get_details_url( $schedule['contact']['id'], $schedule['contact']['types'] ), $contact_user, $users_text, __( 'on', 'erp' ), erp_format_date( $schedule['start_date'] ), __( 'at', 'erp' ), date( 'g:ia', strtotime( $schedule['start_date'] ) ) ) . " <a href='#' data-schedule_id=' " . $schedule['id'] . " ' data-title='" . $schedule['extra']['schedule_title'] . "' class='erp-crm-dashbaord-show-details-schedule'>" . __( 'Details &rarr;', 'erp' ) . "</a>";
                }
                
            $template .= '</li>';
            
        endforeach; 
        
        $template .= '</ul>';

    else : 
        
        $template .= 'No schedules found';

    endif;

    return $template;
} );

// show all contacts
add_shortcode( 'view_all_contacts', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

    $contacts_count  = erp_crm_customer_get_status_count( 'contact' );
    $companies_count = erp_crm_customer_get_status_count( 'company' );

    $template = '';
    $template .= '<div class="erp-info-box-item">
        <div class="erp-info-box-item-inner">
            <div class="erp-info-box-content">
                <div class="erp-info-box-content-row">
                    <div class="erp-info-box-content-left">';

                        $template .= '<h3>' . number_format_i18n( $contacts_count['all']['count'], 0 ) . '</h3>';
                        $template .= '<p>' . sprintf( _n( 'Contact', 'Contacts', $contacts_count['all']['count'], 'erp' ), number_format_i18n( $companies_count['all']['count'] ), 0 ) . '</p>';
                    $template .= '</div>';

                    $template .= '<div class="erp-info-box-content-right">
                        <ul class="erp-info-box-list">';

                            foreach ( $contacts_count as $contact_key => $contact_value ) {
                                if ( $contact_key == 'all' || $contact_key == 'trash' ) {
                                    continue;
                                }

                                $template .= '<li>
                                    <a href="' . home_url( '/crmdashboard/contacts?page=crmdashboard&status=' . $contact_key ) . '">
                                        <i class="fa fa-square" aria-hidden="true"></i>&nbsp;';

                                            $singular = $contact_value['label'];
                                            $plural = erp_pluralize( $singular );

                                            $plural = apply_filters( "erp_crm_life_stage_plural_of_{$contact_key}", $plural, $singular );

                                            $template .= sprintf( _n( "%s {$singular}", "%s {$plural}", $contact_value['count'], 'erp' ), number_format_i18n( $contact_value['count'] ), 0 );
                                    $template .= '</a>
                                </li>';
                            }
                        $template .= '</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    return $template;
} );

// show all companies created
add_shortcode( 'view_all_companies', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

    $companies_count = erp_crm_customer_get_status_count( 'company' );
    
    $template = '';

    $template .= '<div class="erp-info-box-item">
            <div class="erp-info-box-item-inner">
                <div class="erp-info-box-content">
                    <div class="erp-info-box-content-row">
                        <div class="erp-info-box-content-left">
                            <h3>' . number_format_i18n( $companies_count['all']['count'], 0 ) . '</h3>';
                        
                        $template .= '<p>' . sprintf( _n( 'Company', 'Companies', $companies_count['all']['count'], 'erp' ), number_format_i18n( $companies_count['all']['count'] ), 0 ) . '</p>';

                    $template .= '</div>
                    <div class="erp-info-box-content-right">
                        <ul class="erp-info-box-list">';

                            foreach ( $companies_count as $company_key => $company_value ) {
                                if ( $company_key == 'all' || $company_key == 'trash' ) {
                                    continue;
                                }
                                $template .= '<li>
                                    <a href="' . home_url( '/crmdashboard/companies?page=crmdashboard&status=' . $company_key ) . '">
                                        <i class="fa fa-square" aria-hidden="true"></i>&nbsp;';
                                        

                                            $singular = $company_value['label'];
                                            $plural = erp_pluralize( $singular );

                                            $plural = apply_filters( "erp_crm_life_stage_plural_of_{$company_key}", $plural, $singular );

                                            $template .= sprintf( _n( "%s {$singular}", "%s {$plural}", $company_value['count'], 'erp' ), number_format_i18n( $company_value['count'] ), 0 );
                                $template .= '</a>
                                </li>';
                            }
                        $template .= '</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    return $template;
} );

// show my schedules in the dashbaord
add_shortcode( 'show_my_schedules', function() {
    wp_enqueue_script( 'erp-js' );
    wp_enqueue_style( 'erp-fullcalendar' );
    wp_enqueue_style( 'erp-shortcode-styles' );
    
    $user_id        = is_user_logged_in() ? get_current_user_id() : -1;
    $args           = [
        'created_by' => $user_id,
        'number'     => -1,
        'type'       => 'log_activity'
    ];

    $schedules      = erp_crm_get_feed_activity( $args );
    $schedules_data = erp_crm_prepare_calendar_schedule_data( $schedules );

    $template = '';

    $template .= '<style>
        .fc-time {
            display:none;
        }
        .fc-title {
            cursor: pointer;
        }
        .fc-day-grid-event .fc-content {
            white-space: normal;
        }
    </style>

    <div id="erp-crm-calendar"></div>';

    $template .= "
    <script>
        ;jQuery(document).ready(function($) {
            $('#erp-crm-calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false,
                eventLimit: true,
                events: " . json_encode( $schedules_data ) . ",
            });
        });
    </script>";

    return $template;
} );