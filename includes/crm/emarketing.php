<?php
add_shortcode( 'email-campaign-list', function() {
    if ( isset( $_REQUEST['action'] ) && isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
        switch ( $_REQUEST['action'] ) {
            case 'duplicate':
                erp_email_campaign()->die_if_invalid_campaign( $_GET['id'], true );

                $new_campaign = erp_email_campaign()->duplicate_campaign( $_GET['id'] );

                $redirect = remove_query_arg( [ '_wp_http_referer', '_wpnonce', 'campaign_search', 'id', 'action', 'action2' ], wp_unslash( $_SERVER['REQUEST_URI'] ) );

                $redirect = add_query_arg( 'duplicated', $new_campaign->id, $redirect );
                break;

            case 'trash':
                if ( is_array( $_GET['id'] ) ) {
                    foreach ( $_GET['id'] as $id ) {
                        erp_email_campaign()->die_if_invalid_campaign( $id, false );
                    }
                } else {
                        erp_email_campaign()->die_if_invalid_campaign( $_GET['id'], false );                    
                }

                $delete_count = erp_email_campaign()->trash_campaigns( $_GET['id'] );

                $redirect = add_query_arg( 'trashed', $delete_count, $redirect );
                break;
            
            default:
                break;
        }

        if ( isset( $redirect ) && !empty( $redirect ) ) {
            echo '<script>';
            echo 'window.location.href = "' . $redirect . '";';
            echo '</script>';
        }
    }



	require_once WPERP_EMAIL_CAMPAIGN_INCLUDES . '/class-email-campaign-list-table.php';

	$template = '';
    wp_enqueue_style( 'erp-shortcode-styles' );
	
	if( ! is_admin() ){
	   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	   require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	   require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	   require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}

    wp_list_table_pagination();

	$campaign_table = new WeDevs\ERP\CRM\EmailCampaign\Campaign_List_Table();

	ob_start();
	
    ?>

    <div class="wrap erp-email-campaign erp-email-campaign-list" id="erp-email-campaign-list">
        <div class="list-table-wrap">
            <div class="list-table-inner">

                <form method="get" class="email-campaign-list-table-form">
                    <?php if ( version_compare( WPERP_VERSION, '1.4.0', '<' ) ): ?>
                        <input type="hidden" name="page" value="erp-email-campaign">
                    <?php else: ?>
                        <input type="hidden" name="page" value="erp-crm">
                        <input type="hidden" name="section" value="email-campaign">
                    <?php endif?>
                    <?php
                        $campaign_table->prepare_items();
                        $campaign_table->search_box();
                        $campaign_table->views();

                        $campaign_table->display();
                    ?>
                </form>

            </div><!-- .list-table-inner -->
        </div><!-- .list-table-wrap -->
    </div>

    <?php
	$template .= ob_get_contents(); 

	ob_end_clean();

	return $template;
} );

add_shortcode( 'new-email-campaign', function() {
    wp_enqueue_media();

    $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

    $campaign_id = !empty( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

    $ecampGlobal = [
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'erp-email-campaign' ),
        'debug'     => true,
        'date'      => [
            'format'        => ecamp_js_date_format(),
            'placeholder'   => erp_format_date( 'now' )
        ],
        'time'      => [
            'format'        => get_option( 'time_format', 'g:i a' ),
            'placeholder'   => date( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) )
        ],
    ];
    
    wp_enqueue_style( 'erp-shortcode-styles' );

    wp_enqueue_style( 'tiny-mce', site_url( '/wp-includes/css/editor.css' ), [], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_enqueue_script( 'tiny-mce', site_url( '/wp-includes/js/tinymce/tinymce.min.js' ), [] );

    wp_enqueue_script( 'tiny-mce-code', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/tinymce/plugins/code/plugin.min.js', [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'tiny-mce-hr', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/tinymce/plugins/hr/plugin.min.js', [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'tiny-mce-wpeditimage', site_url( '/wp-includes/js/tinymce/plugins/wpeditimage/plugin.min.js' ), [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_enqueue_style( 'erp-email-campaign-template-style', WPERP_EMAIL_CAMPAIGN_ASSETS . '/css/email-template-styles.css', [], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_enqueue_style( 'erp-email-campaign-editor' );

    // scripts
    wp_enqueue_script( 'erp-email-campaign-editor' );

    wp_localize_script( 'erp-email-campaign-editor', 'ecampGlobal', $ecampGlobal );


    wp_enqueue_style( 'erp-email-campaign-vendor' );

    wp_enqueue_style( 'erp-email-campaign' );

    wp_enqueue_script( 'erp-vue-table', WPERP_CRM_ASSETS . "/js/vue-table.js", [ 'erp-vuejs', 'jquery' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'erp-email-campaign-vendor' );
    wp_localize_script( 'erp-vue-table', 'wpVueTable', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'wp-erp-vue-table' )
    ] );

    wp_enqueue_script( 'erp-email-campaign' );

    // localized vars for the single campaign page
    $ecampGlobal['searchPlaceHolder'] = __( 'Search Contact', 'erp-email-campaign' );

    wp_localize_script( 'erp-email-campaign', 'ecampGlobal', $ecampGlobal );

    ob_start();
    ?>
    <div class="wrap erp-email-campaign erp-email-campaign-edit" id="erp-email-campaign-edit" v-cloak>

        <h2 class="clear ecamp-page-title">
            {{ pageTitle }}
            <span class="alignright">
                <button class="button" :disabled="isPreviewBtnDisabled" v-on:click="goToPreviewPage">
                    <i class="fa fa-eye"></i> {{ i18n.previewTemplate }}
                </button>
                <button class="button" :disabled="isPreviewBtnDisabled" v-on:click="sendPreviewEmail">
                    <i class="fa fa-paper-plane"></i> {{ i18n.sendPreview }}
                </button>
            </span>
        </h2>

        <form action="#" v-on:submit="preventFormSubmission">
            <div class="erp-grid-container margin-top-12">
                <div id="editor-step-1" v-if="1 === step">
                    <campaign-form  :i18n="i18n" :form-data="formData" :automatic-actions="automaticActions" :shortcodes="customizerData.shortcodes"></campaign-form>
                </div>

                <div id="editor-step-2" v-if="2 === step">
                    <customizer :customizer-data="customizerData" :i18n="i18n" :email-template="emailTemplate" ></customizer>
                </div>

                <div id="editor-step-3" v-if="3 === step">
                    <h3>{{ i18n.reviewDetails }}</h3>
                    <hr>
                    <table class="form-table review-details-table test">
                        <tbody>
                            <tr>
                                <th><label for="email-subject">{{ i18n.emailSubject }}</label></th>
                                <td>{{ formData.subject }}</td>
                            </tr>
                            <tr>
                                <th><label for="sender">{{ i18n.sender }}</label></th>
                                <td>{{ formData.sender.name }} <{{ formData.sender.email }}></td>
                            </tr>
                            <tr>
                                <th><label for="reply-to">{{ i18n.replyTo }}</label></th>
                                <td>{{ formData.replyTo.name }} <{{ formData.replyTo.email }}></td>
                            </tr>
                            <tr>
                                <th><label for="campaign-type">{{ i18n.newsletterType }}</label></th>
                                <td>
                                    <span v-if="'automatic' !== formData.send">{{ i18n.standard }}</span>
                                    <span v-else>{{ i18n.automatic }}</span>

                                    <p v-if="'automatic' === formData.send">{{{ automaticPhrase }}}</p>
                                </td>
                            </tr>
                            <tr v-if="'automatic' !== formData.send">
                                <th><label>{{ i18n.lists }}</label></th>
                                <td>
                                    <div class="row">
                                        <div class="col-3">
                                            <ul class="ecamp-contact-lists" v-for="(typeSlug, listType) in formData.lists">
                                                <li><strong>{{ listType.title }}</strong></li>
                                                <ul>
                                                    <li v-for="list in listType.lists" v-if="isListSelected(typeSlug, list.id)">
                                                        {{ list.name }} ({{ list.count }})
                                                    </li>
                                                    <li v-if="!listType.lists.length"><em>{{ i18n.noListFound }}</em></li>
                                                </ul>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="'automatic' !== formData.send">
                                <th><label>{{ i18n.scheduleCampaign }}</label></th>
                                <td>
                                    <div class="row erp-email-campaign-schedule-setter">
                                        <div class="col-3" v-if="formData.isScheduled">
                                            <div class="row">
                                                <div class="col-1">
                                                    <input type="checkbox" class="form-control" v-model="formData.isScheduled">
                                                </div>
                                                <div class="col-2">
                                                    <datepicker
                                                        id="delivery-date"
                                                        class="form-control margin-bottom-12"
                                                        :date="formData.schedule.date"
                                                        :exclude="'prev'"
                                                    ></datepicker>
                                                </div>

                                                <div class="col-1 symbol">
                                                    @
                                                </div>

                                                <div class="col-2">
                                                    <timepicker
                                                        id="delivery-time"
                                                        class="form-control"
                                                        :time="formData.schedule.time"
                                                    ></timepicker>
                                                </div>
                                            </div>

                                            <p class="hint">Current local time is {{ currentLocalTime }}</p>

                                        </div>
                                        <div class="col-4" v-else>
                                            <label><input type="checkbox" class="form-control" v-model="formData.isScheduled"> Yes schedule it</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><label>{{ i18n.googleCampaignName }}</label></th>
                                <td>
                                    <div class="row">
                                        <div class="col-3">
                                            <input type="text" class="form-control" v-model="formData.campaignName">
                                            <p class="hint">For example "New year sale"</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- .erp-grid-container -->

            <p v-if="!hideFooterBtns">
                <button class="button button-primary" type="button" v-if="showPrevBtn" v-on:click="--step">
                    <i class="fa fa-angle-double-left"></i> {{ i18n.previous }}
                </button>
                 <button class="button button-primary" type="button" v-if="showNextBtn" :disabled="isNextBtnDisabled" v-on:click="goToNextStep(step)">
                    {{ i18n.next }} <i class="fa fa-angle-double-right"></i>
                </button>
                 <button class="button button-primary" type="button" v-if="showSubmitBtn" :disabled="isSubmitBtnDisabled" v-on:click="saveCampaign(false, false)">
                    {{ submitBtnLabel }}
                </button>
                 <button class="button" type="button" v-if="showSubmitBtn" :disabled="isSubmitBtnDisabled" v-on:click="saveCampaign(true, false)">
                    {{ i18n.saveAsDraft }}
                </button>
            </p>

            <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
        </form>

    </div><!-- #erp-email-campaign-edit -->


    <?php
    $template = ob_get_contents();
    ob_end_clean();

    return $template;
} );

add_shortcode( 'view-single-campaign', function() {

    $campaign = new WeDevs\ERP\CRM\EmailCampaign\Single_Campaign( $_GET['id'] );
    $email_stats = $campaign->get_email_stats_with_legends();
    $url_stats = $campaign->get_url_stats();
    $sent = $campaign->campaign->people->count();
    $total_people = $sent + $campaign->campaign->peopleQueue->count();
    
    wp_enqueue_media();

    $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

    $ecampGlobal = [
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'erp-email-campaign' ),
        'debug'     => true,
        'date'      => [
            'format'        => ecamp_js_date_format(),
            'placeholder'   => erp_format_date( 'now' )
        ],
        'time'      => [
            'format'        => get_option( 'time_format', 'g:i a' ),
            'placeholder'   => date( get_option( 'time_format', 'g:i a' ), current_time( 'timestamp' ) )
        ],
    ];
    
    wp_enqueue_style( 'erp-shortcode-styles' );

    wp_enqueue_style( 'tiny-mce', site_url( '/wp-includes/css/editor.css' ), [], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_enqueue_script( 'tiny-mce', site_url( '/wp-includes/js/tinymce/tinymce.min.js' ), [] );

    wp_enqueue_script( 'tiny-mce-code', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/tinymce/plugins/code/plugin.min.js', [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'tiny-mce-hr', WPERP_EMAIL_CAMPAIGN_ASSETS . '/js/tinymce/plugins/hr/plugin.min.js', [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'tiny-mce-wpeditimage', site_url( '/wp-includes/js/tinymce/plugins/wpeditimage/plugin.min.js' ), [ 'tiny-mce' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );

    wp_enqueue_style( 'erp-email-campaign-template-style', WPERP_EMAIL_CAMPAIGN_ASSETS . '/css/email-template-styles.css', [], WPERP_EMAIL_CAMPAIGN_VERSION );
    wp_enqueue_style( 'erp-email-campaign-editor' );

    // scripts
    wp_enqueue_script( 'erp-email-campaign-editor' );

    wp_localize_script( 'erp-email-campaign-editor', 'ecampGlobal', $ecampGlobal );


    wp_enqueue_style( 'erp-email-campaign-vendor' );

    wp_enqueue_style( 'erp-email-campaign' );

    wp_enqueue_script( 'erp-vue-table', WPERP_CRM_ASSETS . "/js/vue-table.js", [ 'erp-vuejs', 'jquery' ], WPERP_EMAIL_CAMPAIGN_VERSION, true );
    wp_enqueue_script( 'erp-email-campaign-vendor' );
    wp_localize_script( 'erp-vue-table', 'wpVueTable', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'wp-erp-vue-table' )
    ] );

    wp_enqueue_script( 'erp-email-campaign' );

    // localized vars for the single campaign page
    $ecampGlobal['searchPlaceHolder'] = __( 'Search Contact', 'erp-email-campaign' );

    erp_email_campaign()->die_if_invalid_campaign( $_GET['id'] );

    // top nav filter and group filters for campaign subscribers list table
    $ecampGlobal['topNavFilter'] = erp_email_campaign()->get_campaign_subscriber_statuses( $_GET['id'] );

    $groups = erp_email_campaign()->get_campaign_contact_groups( $_GET['id'] );

    $groups = array_map( function ( $group ) {
        return [
            'id' => $group[0]->id,
            'text' => $group[0]->name
        ];

    } , (array) $groups );

    array_unshift( $groups, [ 'id' => 0, 'text' => __( 'Filter by Contact Group', 'erp-email-campaign' ) ] );

    $ecampGlobal['groupFilter'] = $groups;

    // i18n strings
    $ecampGlobal['i18n'] = [
        'name'              => __( 'Name', 'erp-email-campaign' ),
        'email'             => __( 'Email Status', 'erp-email-campaign' ),
        'lists'             => __( 'Lists', 'erp-email-campaign' ),
        'subs_status'       => __( 'Subscription Status', 'erp-email-campaign' ),
        'opened'            => __( 'Opened', 'erp-email-campaign' ),
        'confirmDuplicate'  => __( 'Are you sure you want to duplicate this campaign?', 'erp-email-campaign' )
    ];

    $ecampGlobal['campaignId'] = $_GET['id'];

    wp_localize_script( 'erp-email-campaign', 'ecampGlobal', $ecampGlobal );

    ob_start();
    ?>
    <script>
        var campaignEmailStats = JSON.parse('<?php echo json_encode( $email_stats ); ?>');
    </script>
    <div class="wrap erp erp-email-campaign erp-email-campaign-single send-<?php echo $campaign->send; ?>" id="erp-email-campaign-single">
        <h1 style="margin-bottom: 15px;"><?php _e( 'Campaign', 'erp-email-campaign' ); ?> : <?php echo $campaign->email_subject; ?></h1>

        <div class="list-table-wrap erp-grid-container">
            <div class="row">
                <div class="col-3">
                    <div class="postbox ecamp-single-summery" style="height: 312px;">
                        <h3 class="hndle"><span><?php _e( 'Summery', 'erp-email-campaign' ); ?></span></h3>

                        <table class="wp-list-table widefat fixed striped valign-top table-summery">
                            <tbody>
                                <tr>
                                    <th><?php _e( 'Status', 'erp-email-campaign' ); ?></th>
                                    <td>
                                        <?php if ( ( 'paused' !== $campaign->status && 'draft' !== $campaign->status) && 'scheduled' === $campaign->send && ! empty( $campaign->deliver_at ) && ( strtotime( $campaign->deliver_at ) > current_time( 'timestamp' ) ) ): ?>
                                                <span class="list-table-status scheduled">
                                                    <?php _e( 'Scheduled', 'erp-email-campaign' ); ?>
                                                    <span class="schedule-label"><i class="dashicons dashicons-clock"></i> <?php _e( 'send at', 'erp-email-campaign' ) ?>: <?php echo date( 'm-d-Y g:i a', strtotime( $campaign->deliver_at ) ) ?></span>
                                                </span>
                                        <?php else: ?>
                                            <span class="list-table-status <?php echo $campaign->status; ?>">
                                                <?php if ( 'active' === $campaign->status ): ?>
                                                    <?php echo $campaign->get_active_campaign_status(); ?>
                                                <?php elseif( 'sent' === $campaign->status ): ?>
                                                    <?php printf( __( 'Sent to %s subscribers', 'erp-email-campaign' ), $sent ); ?>
                                                <?php else: ?>
                                                    <?php echo $campaign->email_campaign->statuses[ $campaign->status ]['label']; ?>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e( 'Subject', 'erp-email-campaign' ); ?></th>
                                    <td>
                                        <?php echo $campaign->email_subject; ?>
                                    </td>
                                </tr>

                                <?php if ( 'automatic' !== $campaign->send ): ?>
                                    <tr>
                                        <th><?php _e( 'Lists', 'erp-email-campaign' ); ?></th>
                                        <td>
                                            <?php
                                                $list_titles = $campaign->get_list_titles();
                                                echo empty( $list_titles ) ? '-' : implode( ', ' , $campaign->get_list_titles() );
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th><?php _e( 'From', 'erp-email-campaign' ); ?></th>
                                    <td>
                                        <?php echo $campaign->sender_name; ?> &lt;<?php echo $campaign->sender_email; ?>&gt;
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php _e( 'Reply To', 'erp-email-campaign' ); ?></th>
                                    <td>
                                        <?php echo $campaign->reply_to_name; ?> &lt;<?php echo $campaign->reply_to_email; ?>&gt;
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="ecamp-single-summery-btns">
                            <a href="#duplicate" class="button duplicate-campaign" data-campaign="<?php echo $campaign->id; ?>">
                                <?php _e( 'Duplicate', 'erp-email-campaign' ); ?>
                            </a>

                            <a href="<?php echo site_url( '?erp-email-campaign=1&view-email-in-browser=1&campaign=' . $campaign->id ); ?>" target="_blank" class="button">
                                <?php _e( 'View', 'erp-email-campaign' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="postbox single-campaign-chart">
                        <h3 class="hndle"><span><?php _e( 'Email Stats', 'erp-email-campaign' ); ?></span></h3>

                        <div class="postbox-inside">
                            <div id="ecmap-single-email-stats" style="width: 100%; height: 250px;">
                                <?php if ( empty( $email_stats ) ): ?>
                                    <p class="text-center">
                                        <?php _e( 'No email statistic found for this campaign', 'erp-email-campaign' ); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3><span><?php _e( 'Link Statistics', 'erp-email-campaign' ); ?></span></h3>
            <div class="postbox">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="column-serial">#</th>
                            <th class="campaign-link"><?php _e( 'Links', 'erp-email-campaign' ); ?></th>
                            <th class="click-counts text-center"><?php _e( 'Unique Clicks', 'erp-email-campaign' ); ?></th>
                            <th class="click-counts text-center"><?php _e( 'Total Clicks', 'erp-email-campaign' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $url_stats ) ): ?>
                            <tr>
                                <td class="text-center" colspan="4"><?php _e( 'No link statistic found for this campaign', 'erp-email-campaign' ); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach( $url_stats as $i => $stat ): ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo $stat->url; ?></td>
                                    <td class="text-center"><?php echo $stat->unique_click; ?></td>
                                    <td class="text-center"><?php echo $stat->total_click; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <h3><span><?php _e( 'Campaign Subscribers', 'erp-email-campaign' ); ?></span></h3>
            <div id="campaign-people-stats">
                <vtable v-ref:vtable
                    table-class="customers"
                    action="get_campaign_people_data"
                    :wpnonce="wpnonce"
                    page="<?php echo home_url( '/crmdashboard/email-marketing/view-campaign?action=view' ); ?>"
                    per-page="10"
                    :top-nav-filter="topNavFilter"
                    :extra-bulk-action = "groupFilter"
                    :fields="fields"
                    :search="search"
                    hide-cb="hide"
                    after-fetch-data="afterFetchData"
                ></vtable>

                <div id="erp-email-campaign-subscriber-details" class="erp-email-campaign-modal" tabindex="-1" role="dialog">
                    <div class="erp-email-campaign-modal-dialog" role="document">
                        <div class="erp-email-campaign-modal-content">
                            <div class="erp-email-campaign-modal-header">
                                <button type="button" class="erp-close" data-dismiss="erp-email-campaign-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="erp-email-campaign-modal-title"><?php _e( "Subscriber's campaign activity", 'erp-email-campaign' ); ?></h4>
                            </div>
                            <div v-if="subscriberDetailsIsFetching" class="erp-email-campaign-modal-body">
                                <div class="text-center">
                                    <div class="erp-spinner"></div><br>
                                    <?php _e( 'Loading activity data', 'erp-email-campaign' ); ?>...
                                </div>
                            </div>
                            <div v-else class="erp-email-campaign-modal-body">
                                <div class="clearfix">
                                    <p class="subscriber-profile pull-left">
                                        <img :src="subscriberInfo.avatar" alt="">
                                        <a :href="subscriberInfo.details_url">{{ subscriberInfo.first_name }} {{ subscriberInfo.last_name }}</a>
                                        {{ subscriberInfo.email }}
                                    </p>
                                </div>

                                <div class="subscriber-activities">
                                    <div v-for="timeLineItem in timeLineItems" class="subscriber-activity">
                                        <i class="timeline-icon"></i>
                                        <div class="timeline-item-content">
                                            <p v-if="'sent' === timeLineItem.type">
                                                <?php _e( 'Email sent', 'erp-email-campaign' ); ?>
                                            </p>
                                            <p v-if="'open' === timeLineItem.type">
                                                <?php _e( 'Opened email', 'erp-email-campaign' ); ?>
                                            </p>
                                            <p v-if="'url' === timeLineItem.type">
                                                <?php _e( 'Clicked link', 'erp-email-campaign' ); ?> <a target="_blank" :href="timeLineItem.url">{{ timeLineItem.url }}</a>
                                            </p>
                                            <span class="timeline-time">{{ getTimeLineDateTime(timeLineItem.time) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>

        </div><!-- .list-table-wrap -->
    </div>

    <?php
    $template = ob_get_contents();
    ob_end_clean();

    return $template;
});