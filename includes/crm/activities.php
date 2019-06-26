<?php
add_shortcode( 'activity_list', function() {
	$localize_script = get_default_localize_script();

	$contact_actvity_localize = get_default_contact_actvity_localize();
	$contact_actvity_localize['isActivityPage'] = true;

	if ( function_exists( 'erp_get_vue_component_template' ) ) {
		erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-new-note.php', 'erp-crm-timeline-feed-new-note' );
		erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-email.php', 'erp-crm-timeline-feed-email' );
		erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-log-activity.php', 'erp-crm-timeline-feed-log-activity' );
		erp_get_vue_component_template( WPERP_MODULES . '/crm/views/js-templates/timeline-task.php', 'erp-crm-timeline-feed-task-note' );
		erp_get_vue_component_template( WPERP_EMAIL_CAMPAIGN_VIEWS . '/email-campaign-component.php', 'erp-crm-timeline-email-campaign' );
	}

	wp_localize_script( 'wp-erp-crm-vue-component', 'wpCRMvue', $contact_actvity_localize );
	wp_enqueue_script( 'wp-erp-crm-vue-customer' );

	wp_enqueue_script( 'post' );

	wp_enqueue_script( 'erp-crm' );
	wp_localize_script( 'erp-crm', 'wpErpCrm', $localize_script );
	
	wp_enqueue_style( 'erp-select2' );
	wp_enqueue_style( 'erp-shortcode-styles' );

	$feeds_tab = array();
	$crm_users = array();

	if ( function_exists( 'erp_crm_get_customer_feeds_nav' ) )
		$feeds_tab = erp_crm_get_customer_feeds_nav();

	if ( function_exists( 'erp_crm_get_crm_user' ) )
		$crm_users = erp_crm_get_crm_user();

	$template = '';

	ob_start();
	?>

	<div class="wrap erp erp-crm-activities erp-single-customer" id="wp-erp">
		<hr>

		<div class="erp-customer-feeds" id="erp-customer-feeds" v-cloak>
			<div class="activity-filter" id="erp-crm-activities-filter">

				<div class="filters">
					<select style="width:180px;" v-selecttwo="filterFeeds.type" class="select2" v-model="filterFeeds.type" id="activity-type" data-placeholder="Select a type">
						<option value="">All Types</option>

						<?php foreach ( $feeds_tab as $key => $value ): ?>
							<option value="<?php esc_attr_e( $key ); ?>"><?php _e( $value['title'] ); ?></option>
						<?php endforeach; ?>

					</select>
				</div>

				<?php /*
				<div class="filters">
					<select style="width:260px;" v-selecttwo="filterFeeds.created_by" class="select2" v-model="filterFeeds.created_by" id="activity-created-by" data-placeholder="Created by..">
						<option value="-1">All</option>

						<?php foreach ( $crm_users as $crm_user ): ?>
							<option value="<?php esc_attr_e( $crm_user->ID ); ?>"><?php _e( $crm_user->display_name ); ?></option>
						<?php endforeach; ?>


					</select>
				</div>
				*/?>

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
							<span class="bg-red">{{ month | formatDate 'F, Y' }}</span>
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

	</div>
<?php

	$template = ob_get_contents();

	ob_end_clean();

	return $template;
} );