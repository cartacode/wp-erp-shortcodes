<?php
add_shortcode( 'activity_list', function() {
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
        'isActivityPage'	   => true
    ] );

    if ( function_exists( 'erp_get_vue_component_template' ) ) {
	    erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-new-note.php', 'erp-crm-timeline-feed-new-note' );
	    erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-email.php', 'erp-crm-timeline-feed-email' );
	    erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-log-activity.php', 'erp-crm-timeline-feed-log-activity' );
	    erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-task.php', 'erp-crm-timeline-feed-task-note' );    	
    }

	wp_localize_script( 'wp-erp-crm-vue-component', 'wpCRMvue', $contact_actvity_localize );
    wp_enqueue_script( 'wp-erp-crm-vue-customer' );

    wp_enqueue_script( 'post' );

    wp_enqueue_script( 'erp-crm' );
    wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
    
	wp_enqueue_style( 'erp-nprogress' );
    wp_enqueue_style( 'erp-select2' );
    wp_enqueue_style( 'activities' );

	$feeds_tab = array();
	$crm_users = array();

	if ( function_exists( 'erp_crm_get_customer_feeds_nav' ) )
		$feeds_tab = erp_crm_get_customer_feeds_nav();

	if ( function_exists( 'erp_crm_get_crm_user' ) )
		$crm_users = erp_crm_get_crm_user();

	$template = '';

	$template .= '
		<div class="wrap erp erp-crm-activities erp-single-customer" id="wp-erp">
			<hr>

			<div class="erp-customer-feeds" id="erp-customer-feeds" v-cloak>
				<div class="activity-filter" id="erp-crm-activities-filter">

					<div class="filters">
						<select style="width:180px;" v-selecttwo="filterFeeds.type" class="select2" v-model="filterFeeds.type" id="activity-type" data-placeholder="Select a type">
							<option value="">All Types</option>';

	foreach ( $feeds_tab as $key => $value ){
		$template .= '		<option value="' . $key . '">' . $value['title'] . '</option>';
	}

	$template .= '      </select>
					</div>

					<div class="filters">
						<select style="width:260px;" v-selecttwo="filterFeeds.created_by" class="select2" v-model="filterFeeds.created_by" id="activity-created-by" data-placeholder="Created by..">
							<option value="-1">All</option>';
	foreach ( $crm_users as $crm_user ){
		$template .= '		<option value="' . $crm_user->ID . '">' . $crm_user->display_name . '</option>';
	}

	
	$template .=		'</select>
					</div>

					<div class="filters">
						<select style="width:260px;" v-selecttwo="filterFeeds.customer_id" data-types="contact,company"  class="erp-crm-contact-list-dropdown" v-model="filterFeeds.customer_id" id="activity-created-for"  data-placeholder="Created for contact or company ..">
							<option value=""></option>
						</select>
					</div>

					<div class="filters">
						<input type="search" v-datepicker v-model="filterFeeds.created_at" placeholder="Created Date..">
					</div>

					<div class="clearfix"></div>
				</div>

				<div class="activity-content">

					<ul class="timeline" v-if = "feeds.length">

						<template v-for="( month, feed_obj ) in feeds | formatFeeds">

							<li class="time-label">
								<span class="bg-red">{{ month | formatDate \'F, Y\' }}</span>
							</li>

							<li v-for="feed in feed_obj">
								<timeline-feed :i18n="i18n" :is="loadTimelineComponent( feed.type )" :feed="feed"></timeline-feed>
							</li>

						</template>
					</ul>

					<div class="feed-load-more" v-show="( feeds.length >= limit ) && !loadingFinish">
						<button @click="loadMoreContent( feeds )" class="button">
							<i class="fa fa-cog fa-spin" v-if="loading"></i>
							&nbsp;<span v-if="!loading">Load More</span>
							&nbsp;<span v-else>Loading..</span>
						</button>
					</div>

					<div class="no-activity-found" v-if="!feeds.length">
						No Activity found for this Contact
					</div>

				</div>
			</div>

		</div>';

	return $template;
} );