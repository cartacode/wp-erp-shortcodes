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
	        _e( 'No one has birthdays this week!', 'erp' );
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