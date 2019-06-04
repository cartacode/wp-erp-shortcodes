<?php
/**
 * List table class
 */
class Custom_Employee_List_Table extends \WP_List_Table {

    private $counts = array();
    private $page_status = '';

    function __construct() {
        global $status, $page, $page_status;

        parent::__construct( array(
            'singular' => 'employee',
            'plural'   => 'employees',
            'ajax'     => false
        ) );
    }

    /**
     * Render extra filtering option in
     * top of the table
     *
     * @since 0.1
     *
     * @param  string $which
     *
     * @return void
     */
    function extra_tablenav( $which ) {
        if ( $which != 'top' ) {
            return;
        }

        $selected_desingnation = ( isset( $_GET['filter_designation'] ) ) ? $_GET['filter_designation'] : 0;
        $selected_department   = ( isset( $_GET['filter_department'] ) ) ? $_GET['filter_department'] : 0;
        $selected_type   = ( isset( $_GET['filter_employment_type'] ) ) ? $_GET['filter_employment_type'] : '';
        ?>
        <div class="alignleft actions">

            <label class="screen-reader-text" for="new_role"><?php _e( 'Filter by Employment Type', 'erp' ) ?></label>
            <select name="filter_employment_type" id="filter_employment_type">
                <option value="-1"><?php _e( '- Select Employment Type -', 'erp' ) ?></option>
                <?php
                    $types = erp_hr_get_employee_types();

                    foreach ( $types as $key => $title ) {
                        echo sprintf( "<option value='%s'%s>%s</option>\n", $key, selected( $selected_type, $key, false ), $title );
                    }
                ?>
            </select>

            <?php
            submit_button( __( 'Filter' ), 'button', 'filter_employee', false );
        echo '</div>';
    }

    /**
     * Message to show if no employee found
     *
     * @return void
     */
    function no_items() {
        _e( 'No employee found.', 'erp' );
    }

    /**
     * Default column values if no callback found
     *
     * @param  object  $item
     * @param  string  $column_name
     *
     * @return string
     */
    function column_default( $employee, $column_name ) {

        switch ( $column_name ) {
            case 'brokerage_office':
                return $employee->get_brokerage_office('view');

            case 'department':
                return $employee->get_department('view');

            case 'type':
                return $employee->get_type('view');

            case 'date_of_hire':
                return $employee->get_joined_date();

            case 'status':
                return erp_hr_get_employee_statuses_icons( strtolower($employee->status) );

            default:
                return isset( $employee->$column_name ) ? $employee->$column_name : '';
        }
    }

    public function current_action() {

        if ( isset( $_REQUEST['filter_employee'] ) ) {
            return 'filter_employee';
        }

        if ( isset( $_REQUEST['employee_search'] ) ) {
            return 'employee_search';
        }

        return parent::current_action();
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'name'         => array( 'employee_name', false ),
            'date_of_hire' => array( 'hiring_date', false ),
        );

        return $sortable_columns;
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'name'         => __( 'Employee Name', 'erp' ),
            'brokerage_office'  => __( 'Brokerage Office', 'erp' ),
            'department'   => __( 'Department', 'erp' ),
            'type'         => __( 'Employment Type', 'erp' ),
            'date_of_hire' => __( 'Joined', 'erp' ),
            'status'       => __( 'Status', 'erp' ),
        );

        return apply_filters( 'erp_hr_employee_table_cols', $columns );
    }

    /**
     * Render the employee name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_name( $employee ) {
        $actions     = array();
        $delete_url  = '';
        $data_hard   = ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) ? 1 : 0;
        $delete_text = ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) ? __( 'Permanent Delete', 'erp' ) : __( 'Delete', 'erp' );

        if ( current_user_can( 'erp_edit_employee', $employee->get_user_id() ) ) {
            $actions['edit']   =  sprintf( '<a href="%s" data-id="%d"  title="%s">%s</a>', $delete_url, $employee->get_user_id(), __( 'Edit this item', 'erp' ), __( 'Edit', 'erp' ) );
        }

        if ( current_user_can( 'erp_delete_employee' ) ) {
            $actions['delete'] = sprintf( '<a href="%s" class="submitdelete" data-id="%d" data-hard=%d title="%s">%s</a>', $delete_url, $employee->get_user_id(), $data_hard, __( 'Delete this item', 'erp' ), $delete_text );
        }

        if ( $data_hard ) {
            $actions['restore'] = sprintf( '<a href="%s" class="submitrestore" data-id="%d" title="%s">%s</a>', $delete_url, $employee->get_user_id(),  __( 'Restore this item', 'erp' ), __( 'Restore', 'erp' ) );
        }

        if ( class_exists( 'user_switching' ) && function_exists( 'current_wp_erp_user_is' ) ) {
            if ( current_user_can( 'administrator') || current_wp_erp_user_is( 'broker' ) || current_wp_erp_user_is( 'staff' ) ) {
                $user = get_user_by( 'id', $employee->get_user_id() );
                $switch_link = wp_nonce_url( add_query_arg( array(
                    'action'  => 'switch_to_user',
                    'user_id' => $user->ID,
                    'nr'      => 1,
                ), wp_login_url() ), "switch_to_user_{$user->ID}" );
                
                $actions['switch_to_user'] = '<a href="' . esc_url( $switch_link ) . '">' . esc_html__( 'Switch&nbsp;To', 'user-switching' ) . '</a>';
            }            
        }

        return sprintf( '%4$s <a href="%3$s"><strong>%1$s</strong></a> %2$s', $employee->get_full_name(), $this->row_actions( $actions ), erp_hr_url_single_employee( $employee->get_user_id() ), $employee->get_avatar() );
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = [];

        if ( ! current_user_can( erp_hr_get_manager_role() ) ) {
            return $actions;
        }

        $actions = [
            'delete'  => __( 'Move to Trash', 'erp' ),
        ];

        if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) {
            unset( $actions['delete'] );

            $actions['permanent_delete'] = __( 'Permanent Delete', 'erp' );
            $actions['restore'] = __( 'Restore', 'erp' );
        }

        return $actions;
    }

    /**
     * Render the checkbox column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="employee_id[]" value="%s" />', $item->id
        );
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views() {
        $status_links   = array();
        $base_link      = home_url( '/myteam/teammates' );

        foreach ($this->counts as $key => $value) {
            $class = ( $key == $this->page_status ) ? 'current' : 'status-' . $key;
            $status_links[ $key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => $key ), $base_link ), $class, $value['label'], $value['count'] );
        }

        $status_links[ 'trash' ] = sprintf( '<a href="%s" class="status-trash">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => 'trash' ), $base_link ), __( 'Trash', 'erp' ), erp_hr_count_trashed_employees() );

        return $status_links;
    }

    /**
     * Search form for employee table
     *
     * @since 0.1
     *
     * @param  string $text
     * @param  string $input_id
     *
     * @return void
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s_keyword'] ) && !$this->has_items() )
            return;

        $input_id = $input_id . '-search-input';
        ?>
        <form class="show-search-box">
            <p class="search-box">
                <input type="search" id="<?php echo $input_id ?>" name="s_keyword" value="<?php _e(esc_attr_e( $_REQUEST['s_keyword'] )) ?>" />
                <input type="submit" name="" value="Search Teammate" />
            </p>
        </form>
        <?php
    }

    /**
     * Prepare the class items
     *
     * @return void
     */
    function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = 20;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'active';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if ( isset( $_REQUEST['s_keyword'] ) && !empty( $_REQUEST['s_keyword'] ) ) {
            $args['s'] = $_REQUEST['s_keyword'];
        }

        if ( isset( $_REQUEST['orderby'] ) && !empty( $_REQUEST['orderby'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
        }

        if ( isset( $_REQUEST['status'] ) && !empty( $_REQUEST['status'] ) && current_user_can( erp_hr_get_manager_role() ) ) {
            $args['status'] = $_REQUEST['status'];
        }

        if ( isset( $_REQUEST['order'] ) && !empty( $_REQUEST['order'] ) ) {
            $args['order'] = $_REQUEST['order'];
        }

        if ( isset( $_REQUEST['filter_designation'] ) && $_REQUEST['filter_designation'] ) {
            $args['designation'] = $_REQUEST['filter_designation'];
        }

        if ( isset( $_REQUEST['filter_department'] ) && $_REQUEST['filter_department'] ) {
            $args['department'] = $_REQUEST['filter_department'];
        }

        if ( isset( $_REQUEST['filter_employment_type'] ) && $_REQUEST['filter_employment_type'] ) {
            $args['type'] = $_REQUEST['filter_employment_type'];
        }

        $this->counts = erp_hr_employee_get_status_count();
        $this->items  = erp_hr_get_employees( $args );

        $args['count'] = true;
        $total_items = erp_hr_get_employees( $args );


        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );
    }

    function pagination( $which ) {
        if ( empty( $this->_pagination_args ) ) {
            return;
        }
     
        $total_items = $this->_pagination_args['total_items'];
        $total_pages = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }
     
        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }
     
        $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';
     
        $current = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();
     
        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
     
        $current_url = remove_query_arg( $removable_query_args, $current_url );
     
        $page_links = array();
     
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';
     
        $disable_first = $disable_last = $disable_prev = $disable_next = false;
     
        if ( $current == 1 ) {
            $disable_first = true;
            $disable_prev = true;
        }
        if ( $current == 2 ) {
            $disable_first = true;
        }
        if ( $current == $total_pages ) {
            $disable_last = true;
            $disable_next = true;
        }
        if ( $current == $total_pages - 1 ) {
            $disable_last = true;
        }
     
        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', 1, $current_url ) ),
                __( 'First page' ),
                '&laquo;'
            );
        }
     
        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }
     
        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;
     
        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
                __( 'Next page' ),
                '&rsaquo;'
            );
        }
     
        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
                __( 'Last page' ),
                '&raquo;'
            );
        }
     
        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';
     
        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";
     
        echo $this->_pagination;
    }

}
