<?php
add_shortcode( 'import_contacts_from_csv', function() {
    $page           = '?action=download_sample';
    $nonce          = 'erp-emport-export-sample-nonce';
    $csv_sample_url = home_url( '/crmdashboard/import' ) . wp_nonce_url( $page, $nonce );

    $erp_import_export_fields = erp_get_import_export_fields();
    $keys = array_keys( $erp_import_export_fields );

    $import_export_types = [];
    foreach ( $keys as $type ) {
        $import_export_types[ $type ] = __( ucwords( $type ), 'erp' );
    }

    unset( $import_export_types['employee'] );

    $users       = [];
    $life_stages = [];
    $groups      = [];

    $life_stages    = erp_crm_get_life_stages_dropdown_raw();

    if ( !current_user_can('administrator') ) {
        $args['created_by'] = get_current_user_id();
    }
    $crm_users      = erp_crm_get_crm_user( $args );

    foreach ( $crm_users as $user ) {
        $users[ $user->ID ] = $user->display_name . ' &lt;' . $user->user_email . '&gt;';
    }

    $contact_groups = erp_crm_get_contact_groups( [ 'number' => '-1' ] );

    $groups = ['' => __( '&mdash; Select Group &mdash;', 'erp' )];
    foreach ( $contact_groups as $group ) {
        $groups[ $group->id ] = $group->name;
    }

    ob_start();

    erp_importer_notices( true );
?>
<div class="postbox">
    <div class="inside">
        <h3><?php _e( 'Import CSV', 'erp' ); ?></h3>

        <form method="post" action="<?php echo $csv_sample_url; ?>" enctype="multipart/form-data" id="import_form">

            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="type"><?php _e( 'Type', 'erp' ); ?></label>
                        </th>
                        <td>
                            <select name="type" id="type">
                                <?php foreach ( $import_export_types as $key => $value ) { ?>
                                    <option value="<?php echo $key; ?>"><?php _e( $value, 'erp' ); ?></option>
                                <?php } ?>
                            </select>
                            <p class="description"><?php _e( 'Select item type to import.', 'erp' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="type"><?php _e( 'CSV File', 'erp' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="file" name="csv_file" id="csv_file" />
                            <p class="description"><?php _e( 'Upload a csv file.', 'erp' ); ?></p>
                            <p id="download_sample_wrap">
                                <input type="hidden" value="<?php echo $csv_sample_url; ?>" />
                                <button class="button button-primary"> Download Sample CSV</button>
                            </p>
                        </td>
                    </tr>
                </tbody>
                <tbody id="crm_contact_lifestage_owner_wrap">
                    <tr>
                        <th>
                            <label for="contact_owner"><?php _e( 'Contact Owner', 'erp' ); ?></label>
                        </th>
                        <td>
                            <select name="contact_owner" id="contact_owner">
                                <?php
                                    $current_user = get_current_user_id();
                                    echo erp_html_generate_dropdown( $users, $current_user );
                                ?>
                            </select>
                            <p class="description"><?php _e( 'Contact owner for contact.', 'erp' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="life_stage"><?php _e( 'Life Stage', 'erp' ); ?></label>
                        </th>
                        <td>
                            <select name="life_stage" id="life_stage">
                                <?php echo erp_html_generate_dropdown( $life_stages ); ?>
                            </select>
                            <p class="description"><?php _e( 'Life stage for contact.', 'erp' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="contact_group"><?php _e( 'Contact Group', 'erp' ); ?></label>
                        </th>
                        <td>
                            <select name="contact_group">
                                <?php echo erp_html_generate_dropdown( $groups ); ?>
                            </select>
                            <p class="description"><?php _e( 'Imported contacts will be subscribed in selected group.', 'erp' ); ?></p>
                        </td>
                    </tr>
                </tbody>

                <tbody id="fields_container" style="display: none;">

                </tbody>
            </table>

            <?php wp_nonce_field( 'erp-import-export-nonce' ); ?>
            <?php submit_button( __( 'Import', 'erp' ), 'primary', 'erp_import_csv' ); ?>
        </form>
    </div><!-- .inside -->
</div><!-- .postbox -->
<?php
	erp_import_export_javascript( true );

	$template = ob_get_contents();

	ob_clean();

	return $template;
} );