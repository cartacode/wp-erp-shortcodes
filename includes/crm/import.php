<?php
//show import export
add_shortcode('show_all_import',function(){
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
                            <input type="file" name="csv_file" id="csv_file" />
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
                    <tr><td><input type="submit" name="erp_import_csv" id="erp_import_csv" class="button button-primary" value="Import"></td></tr>
                    </tbody>
            </table> 
        </form>';
        return $html_6;
});
function erp_import_export_download_sample_action_1() {
    global $wpdb;
    $user_details = wp_get_current_user();

     if(isset($_POST['erp_import_csv']))
     {
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
                        $query_2 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."erp_peoples WHERE email = '".$emapData[0]."'" );

                        if($query_2)
                        {
                            echo "Record already exists of ".$emapData[0];
                            echo "<br/>";
                        }
                        else
                        {

                            //insert into wp_erp_peoples
                           $query_1 = "INSERT INTO ".$wpdb->prefix."erp_peoples(`user_id`,`first_name`,`last_name`,`company`,`email`,`phone`,`mobile`,`other`,`website`,`fax`,`notes`,`street_1`,`street_2`,`city`,`state`,`postal_code`,`country`,`currency`,`life_stage`,`contact_owner`,`hash`,`created_by`,`created`) values('','".$emapData[0]."','".$emapData[1]."','','".$emapData[2]."','".$emapData[3]."','".$emapData[4]."','".$emapData[5]."','".$emapData[6]."','".$emapData[7]."','".$emapData[8]."','".$emapData[9]."','".$emapData[10]."','".$emapData[11]."','".$emapData[12]."','".$emapData[13]."','".$emapData[14]."','NULL','customer','".$user_details->ID."','".md5($user_details->ID)."','".$user_details->ID."','".$date."')";
                            $result = $wpdb->query($query_1);
                            //extract from wp_erp_peoples and insert into wp_erp_people_type_relations
                            $get_last_id = $wpdb->insert_id;
                            $query_2 = "INSERT INTO ".$wpdb->prefix."erp_people_type_relations(`people_id`,`people_types_id`) values('".$get_last_id."','1')";
                            $wpdb->query($query_2); 
                           
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