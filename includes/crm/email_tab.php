
<div class="activity-feed" style="margin-bottom: 10px">
    <input type="text" placeholder="From" class="erp-date-field email_from">
    <input type="text" placeholder="To" id="email_to" class="erp-date-field email_to">
    <input type="button" value="Filter" class="search_email_btn" onclick="search_email()">
    <input type="button" value="Reset" class="search_email_reset_btn" onclick="search_email_reset()">
</div>

<div id="email_listing">
    <table id="list_email">
        <thead>
            <th width="50%"><strong>Subject</strong></th>
            <th><strong>From</strong></th>
            <th><strong>Date</strong></th>
        </thead>
        <tbody>

        <tbody>
        <tr v-for="(item, index) in items" :key="index">
            <td v-for="(column, indexColumn) in columns" :key="indexColumn">{{item[column]}}</td>
            <td></td>
        </tr>


        </tbody>
        </tbody>
    </table>
</div>

<div style="" class="no-activity-found no_email_found" >
    <?php _e( 'No Emails found', 'erp' ); ?>
</div>


<style>
    .submessages{
    }
    .email_snippet{

    }
    /* Chat containers */
    .email-chat .container {
        border: 2px solid #dedede;
        background-color: #f1f1f1;
        border-radius: 5px;
        padding: 10px;
        margin: 0px 0px 0 0px !important;
        width: auto;
        max-width: 100%;
        max-height: 500px;
        overflow-x: scroll;
    }
    .email-chat .container table{
        width:auto !important;
    }

    /* Darker chat container */
    .email-chat .darker {
        border-color: #ccc;
        background-color: #ddd;
    }

    /* Clear floats */
   .email-chat .container::after {
        content: "";
        clear: both;
        display: table;
    }

   .email-chat .right{
       text-align: right;
   }

    /* Style images */

    /* Style the right image */

    /* Style time text */
    .email-chat .time-right {
        float: right;
        color: #aaa;
    }

    /* Style time text */
    .email-chat .time-left {
        float: left;
        color: #999;
    }
    .popupWrap .popup.defaultPop{
        top: 152px;
        bottom: 0;
    }

    .ui-datepicker-trigger{
        display:none !important;
    }

    .email_from{
        min-height: 20px !important;
        padding-top: 0px !important;
        padding-bottom: 2px !important;
    }

    .email_to{
        min-height: 20px !important;
        padding-top: 0px !important;
        padding-bottom: 2px !important;
        margin-left: 5px;
    }
    .activity-feed{
        margin-bottom: 10px;
    }
    .search_email_btn{
        margin-left: 5px !important;
    }
    .search_email_reset_btn{
        margin-left: 2px !important;
    }
</style>