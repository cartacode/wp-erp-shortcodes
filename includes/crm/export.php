<?php
//export all records in csv file
add_shortcode('show_all_export',function(){
    
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

    $is_crm_activated = erp_is_module_active( 'crm' );
    $is_hrm_activated = erp_is_module_active( 'hrm' );

    $erp_import_export_fields = erp_get_import_export_fields();
    $keys = array_keys( $erp_import_export_fields );
    
    $get_export_data = site_url().'/export_csv.php';
    
    $html_7 = '<h3>'._e( 'Export CSV', 'erp' ).'</h3>';

    $html_7 .=   '<form method="post" action="'.$get_export_data.'" id="export_form">

            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="type">Type</label>
                        </th>
                        <td>
                            <select name="type" id="type"> 
                                    <option value="'.$keys[0].'">'.$keys[0].'</option>
                            </select>
                            <p class="description">Select item type to export</p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="fields">Fields<span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="checkbox" id="ckbCheckAll" value="select_all" />Select All
						    <p id="checkBoxes">
						        <input type="checkbox" name="fields[]" class="checkBoxClass"  value="first_name" /> First Name
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="last_name" /> Last Name
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="email" /> Email
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="phone" /> Phone
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="mobile" /> Mobile
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="other" /> Other
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="website" /> Website
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="fax" /> Fax
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="notes" /> Notes
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="street_1" /> Street1
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="street_2"  /> Street2
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="city" /> City
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="state" /> State
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="postal_code"  /> Postal Code
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="country" /> Country
						        <input type="checkbox" name="fields[]" class="checkBoxClass" value="currency" /> Currency
						    </p>

                            <p class="description">Only selected field will be on the csv file</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="export_all" value="Export" />
        </form>';
    return $html_7;
});
?>