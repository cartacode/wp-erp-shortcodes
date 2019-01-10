<div id="erp-modal">
    <div class="erp-modal">

        <span id="modal-label" class="screen-reader-text"><?php _e( 'Modal window. Press escape to close.', 'erp' ); ?></span>
        <a href="#" class="close">× <span class="screen-reader-text"><?php _e( 'Close modal window', 'erp' ); ?></span></a>

        <form action="" class="erp-modal-form" method="post">
            <header class="modal-header">
                <h2>&nbsp;</h2>
            </header>

            <div class="content-container modal-footer">
                <div class="content"><?php _e( 'Loading', 'erp' ); ?></div>
            </div>

            <footer>
                <ul>
                    <li>
                        <div class="erp-loader erp-hide"></div>
                    </li>
                    <li>
                        <span class="activate">
                            <button type="submit" class="button-primary"></button>
                        </span>
                    </li>
                </ul>
            </footer>
        </form>
    </div>
    <div class="erp-modal-backdrop"></div>
</div>
<style>
a#erp-set-emp-photo {
    border-radius: 3px;
    background: #0085ba;
    border-color: #0073aa #006799 #006799;
    box-shadow: 0 1px 0 #006799;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
    width: 150px;
    margin-top: 10px;
    clear: both;
    padding: 5px;
    text-align: center;
    display: inline-block;
}
.erp-modal .content .row label
{
    position: relative;
}
.erp-modal .content .row label span.required
{
    position: absolute;
}
.erp-employee-form .right-column .erp-employee-modal-right, .erp-customer-form .right-column .erp-employee-modal-right, .erp-employee-form .right-column .erp-crm-modal-right, .erp-customer-form .right-column .erp-crm-modal-right {
    border: 1px solid rgba(221, 221, 221, 0.4) !important;
    display: inline-block;
}
.erp-employee-form .advanced-fields, .erp-customer-form .advanced-fields {
    margin-left: 10px;
    display: flex;
    align-items: center;
}
.erp-employee-form .select2-container
{
    width: 100% !important;
}
.erp-employee-form input[type='email'], .erp-customer-form input[type='email'], .erp-employee-form input[type='number'], .erp-customer-form input[type='number'], .erp-employee-form input[type='password'], .erp-customer-form input[type='password'], .erp-employee-form input[type='search'], .erp-customer-form input[type='search'], .erp-employee-form input[type='tel'], .erp-customer-form input[type='tel'], .erp-employee-form input[type='text'], .erp-customer-form input[type='text'], .erp-employee-form input[type='url'], .erp-customer-form input[type='url'], .erp-employee-form textarea, .erp-customer-form textarea, .erp-employee-form select, .erp-customer-form select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: transparent;
    border: 1px solid #d1d1d1;
    border-radius: 3px;
    box-shadow: none;
    box-sizing: inherit;
    padding: 5px;
    width: 100%;
    line-height: 1.5;
    min-height: auto;
}
input[type=checkbox], input[type=radio] {
    border: 1px solid #b4b9be;
    background: #fff;
    color: #555;
    clear: none;
    cursor: pointer;
    display: inline-block;
    line-height: 0;
    height: 16px;
    margin: -4px 4px 0 0;
    outline: 0;
    padding: 0!important;
    text-align: center;
    vertical-align: middle;
    width: 16px;
    min-width: 16px;
    -webkit-appearance: none;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    transition: .05s border-color ease-in-out;
}
.erp-employee-form .employee-work, .erp-customer-form .employee-work, .erp-employee-form .employee-personal, .erp-customer-form .employee-personal, .erp-employee-form .others-info, .erp-customer-form .others-info, .erp-employee-form .contact-group, .erp-customer-form .contact-group, .erp-employee-form .additional-info, .erp-customer-form .additional-info, .erp-employee-form .social-info, .erp-customer-form .social-info {
    display: none;
}
.erp-modal .content .row select
{
        max-width: 100% !important;
}
#erp-new-employee-popup .select2-selection--single .select2-selection__rendered, #erp-employee-edit .select2-selection--single .select2-selection__rendered, #erp-customer-edit .select2-selection--single .select2-selection__rendered, #erp-crm-new-contact .select2-selection--single .select2-selection__rendered {
    height: 32px;
    line-height: 31px;
}
#erp-new-employee-popup .select2-selection--single, #erp-employee-edit .select2-selection--single, #erp-customer-edit .select2-selection--single, #erp-crm-new-contact .select2-selection--single {
    border: 1px solid #d1d1d1;
    border-radius: 3px;
    height: 33px;
}
</style>