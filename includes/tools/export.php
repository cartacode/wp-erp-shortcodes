<?php
//export all records in csv file
add_shortcode( 'show_all_export', function() {
    wp_enqueue_style( 'erp-shortcode-styles' );

    $erp_import_export_fields = erp_get_import_export_fields();
    $keys = array_keys( $erp_import_export_fields );

    $import_export_types = [];
    foreach ( $keys as $type ) {
        $import_export_types[ $type ] = __( ucwords( $type ), 'erp' );
    }

    unset( $import_export_types['employee'] );

    ob_start();
?>

<div class="postbox">
    <div class="inside">
        <h3><?php _e( 'Export Contacts / Companies', 'erp' ); ?></h3>

        <form method="post" action="<?php echo admin_url( 'admin.php?page=erp-tools' ); ?>" id="export_form">

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
                            <p class="description"><?php _e( 'Select item type to export.', 'erp' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="fields"><?php _e( 'Fields', 'erp' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <label><input type="checkbox" id="selecctall"/> <strong><?php _e( 'Select All', 'erp' ); ?></strong></label>
                            <br />
                            <div id="fields"></div>
                            <p class="description"><?php _e( 'Only selected field will be on the csv file.', 'erp' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php wp_nonce_field( 'erp-import-export-nonce' ); ?>
            <?php submit_button( __( 'Export', 'erp' ), 'primary', 'erp_export_csv' ); ?>
        </form>
    </div><!-- .inside -->
</div><!-- .postbox -->


<?php
    erp_import_export_javascript( true );

    $template = ob_get_contents();
    ob_clean();

    return $template;
});
?>