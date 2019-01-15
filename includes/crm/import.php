<?php
//show import export
add_shortcode('show_all_import',function(){

    global $wpdb;
    $csv_file_path = site_url(). '/wp-content/plugins/wp-erp-shortcodes/sample_csv_contact-1.csv';
    
     $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

        $is_crm_activated = erp_is_module_active( 'crm' );
        $is_hrm_activated = erp_is_module_active( 'hrm' );

        $erp_import_export_fields = erp_get_import_export_fields();
        $keys = array_keys( $erp_import_export_fields );

        $import_export_types = [];
        foreach ( $keys as $type ) {
            $import_export_types[ $type ] = __( ucwords( $type ), 'erp' );
        }

        $csv_sample_url = erp_import_export_download_sample_action_1();


        $users       = [];
        $life_stages = [];
        $groups      = [];

        //get contact groups during import
        /*$items = array();
        $get_groups = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."erp_crm_contact_group");
        foreach($get_groups as $get_group_items)
        {
            $group_name = $get_group_items->name;
        }
        echo $group_name;*/
        //get user details
        $user_details = wp_get_current_user();

    $html_6 =  '<h3>'._e( 'Import CSV', 'erp' ).'</h3>';
    $html_6 .=  '<form method="post" action="'.$csv_sample_url.'" enctype="multipart/form-data" id="import_form">
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
                            <p class="description">Select item type to import</p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="type">CSV File<span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="file" name="csv_file" id="csv_file" required/>
                            <p class="description">Upload a csv file</p>
                            <p id="download_sample_wrap">
                                
                                <a  href="'.$csv_file_path.'" class="button button-primary" download> Download Sample CSV</a>
                            </p>
                        </td>
                    </tr>
                </tbody>
                <tbody id="crm_contact_lifestage_owner_wrap">
                    <tr>
                        <th>
                            <label for="contact_owner">Contact Owner</label>
                        </th>
                        <td>
                            <select name="contact_owner" id="contact_owner">
                                    <option value="'.$user_details->ID.'">'.$user_details->user_login.' '.$user_details->user_email.'</option>
                            </select>
                            <p class="description">Contact owner for contact</p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="contact_group">Contact Group</label>
                        </th>
                        <td>
                            <select name="contact_group">
                            <option value="select">- Select Group -</option>
                            ';
                                $get_groups = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."erp_crm_contact_group");
                                foreach($get_groups as $get_group_items)
                                {
                            $html_6 .= '<option value="'.$get_group_items->id.'">'.$get_group_items->name.'</option>';
                                }
                        $html_6 .=   '</select>
                            <p class="description">Imported contacts will be subscribed in selected group.</p>
                        </td>
                    </tr>
                    </tbody>
                    <tbody id="show_below_form" style="display:none">
                        <tr>
                            <th>
                                <label for="fields[first_name]" class="csv_field_labels">First Name <span class="required">*</span></label>
                            </th>
                            <td>
                                <select name="first_name" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0" selected>First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[last_name]" class="csv_field_labels">Last Name</label>
                            </th>
                            <td>
                                <select name="last_name" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1" selected>Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[email]" class="csv_field_labels">Email</label>
                            </th>
                            <td>
                                <select name="email" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0" >First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2" selected>Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[phone]" class="csv_field_labels">Phone</label>
                            </th>
                            <td>
                                <select name="phone" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3" selected>Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[mobile]" class="csv_field_labels">Mobile</label>
                            </th>
                            <td>
                                <select name="mobile" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4" selected>Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[other]" class="csv_field_labels">Other</label>
                            </th>
                            <td>
                                <select name="other" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5" selected>Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[website]" class="csv_field_labels">Website</label>
                            </th>
                            <td>
                                <select name="website" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6" selected>Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[fax]" class="csv_field_labels">Fax</label>
                            </th>
                            <td>
                                <select name="fax" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7" selected>Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[notes]" class="csv_field_labels">Notes</label>
                            </th>
                            <td>
                                <select name="notes" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8" selected>Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[street_1]" class="csv_field_labels">Street 1</label>
                            </th>
                            <td>
                                <select name="street1" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9" selected>Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[street_2]" class="csv_field_labels">Street 2</label>
                            </th>
                            <td>
                                <select name="street2" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10" selected>Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[city]" class="csv_field_labels">City</label>
                            </th>
                            <td>
                                <select name="city" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11" selected>City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[state]" class="csv_field_labels">State</label>
                            </th>
                            <td>
                                <select name="state" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12" selected>State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[postal_code]" class="csv_field_labels">Postal Code</label>
                            </th>
                            <td>
                                <select name="postal_code" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13" selected>Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[country]" class="csv_field_labels">Country</label>
                            </th>
                            <td>
                                <select name="country" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14" selected>Country</option>
                                    <option value="15">Currency</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="fields[currency]" class="csv_field_labels">Currency</label>
                            </th>
                            <td>
                                <select name="currency" class="csv_fields" required="">
                                    <option>— Select Field —</option>
                                    <option value="0">First Name</option>
                                    <option value="1">Last Name</option>
                                    <option value="2">Email</option>
                                    <option value="3">Phone</option>
                                    <option value="4">Mobile</option>
                                    <option value="5">Other</option>
                                    <option value="6">Website</option>
                                    <option value="7">Fax</option>
                                    <option value="8">Notes</option>
                                    <option value="9">Street1</option>
                                    <option value="10">Street2</option>
                                    <option value="11">City</option>
                                    <option value="12">State</option>
                                    <option value="13">Postal Code</option>
                                    <option value="14">Country</option>
                                    <option value="15" selected>Currency</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tr><td><input type="submit" name="erp_import_csv" id="erp_import_csv" class="button button-primary" value="Import"></td></tr>
            </table> 
        </form>';
        return $html_6;
});
function erp_import_export_download_sample_action_1() {
    global $wpdb;
    $user_details = wp_get_current_user();

     if(isset($_POST['erp_import_csv']))
     {
        //post csv form
        $first_name     = $_POST['first_name'];
        $last_name      = $_POST['last_name'];
        $email          = $_POST['email'];
        $phone          = $_POST['phone'];
        $mobile         = $_POST['mobile'];
        $other          = $_POST['other'];
        $website        = $_POST['website'];
        $fax            = $_POST['fax'];
        $notes          = $_POST['notes'];
        $street1        = $_POST['street1'];
        $street2        = $_POST['street2'];
        $city           = $_POST['city'];
        $state          = $_POST['state'];
        $postal_code    = $_POST['postal_code'];
        $country        = $_POST['country'];
        $currency       = $_POST['currency'];

        //contact group
        $group_id   = $_POST['contact_group'];
        
        $filename = $_FILES["csv_file"]["name"];
        $filetmp  = $_FILES["csv_file"]["tmp_name"];
        $ext=substr($filename,strrpos($filename,"."),(strlen($filename)-strrpos($filename,".")));
        $date = date('Y-m-d H:i:s');

        if($ext==".csv")
        {
          $file = fopen($filetmp, "r");
                 $counter = 0;
                 while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
                 {
                    if($counter != 0)
                    {
                        //check if email exists in database or not
                        $query_2 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."erp_peoples WHERE email = '".$emapData[2]."'" );

                        if($query_2)
                        {
                            echo "Record already exists of ".$emapData[0];
                            echo "<br/>";
                        }
                        else
                        {

                            //insert into wp_erp_peoples
                           $query_1 = "INSERT INTO ".$wpdb->prefix."erp_peoples(`user_id`,`first_name`,`last_name`,`company`,`email`,`phone`,`mobile`,`other`,`website`,`fax`,`notes`,`street_1`,`street_2`,`city`,`state`,`postal_code`,`country`,`currency`,`life_stage`,`contact_owner`,`hash`,`created_by`,`created`) values('','".$emapData[$first_name]."','".$emapData[$last_name]."','','".$emapData[$email]."','".$emapData[$phone]."','".$emapData[$mobile]."','".$emapData[$other]."','".$emapData[$website]."','".$emapData[$fax]."','".$emapData[$notes]."','".$emapData[$street1]."','".$emapData[$street2]."','".$emapData[$city]."','".$emapData[$state]."','".$emapData[$postal_code]."','".$emapData[$country]."','NULL','customer','".$user_details->ID."','".md5($user_details->ID)."','".$user_details->ID."','".$date."')";
                            $result = $wpdb->query($query_1);
                            //extract from wp_erp_peoples and insert into wp_erp_people_type_relations
                            $get_last_id = $wpdb->insert_id;
                            $query_2 = "INSERT INTO ".$wpdb->prefix."erp_people_type_relations(`people_id`,`people_types_id`) values('".$get_last_id."','1')";
                            $wpdb->query($query_2); 
                            //insert data into contact_subscriber table
                            if($group_id != 'select')
                            {
                                $query_3 = "INSERT INTO ".$wpdb->prefix."erp_crm_contact_subscriber(`user_id`,`group_id`,`status`,`subscribe_at`,`unsubscribe_at`,`hash`) values('".$get_last_id."','".$group_id."','subscribe','".$date."',NULL,'".md5($user_details->ID)."')";
                                $wpdb->query($query_3);
                            }
                        }
                    }
                    $counter++;    
                 }
                 fclose($file);
                 echo "CSV File has been successfully Imported.";
                 echo "<br/>";
        }
        else {
            echo "Error: Please Upload only CSV File";
        }
        
     }
}
?>