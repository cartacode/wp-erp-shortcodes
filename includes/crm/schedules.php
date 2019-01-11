<?php
add_shortcode( 'schedule-calendar', function() {
	$tab = 'own';
	
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

	if ( function_exists( 'erp_get_js_template' ) ) {
		erp_get_js_template( WPERP_MODULES . '/crm/views/js-templates/single-schedule-details.php', 'erp-crm-single-schedule-details' );
		erp_get_js_template( WPERP_MODULES . '/crm/views/js-templates/customer-add-schedules.php', 'erp-crm-customer-schedules');
	}

	wp_enqueue_script( 'erp-crm' );
	wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
	
	wp_enqueue_style( 'erp-tiptip' );
	wp_enqueue_script( 'erp-tiptip' );
	wp_enqueue_style( 'erp-fullcalendar' );
	wp_enqueue_script( 'erp-fullcalendar' );
	wp_enqueue_style( 'erp-select2' );
	wp_enqueue_style( 'erp-timepicker' );
	wp_enqueue_script( 'erp-timepicker' );
    wp_enqueue_script( 'underscore' );
    wp_enqueue_script( 'erp-trix-editor' );
    wp_enqueue_style( 'erp-trix-editor' );

	wp_enqueue_style( 'erp-shortcode-styles' );

	ob_start();

	render_schedule_fullcalendar( $tab );

	$template .= ob_get_contents();

	ob_end_clean();

	$template .= file_get_contents( dirname( __FILE__ ) . '/demo-modal.php' );

	return $template;
} );

function render_schedule_fullcalendar( $tab ) {
	$schedules_data = erp_crm_get_schedule_data( $tab );
?>
<div class="wrap erp erp-crm-schedules" id="wp-erp">
	<div class="erp-crm-schedule-wrapper">
		<div id="erp-crm-schedule-calendar-<?php esc_attr( $tab ); ?>"></div>
	</div>
</div>

<script>
	;jQuery(document).ready(function($) {
		window.wrap = function() {};

		$('#erp-crm-schedule-calendar-<?php esc_attr( $tab ); ?>').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			editable: false,
			eventLimit: true,
			events: <?php echo json_encode( $schedules_data ); ?>,
			eventClick: function(calEvent, jsEvent, view) {
				var scheduleId = calEvent.schedule.id;
				var title      = ( calEvent.schedule.extra.schedule_title ) ? calEvent.schedule.extra.schedule_title : '<?php _e( 'Log Details', 'erp' ) ?>';

				if ( 'tasks' === calEvent.schedule.type ) {
					title = calEvent.schedule.extra.task_title
				}

				$.erpPopup({
					title: title,
					button: '',
					id: 'erp-customer-edit',
					onReady: function() {
						var modal = this;

						$( 'header', modal).after( $('<div class="loader"></div>').show() );

						wp.ajax.send( 'erp-crm-get-single-schedule-details', {
							data: {
								id: scheduleId,
								_wpnonce: '<?php echo wp_create_nonce( 'wp-erp-crm-nonce' ); ?>'
							},

							success: function( response ) {
								var startDate = wperp.dateFormat( response.start_date, 'j F' ),
									startTime = wperp.timeFormat( response.start_date ),
									endDate = wperp.dateFormat( response.end_date, 'j F' ),
									endTime = wperp.timeFormat( response.end_date );

								if ( ! response.end_date ) {
									var datetime = startDate + ' at ' + startTime;
								} else {
									if ( response.extra.all_day == 'true' ) {
										if ( wperp.dateFormat( response.start_date, 'Y-m-d' ) == wperp.dateFormat( response.end_date, 'Y-m-d' ) ) {
											var datetime = startDate;
										} else {
											var datetime = startDate + ' to ' + endDate;
										}
									} else {
										if ( wperp.dateFormat( response.start_date, 'Y-m-d' ) == wperp.dateFormat( response.end_date, 'Y-m-d' ) ) {
											var datetime = startDate + ' at ' + startTime + ' to ' + endTime;
										} else {
											var datetime = startDate + ' at ' + startTime + ' to ' + endDate + ' at ' + endTime;
										}
									}
								}

								var html = wp.template('erp-crm-single-schedule-details')( { date: datetime, schedule: response } );
								$( '.content', modal ).html( html );
								$( '.loader', modal).remove();

								$('.erp-tips').tipTip( {
									defaultPosition: "top",
									fadeIn: 100,
									fadeOut: 100,
								} );

							},

							error: function( response ) {
								alert(response);
							}

						});
					}
				});
			},

			dayClick: function(date, jsEvent, view) {

				$.erpPopup({
					title: ( new Date( date) < new Date() ) ? '<?php _e( 'Add new Log', 'erp' ) ?>' : '<?php _e( 'Add new Schedule', 'erp' ); ?>',
					button: ( new Date( date) < new Date() ) ? '<?php _e( 'Create Log', 'erp' ) ?>' : '<?php _e( 'Create Schedule', 'erp' ); ?>',
					id: 'erp-crm-customer-schedules',
					content: wperp.template('erp-crm-customer-schedules')( { current_date: date.format() } ).trim(),
					extraClass: 'larger',
					onReady: function () {
						var modal = this;

						jQuery('.select2').select2({
							width : 'resolve',
						});

						jQuery('.erp-date-field').datepicker({
							dateFormat: 'yy-mm-dd',
							changeMonth: true,
							changeYear: true,
							yearRange: '-50:+5',
						});

						jQuery( '.erp-time-field' ).timepicker({
							'scrollDefault': 'now',
							'step': 15
						});

						$( 'select.erp-crm-contact-list-dropdown' ).select2({
							allowClear: true,
							placeholder: $(this).attr( 'data-placeholder' ),
							minimumInputLength: 3,
							ajax: {
								url: wpErpCrm.ajaxurl,
								dataType: 'json',
								delay: 250,
								escapeMarkup: function( m ) {
									return m;
								},
								data: function (params) {
									return {
										s: params.term, // search term
										_wpnonce: wpErpCrm.nonce,
										types: $(this).attr( 'data-types' ).split(','),
										action: 'erp-search-crm-contacts'
									};
								},
								processResults: function ( data, params ) {
									var terms = [];

									if ( data) {
										$.each( data.data, function( id, text ) {
											terms.push({
												id: id,
												text: text
											});
										});
									}

									if ( terms.length ) {
										return { results: terms };
									} else {
										return { results: '' };
									}
								},
								cache: true
							}
						});


						$( 'input[type=checkbox][name="allow_notification"]', modal ).trigger('change');
					},

					onSubmit: function(modal) {
						modal.disableButton();

						wp.ajax.send( {
							data: this.serialize(),
							success: function( res ) {
								var log_title = res.log_type.toLowerCase().replace(/\b[a-z]/g, function(letter) {
									return letter.toUpperCase();
								});

								var end_time = new Date( res.end_date ),
									start_time = new Date( res.start_date ),
									addOneMin = 60*1000,
									start_date = wperp.dateFormat( res.start_date, 'Y-m-d' );

								if ( res.end_date ) {
									if ( new Date( res.start_date ) < new Date() ) {
									   var time = date( 'g:ia', strtotime( $schedule['start_date'] ) );
									} else {
										if ( wperp.timeFormat( res.start_date ) == wperp.timeFormat( res.end_date ) ) {
											var time = wperp.timeFormat( res.start_date );
										} else {
											var time = wperp.timeFormat( res.start_date ) + ' to ' + wperp.timeFormat( res.end_date );
										}
									}

									var end_date = new Date( end_time.setTime( end_time.getTime() + addOneMin ) );

								} else {
									var time     = wperp.timeFormat( res.start_date );
									var end_date = new Date( start_time.setTime( start_time.getTime() + addOneMin ) );
								}

								var title = time + ' ' + log_title;
								var color = new Date( res.start_date ) < new Date() ? '#f05050' : '#03c756';

								var myEvent = {
									schedule : res,
									title: title,
									start: start_date,
									end: end_date,
									color: color
								};

								$('#erp-crm-schedule-calendar-<?php esc_attr( $tab ); ?>').fullCalendar( 'renderEvent', myEvent );

								modal.enableButton();
								modal.closeModal();
							},
							error: function(error) {
								modal.enableButton();
								alert( error );
							}
						});
					}

				}); //popup
			}
		});
	});
</script>
<?php
}