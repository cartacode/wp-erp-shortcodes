<?php add_shortcode( 'viewemployees', function() {

		if ( current_user_can( 'administrator' ) ) {
			$employees    = \WeDevs\ERP\HRM\Models\Employee::where('status', 'active')->count();
		} else {
			global $wpdb;

			$employee_tbl = $wpdb->prefix . 'erp_hr_employees';
			$employees = \WeDevs\ERP\HRM\Models\Employee::select( array( $employee_tbl . '.user_id', 'display_name' ) )
														->leftJoin( $wpdb->users, $employee_tbl . '.user_id', '=', $wpdb->users . '.ID' );

			$tmp_employee_id_arr = array();
			foreach ( $employees->get()->toArray() as $t_employee) {
				if ( get_user_meta( $t_employee['user_id'], 'created_by', true ) == get_current_user_id() )
					array_push( $tmp_employee_id_arr, $t_employee['user_id'] );
			}

			$employees    = \WeDevs\ERP\HRM\Models\Employee::where('status', 'active')->whereIn( $employee_tbl . '.user_id', $tmp_employee_id_arr )->count();                    
		}
		$departments  = \WeDevs\ERP\HRM\Models\Department::count();
		$designations = \WeDevs\ERP\HRM\Models\Designation::count();

		$employeecount = '<div class="badge-container">
		<div class="badge-wrap badge-green">
			<div class="badge-inner">';
		
			$employeecount .= '<h1 style="text-align: center;">' . number_format_i18n( $employees, 0 ) . '</h1>';

			$employeecount .= '</div>
			</div>
		</div>';
		return $employeecount;
	} );

   /** Widgets *****************************/

	/**
	 * Birthday widget
	 *
	 * @return void
	 */
	
	function erp_hr_dashboard_widget_birthday_extended() {

	    $todays_birthday  = erp_hr_get_todays_birthday();
	    $upcoming_birtday = erp_hr_get_next_seven_days_birthday();


	    if ( $todays_birthday ) { ?>

	        <h4><?php _e( 'Today\'s Birthday', 'erp' ); ?></h4>
	        <span class="wait"><?php _e( 'please wait ...', 'erp' ); ?></span>

	        <ul class="erp-list list-inline">
	            <?php

	            foreach ( $todays_birthday as $user ) {
	                $employee = new \WeDevs\ERP\HRM\Employee( intval( $user->user_id ) );
	                ?>
	                <li>
	                    <a href="<?php echo $employee->get_details_url(); ?>" class="erp-tips" title="<?php echo $employee->get_full_name(); ?>">
	                    <?php echo $employee->get_avatar( 32 ); ?></a> &nbsp;
	                    <?php if ( !isset($_COOKIE[ $employee->get_user_id() ] ) ) : ?>
	                        <a href="#" title="Send birthday wish email to <?php echo $employee->get_full_name(); ?>"
	                            class="send-wish" data-user_id="<?php echo intval( $employee->get_user_id() ); ?>">
	                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
	                        </a>
	                    <?php endif; ?>
	                </li>
	            <?php } ?>
	        </ul>

	        <?php
	    }
	    ?>

	    <?php if ( $upcoming_birtday ) { ?>

	        <h4><?php _e( 'Upcoming Birthdays', 'erp' ); ?></h4>

	        <ul class="erp-list list-two-side list-sep">

	            <?php foreach ( $upcoming_birtday as $key => $user ): ?>

	                <?php $employee = new \WeDevs\ERP\HRM\Employee( intval( $user->user_id ) ); ?>

	                <li>
	                    <a href="<?php echo $employee->get_details_url(); ?>"><?php echo $employee->get_full_name(); ?></a>
	                    <span><?php echo erp_format_date( $user->date_of_birth, 'M, d' ); ?></span>
	                </li>

	            <?php endforeach; ?>

	        </ul>
	        <?php
	    }

	    if ( ! $todays_birthday && ! $upcoming_birtday ) {
	        return 'No one has birthdays this week!';
	    }
	    ?>
	    <style>
	        span.wait {
	            display: none;
	            float: right;
	        }
	        .erp-list .send-wish {
	            box-shadow: none;
	        }
	        .erp-list .send-wish i {
	            color: #fbc02d;
	        }
	    </style>
	    <?php
	}

	add_shortcode( 'birthday-widget-shortcode', 'erp_hr_dashboard_widget_birthday_extended' );

	//view department shortcode
	add_shortcode('crm_view_department',function(){
	   if ( current_user_can( 'administrator' ) )
	    {
	    	$departments  = \WeDevs\ERP\HRM\Models\Department::count();
	    	return '<h1>'.number_format_i18n( $departments, 0 ).'</h1>';
	    }
	    else
	    {
	        echo "No Departments found";
	    }

	});
	//view announcement shortcode
	add_shortcode('crm_latest_announcement',function(){
	    if ( current_user_can( 'administrator' ) )
	    {
	       $query = new WP_Query( array(
            'post_type'      => 'erp_hr_announcement',
            'posts_per_page' => '5',
            'order'          => 'DESC'
	        ) );
		        $announcements = $query->get_posts();
		        $html_4 =  '<ul class="erp-list erp-dashboard-announcement">';
		     $i = 0;
	        foreach ( $announcements as $key => $announcement ):
	        $html_4 .= '<li>'.'<strong>'.$announcement->post_title.'</strong>'.' | '.erp_format_date( $announcement->post_date ).'<br/>';  
	        $html_4 .= wp_trim_words( $announcement->post_content, 50 ) . '</li>';
	        $i++;
	        endforeach;
	        echo '</ul>';
	        return $html_4;
	    }
	    else
	    {
	        echo "No Announcements found";
	    }

	});
	//view who is out this month next month
	add_shortcode('crm_who_is_out',function(){
		if ( current_user_can( 'administrator' ) )
	    {
	    	$leave_requests           = erp_hr_get_current_month_leave_list();
    		$leave_requests_nextmonth = erp_hr_get_next_month_leave_list();
    		if ( $leave_requests ) {
	    		$html_2 = '<h5>This Month</h5>';
	    		$html_2 .= "<div class='erp-list list-two-side list-sep'>";
	    		foreach ( $leave_requests as $key => $leave ):	
	    			$employee = new \WeDevs\ERP\HRM\Employee( intval( $leave->user_id ) );
	    			 
	    			 $html_2 .= '<div>
	                    <a href="'.$employee->get_details_url().'">'.$employee->get_full_name().'</a>
	                    <span><i class="fa fa-calendar"></i>"'.erp_format_date( $leave->start_date, 'M d' ) . ' - '. erp_format_date( $leave->end_date, 'M d' ).'"</span>
	                </div>';
	    		endforeach;
	    		$html_2 .= "</div>";
    		}
    		if ( $leave_requests_nextmonth ) { 
	    		$html_2 .= '<h5>Next Month</h5>';
	    		$html_2 .= '<div class="erp-list list-two-side list-sep">';
	            foreach ( $leave_requests_nextmonth as $key => $leave ): 
	            	$employee = new \WeDevs\ERP\HRM\Employee( intval( $leave->user_id ) );
	                
	                $html_2 .= '<div>
	                    <a href="'.$employee->get_details_url().'">"'.$employee->get_full_name().'"</a>
	                    <span><i class="fa fa-calendar"></i>"'.erp_format_date( $leave->start_date, 'M d' ) . ' - '. erp_format_date( $leave->end_date, 'M d' ).'"</span>
	                </div>';
	            endforeach;
	       		$html_2 .= "</div>";
       		}
        	return $html_2;
	    }
	    else
	    {
	    	echo "No Record Found";
	    }
	});
	//shortcode to show calendar
	add_shortcode('crm_calendar_view',function(){
	if ( current_user_can( 'administrator' ) )
    {
		wp_enqueue_script( 'erp-js' );
	    wp_enqueue_style( 'erp-fullcalendar' );
	    
	    $user_id        = get_current_user_id();
	    $leave_requests = erp_hr_get_calendar_leave_events( false, $user_id, false );
	    $holidays       = erp_array_to_object( \WeDevs\ERP\HRM\Models\Leave_Holiday::all()->toArray() );
	    $events         = [];
	    $holiday_events = [];
	    $event_data     = [];
	    foreach ( $leave_requests as $key => $leave_request ) {
	        //if status pending
	        $policy = erp_hr_leave_get_policy( $leave_request->policy_id );
	        $event_label = $policy->name;
	        if ( 2 == $leave_request->status ) {
	            $policy = erp_hr_leave_get_policy( $leave_request->policy_id );
	            $event_label .= sprintf( ' ( %s ) ', __( 'Pending', 'erp' ) );
	        }
	        $events[] = array(
	            'id'        => $leave_request->id,
	            'title'     => $event_label,
	            'start'     => $leave_request->start_date,
	            'end'       => $leave_request->end_date,
	            'url'       => erp_hr_url_single_employee( $leave_request->user_id, 'leave' ),
	            'color'     => $leave_request->color,
	        );
	    }

	    foreach ( $holidays as $key => $holiday ) {
	        $holiday_events[] = [
	            'id'        => $holiday->id,
	            'title'     => $holiday->title,
	            'start'     => $holiday->start,
	            'end'       => $holiday->end,
	            'color'     => '#FF5354',
	            'img'       => '',
	            'holiday'   => true
	        ];
	    }
		    $schedules_data = array_merge( $events, $holiday_events );

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
		                eventRender: function(event, element, calEvent) {
		                if ( event.holiday ) {
		                    element.find('.fc-content').find('.fc-title').css({ 'top':'0px', 'left' : '3px', 'fontSize' : '13px', 'padding':'2px' });
		                };
		            	},
		            });
		        });
		    </script>";

	    return $template;
	}
	else
	{
		echo 'No Calendar Found';
	}
		});
		
	?>