<?php
add_shortcode( 'email-campaign-list', function() {
	require_once WPERP_EMAIL_CAMPAIGN_INCLUDES . '/class-email-campaign-list-table.php';

	$template = '';
    wp_enqueue_style( 'erp-shortcode-styles' );
	
	if( ! is_admin() ){
	   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	   require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	   require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	   require_once( ABSPATH . 'wp-admin/includes/template.php' );
	}

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

    if ( $action == 'edit' ) {
        $campaign_id = !empty( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
    }

    $ecampGlobal = [
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'erp-email-campaign' ),
        'debug'     => defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : false,
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
                <i class="fa fa-paper-plane-o"></i> {{ i18n.sendPreview }}
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
                <table class="form-table review-details-table">
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