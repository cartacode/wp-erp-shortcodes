<?php
add_shortcode( 'email-connect-settings', function() { ?>
<div class="wrap erp-settings">

	<form method="post" id="mainform" action="" enctype="multipart/form-data">

	    <?php WeDevs\ERP\Framework\ERP_Admin_Settings::output(); ?>

	    <?php
	    global $current_class;

	    do_action( 'erp_after_admin_settings_' . $current_class->get_id() );

	    $get_section_field_items = $current_class->get_section_field_items();
	    $submit_btn_status = isset( $get_section_field_items['submit_button'] ) ? $get_section_field_items['submit_button'] : true;
	    ?>
	    <?php
	    if ( $submit_btn_status ) {
	    	?>
	    	<p class="submit">
				<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'erp' ); ?>" />
		    	<input type="hidden" name="subtab" id="last_tab" />

		    	<?php wp_nonce_field( 'erp-settings-nonce' ); ?>
		    </p>
	    <?php } ?>

   </form>
</div>
<style>
a#imap-test-connection {
    color: #555;
    border-color: #ccc;
    background: #f7f7f7;
    box-shadow: 0 1px 0 #ccc;
    vertical-align: top;
    padding: 5px 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin-bottom: 5px;
    display: inline-block;
}
</style>
<script type="text/javascript">
	var ajaxurl = '/wp-admin/admin-ajax.php';
        jQuery(document).ready(function ($) {
            $("a#smtp-test-connection").click(function (e) {
                e.preventDefault();
                $("a#smtp-test-connection").attr('disabled', 'disabled');
                $("a#smtp-test-connection").parent().find('.erp-loader').show();

                var data = {
                    'action': 'erp_smtp_test_connection',
                    'enable_smtp': $('input[name=enable_smtp]:checked').val(),
                    'mail_server': $('input[name=mail_server]').val(),
                    'port': $('input[name=port]').val(),
                    'authentication': $('select[name=authentication]').val(),
                    'username': $('input[name=username]').val(),
                    'password': $('input[name=password]').val(),
                    'to': $('#smtp_test_email_address').val(),
                    '_wpnonce': '9343a5c5cd'
                };

                $.post(ajaxurl, data, function (response) {
                    $("a#smtp-test-connection").removeAttr('disabled');
                    $("a#smtp-test-connection").parent().find('.erp-loader').hide();

                    var type = response.success ? 'success' : 'error';

                    if (response.data) {
                        swal({
                            title: '',
                            text: response.data,
                            type: type,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#008ec2'
                        });
                    }
                });
            });
        });

        jQuery(document).ready(function ($) {
            $("a#imap-test-connection").click(function (e) {
                e.preventDefault();
                $("a#imap-test-connection").attr('disabled', 'disabled');
                $("a#imap-test-connection").parent().find('.erp-loader').show();

                var data = {
                    'action': 'erp_imap_test_connection',
                    'mail_server': $('input[name=mail_server]').val(),
                    'username': $('input[name=username]').val(),
                    'password': $('input[name=password]').val(),
                    'protocol': $('select[name=protocol]').val(),
                    'port': $('input[name=port]').val(),
                    'authentication': $('select[name=authentication]').val(),
                    '_wpnonce': '0297a670a9'
                };

                $.post(ajaxurl, data, function (response) {
                    $("a#imap-test-connection").removeAttr('disabled');
                    $("a#imap-test-connection").parent().find('.erp-loader').hide();

                    var type = response.success ? 'success' : 'error';

                    if (response.data) {
                        var status = response.success ? 1 : 0;
                        $('#imap_status').val(status);

                        swal({
                            title: '',
                            text: response.data,
                            type: type,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#008ec2'
                        });
                    }
                });
            });
        });
    </script>
<?php
wp_enqueue_script( 'erp-tiptip' );
        wp_enqueue_style( 'erp-sweetalert' );
wp_enqueue_script( 'erp-sweetalert' );     
wp_enqueue_style( 'erp-shortcode-styles' );
 } ); ?>
