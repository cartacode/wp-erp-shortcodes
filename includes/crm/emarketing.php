<?php
add_shortcode( 'email-campaign-list', function() {
	require_once WPERP_EMAIL_CAMPAIGN_INCLUDES . '/class-email-campaign-list-table.php';

	$template = '';
	wp_enqueue_style( 'table-view' );
	wp_enqueue_style( 'emarketing' );
	
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