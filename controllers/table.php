<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

// Check if class already exists

if ( !class_exists("NNR_Data_Manager_List_Table_v1") ):

/* ================================================================================
 *
 * Data Manger is a MVC addon to help you manager custom data within custom tables
 * in WordPress.
 *
 ================================================================================ */

/**
 * Include the WP_List_Table library
 *
 * @since 1.0.0
 *
 */
if ( !class_exists('WP_List_Table') ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * This class extends WP_List_Table and is used to create the table as
 *	seen in the admin panel.
 *
 * @since 1.0.0
 *
 * @extends	WP_List_Table
 */
class NNR_Data_Manager_List_Table_v1 extends WP_List_Table {

	/**
	 * The items to be displayed in the table
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access public
	 */
	public $items = array();

	/**
	 * table_name
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $table_name = '';

	/**
	 * prefix
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $prefix = '';

	/**
	 * text_domain
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $text_domain = '';

	/**
	 * dashboard_page
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $dashboard_page = '';

	/**
	 * add_edit_page
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $add_edit_page = '';

	/**
	 * stats_page
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $stats_page = '';

	/**
	 * stats_table_name
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $stats_table_name = '';

	/**
	 * Construtor
	 *
	 * @since 1.0.0
	 *
	 * @param	N/A
	 * @return	Instance
	 */
	function __construct( $table_name, $args = array(), $single = 'data', $plural = 'data'  ) {

		do_action('nnr_data_man_before_new_table_controller');

		$args = apply_filters('nnr_data_manager_dashboard_table_args', $args, array(
			'prefix'			=> '',
			'text_domain'		=> '',
			'dashboard_page'	=> '',
			'add_edit_page'		=> '',
			'stats_page'		=> '',
		));

        global $status, $page;

        $this->table_name = $table_name;
        $this->prefix = $args['prefix'];
        $this->text_domain = $args['text_domain'];
        $this->dashboard_page = $args['dashboard_page'];
        $this->add_edit_page = $args['add_edit_page'];
        $this->stats_page = $args['stats_page'];
        $this->stats_table_name = $args['stats_table_name'];

        $this->include_scripts();

        //Set parent defaults

        parent::__construct( array(
            'singular'  => $single,     	//singular name of the listed records
            'plural'    => $plural,    		//plural name of the listed records
            'ajax'      => false        	//does this table support ajax?
        ) );

        do_action('nnr_data_man_after_new_table_controller');
    }

    /**
     * Include all the scripts needed for displaying data properly
     *
     * @access public
     * @return void
     */
    function include_scripts() {

	    do_action('nnr_data_man_before_table_scripts');

	    wp_register_style( 'data-manager-dashboard-css-v1', plugins_url( 'css/dashboard.css', dirname(__FILE__)) );
		wp_enqueue_style( 'data-manager-dashboard-css-v1' );

		wp_register_script( 'data-manager-dashboard-js-v1', plugins_url( 'js/dashboard.js', dirname(__FILE__)) );
		wp_enqueue_script( 'data-manager-dashboard-js-v1' );

		do_action('nnr_data_man_after_table_scripts');

    }

	/**
	 * Called if there is data
	 *
	 * @since 1.0.0
	 *
	 * @param	N/A
	 * @return	N/A
	 */
	function no_items() {
		apply_filters('nnr_data_man_table_no_items', _e( 'No data found.' ));
	}

	/**
	 * This method dictates the table's columns and titles.
	 *
	 * @since 1.0.0
	 *
	 * @param	N/A
	 * @return	array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns(){

		do_action('nnr_data_man_table_get_columns');

		$columns = array(
			'status'             => __( 'ON / OFF', $this->text_domain),
			'name'               => __( 'Name', $this->text_domain),
			'impressions'        => __( 'Impressions', $this->text_domain),
			'conversions'        => __( 'Conversions', $this->text_domain),
			'conversion_rate'    => __( 'Conversion Rate', $this->text_domain),
			'start_date'         => __( 'Start Date', $this->text_domain),
			'end_date'        	 => __( 'End Date', $this->text_domain),
		);

		return apply_filters('nnr_data_man_table_get_columns', $columns);
	}

	/**
	 * Called for any colunm without a related function
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_default( $item, $column_name ) {

		do_action('nnr_data_man_table_column_default', $item, $column_name);

		return apply_filters('nnr_data_man_table_column_default', $item[$column_name]);
	}

	/**
	 * Status column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_status( $item ) {

		do_action('nnr_data_man_before_table_column_status');

		// Active

		if ( $item['active'] == 1 ) {

			$data = '<a href="' . get_admin_url() . 'admin.php?page=' . $this->dashboard_page . '&action=deactivate&data_id=' . $item['id'] . '&wp_nonce=' . wp_create_nonce($this->prefix . 'deactivate') . '" data-id="' . $item['id'] . '" data-status="deactivate" class="nnr-change-status fa fa-2x fa-toggle-on" data-toggle="tooltip" data-placement="bottom" title="' . __('Deactivate', $this->text_domain) . '"></a>';

		}

		// Inactive

		if ( $item['active'] == 0 ) {

			$data = '<a href="' . get_admin_url() . 'admin.php?page=' . $this->dashboard_page . '&action=activate&data_id=' . $item['id'] . '&wp_nonce=' . wp_create_nonce($this->prefix . 'activate') . '" data-id="' . $item['id'] . '" data-status="activate" class="nnr-change-status fa fa-2x fa-toggle-off" data-toggle="tooltip" data-placement="bottom" title="' . __('Activate', $this->text_domain) . '"></a>';
		}

		do_action('nnr_data_man_after_table_column_status');

        return apply_filters('nnr_data_man_table_column_status', $data);

	}

	/**
	 * Name column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_name( $item ) {

		do_action('nnr_data_man_before_table_column_name');

		// Build row actions

        $actions = array();

		// Edit

		$actions['edit_1'] = sprintf('<a href="?page=%s&action=edit&data_id=%s&wp_nonce=%s" class="nnr-row-action fa fa-cogs" data-toggle="tooltip" data-placement="bottom" title="' . __('Edit', $this->text_domain) . '"></a>',
            $this->add_edit_page,
            $item['id'],
            wp_create_nonce($this->prefix . 'edit')
        );

		// Preview

		$actions['edit_2'] = sprintf('<a href="?page=%s&action=preview&data_id=%s&wp_nonce=%s" class="nnr-row-action fa fa-eye" data-toggle="tooltip" data-placement="bottom" title="' . __('Preview', $this->text_domain) . '"></a>',
	        $this->dashboard_page,
	        $item['id'],
	        wp_create_nonce($this->prefix . 'preview')
	    );

        // Duplicate

        $actions['edit_3'] = sprintf('<a data-id="%s" class="nnr-row-action nnr-duplicate fa fa-files-o" data-toggle="tooltip" data-placement="bottom" title="' . __('Duplicate', $this->text_domain) . '" href="%s"></a>',
        	$item['id'],
        	get_admin_url() . 'admin.php?page=' . $this->dashboard_page . '&action=duplicate&data_id=' . $item['id'] . '&wp_nonce=' . wp_create_nonce($this->prefix . 'duplicate')
        );

        // Stats

		$actions['edit_5'] = sprintf('<a title="Stats" href="?page=%s&data_id=%s&data_name=%s" class="nnr-row-action fa fa-bar-chart" data-toggle="tooltip" data-placement="bottom"></a>',
            $this->stats_page,
            $item['id'],
            $item['name']
        );

		// Delete

		$actions['delete'] = sprintf('<span data-toggle="tooltip" data-placement="bottom" title="' . __('Delete', $this->text_domain) . '"><a href="#" class="nnr-delete nnr-row-action fa fa-trash-o" data-toggle="modal" data-target="#nnr-delete" data-name="%s" data-id="%s" data-url="%s"></a></span>',
            $item['name'],
            $item['id'],
            get_admin_url() . 'admin.php?page=' . $this->dashboard_page . '&action=delete&data_id=' . $item['id'] . '&wp_nonce=' . wp_create_nonce($this->prefix . 'delete')
        );

        do_action('nnr_data_man_after_table_column_name');

        // Return the title contents

        return apply_filters('nnr_data_man_table_column_name', sprintf('%1$s <small style="opacity: 0.5;">id:(%2$s)</small> %3$s',
            '<span><a href="?page=' . $this->add_edit_page . '&action=edit&data_id=' . $item['id'] . '&wp_nonce=' . wp_create_nonce($this->prefix . 'edit') . '">' . $item['name'] . '</a></span>',
            $item['id'],
            $this->row_actions($actions)
        ));
	}

	/**
	 * Impressions column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_impressions( $item ) {

		do_action('nnr_data_man_before_table_column_impressions');

		$stats_tracker = new NNR_Stats_Tracker_v1($this->stats_table_name);
		$stats = $stats_tracker->get_stats(null, null, $item['id']);

		$impressions_total = 0;
		foreach ($stats as $stat) {
		    $impressions_total += $stat['impressions'];
		}

		do_action('nnr_data_man_after_table_column_impressions');

        return apply_filters('nnr_data_man_table_column_impressions', number_format($impressions_total));
	}

	/**
	 * Conversions column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_conversions( $item ) {

		do_action('nnr_data_man_after_table_column_conversions');

		$stats_tracker = new NNR_Stats_Tracker_v1($this->stats_table_name);
		$stats = $stats_tracker->get_stats(null, null, $item['id']);

		$impressions_total = 0;
		foreach ($stats as $stat) {
		    $impressions_total += $stat['conversions'];
		}

		do_action('nnr_data_man_before_table_column_conversions');

        return apply_filters('nnr_data_man_table_column_conversions', number_format($impressions_total));
	}

	/**
	 * Conversion Rate column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_conversion_rate( $item ) {

		do_action('nnr_data_man_before_table_column_conversion_rate');

		$stats_tracker = new NNR_Stats_Tracker_v1($this->stats_table_name);
		$stats = $stats_tracker->get_stats(null, null, $item['id']);

		$impressions_total = 0;
		$conversions_total = 0;
		foreach ($stats as $stat) {
		    $conversions_total += $stat['conversions'];
		    $impressions_total += $stat['impressions'];
		}

        $ctr = $impressions_total != 0 ? round(($conversions_total/$impressions_total) * 100, 2) : 0;

        do_action('nnr_data_man_after_table_column_conversion_rate');

        return apply_filters('nnr_data_man_table_column_conversion_rate', $ctr . '%');
	}

	/**
	 * Start Date column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_start_date( $item ) {

		do_action('nnr_data_man_table_column_start_date');

		return apply_filters('nnr_data_man_table_column_start_date', $item['start_date']);
	}

	/**
	 * End Date column
	 *
	 * @since 1.0.0
	 *
	 * @param	array $item A singular item (one full row's worth of data)
	 * @param	array $column_name The name/slug of the column to be processed
	 * @return	string Text or HTML to be placed inside the column <td>
	 */
	function column_end_date( $item ) {

		do_action('nnr_data_man_table_column_end_date');

		return apply_filters('nnr_data_man_table_column_end_date', $item['end_date']);
	}

	/**
	 * Prepares data for display.
	 *
	 * @since 1.0.0
	 *
	 * @param	N/A
	 * @return	N/A
	 */
	function prepare_items() {

		do_action('nnr_data_man_before_table_prepare_items');

		$data_manager = new NNR_Data_Manager_v1( $this->table_name );

		global $wpdb;

        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $current_page = $this->get_pagenum();

        // All

        if ( !isset($_GET['status']) ) {
	    	$this->items = $data_manager->get_data();
        } else if ( isset($_GET['status']) && $_GET['status'] == 'active' ) {
	        $this->items = $data_manager->get_active_data();
        } else {
	        $this->items = $data_manager->get_inactive_data();
        }

        $this->items = apply_filters('nnr_data_man_table_items', $this->items);

        $total_items = count($this->items);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = apply_filters('nnr_data_man_table_data', array_slice($this->items,(($current_page-1)*$per_page),$per_page));

        $this->set_pagination_args( apply_filters('nnr_data_man_table_set_pagination_args', array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) ) );

        do_action('nnr_data_man_after_table_prepare_items');
	}

	/**
	 * Get a list of CSS classes for the <table> tag
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	public function get_table_classes() {
	    return apply_filters('nnr_data_man_table_get_table_classes', array('table table-striped table-responsive'));
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {

		$singular = $this->_args['singular'];

		//$this->display_tablenav( 'top' );

		?>
		<table class="<?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="the-list"<?php
				if ( $singular ) {
					echo " data-wp-lists='list:$singular'";
				} ?>>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Generate row actions div
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	protected function row_actions( $actions, $always_visible = false ) {

		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		return $out;
	}

	/**
	 * Generates and display row actions links for the list table.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $item        The item being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string The row actions output. In this case, an empty string.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		return '';
 	}
}

endif;