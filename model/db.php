<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

// Check if class already exists

if (!class_exists("NNR_Data_Manager_v1")):

/* ================================================================================
 *
 * Data Manger is a MVC addon to help you manager custom data within custom tables
 * in WordPress.
 *
 ================================================================================ */

if ( !class_exists('NNR_Data_Manager_Base_v1') ) {
	require_once( dirname(dirname(__FILE__)) . '/base.php');
}

/**
 * NNR_Data_Manager class.
 */
class NNR_Data_Manager_v1 extends NNR_Data_Manager_Base_v1 {

	/**
	 * data_format
	 *
	 * (default value: 'Y-m-d H:i:s')
	 *
	 * @var string
	 * @access public
	 */
	public $data_format = 'Y-m-d H:i:s';

	/**
	 * table_name
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access private
	 */
	private $table_name = '';

	/**
	 * default_data
	 *
	 * @var mixed
	 * @access public
	 */
	public $default_data = array(
		'name'					=> '',
		'active'				=> 1,
		'start_date'			=> '',
		'end_date'				=> '',
		'display_conditions'	=> '',
		'args'					=> '',
	);

	/**
	 * Create a new instance of the Data Manager class and set the table name
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function __construct($table_name) {

		do_action('nnr_data_man_before_new_db_model');

		$this->table_name = $table_name;

		do_action('nnr_data_man_after_new_db_model');
	}

	/**
	 * Create the table
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function create_table() {

		do_action('nnr_data_man_before_db_create_table');

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_db_create_table', "
			CREATE TABLE IF NOT EXISTS `" . $this->get_table_name() . "` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(60) NOT NULL DEFAULT '',
				`active` int(1) NOT NULL DEFAULT 1,
				`start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`display_conditions` longtext,
				`args` longtext,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1;
		") );

		do_action('nnr_data_man_after_db_create_table');

		return $result;
	}

	/**
	 * Adds data into the table as a new row
	 *
	 * @access public
	 * @param array $data (default: array())
	 * @return void
	 */
	function add_data( $data = array() ) {

		do_action('nnr_data_man_before_db_add_data');

		$data = $this->validate_data($data);

		$data = apply_filters('nnr_data_man_db_add_data', $data);

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_db_add_data_query', $wpdb->prepare("INSERT INTO `" . $this->get_table_name() . "` (
				`name`,
				`active`,
				`start_date`,
				`end_date`,
				`display_conditions`,
				`args`
			) VALUES (%s, %d, %s, %s, %s, %s)",
				$data['name'],
				$data['active'],
				date($this->data_format, strtotime($data['start_date'])),
				date($this->data_format, strtotime($data['end_date'])),
				serialize($data['display_conditions']),
				serialize($data['args'])
		) ) );

		do_action('nnr_data_man_after_db_add_data');

		// Return the recently created id for this entry

		return $wpdb->insert_id;

	}

	/**
	 * Update data
	 *
	 * @since 1.0.0
	 *
	 * @param	data to be updated
	 * @return	false if error, otherwise nothing
	 */
	public function update_data( $id = null, $data = array() ) {

		do_action('nnr_data_man_before_db_update_data');

		$data = $this->validate_data($data);

		if ( !isset($id) || empty($id) ) {
			return false;
		}

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_db_update_data_query', $wpdb->prepare(
			"UPDATE `" . $this->get_table_name() . "` SET
				`name` = %s,
				`active` = %d,
				`start_date` = %s,
				`end_date` = %s,
				`display_conditions` = %s,
				`args` = %s
			WHERE id = %d",
				$data['name'],
				$data['active'],
				date($this->data_format, strtotime($data['start_date'])),
				date($this->data_format, strtotime($data['end_date'])),
				serialize($data['display_conditions']),
				serialize($data['args']),
				$id
		) ) );

		do_action('nnr_data_man_after_db_update_data');

		return $result;
	}

	/**
	 * Get all data
	 *
	 * @since 1.0.0
	 *
	 * @param	id
	 * @return	data of optin_fire
	 */
	function get_data() {

		do_action('nnr_data_man_before_db_get_data');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_data_query', "SELECT * FROM `" . $this->get_table_name() . "`"), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_data');

		return apply_filters('nnr_data_man_db_get_data', $this->parse_data( $data ));
	}

	/**
	 * Get specfic data based on id
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function get_data_from_id( $id ){

		do_action('nnr_data_man_before_db_get_data_from_id');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_data_from_id_query', $wpdb->prepare("SELECT * FROM `" . $this->get_table_name() . "` WHERE `id` = %d", $id)), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_data_from_id');

		if ( $data ) {

			$parsed_data = $this->parse_data( $data );

			return apply_filters('nnr_data_man_db_get_data_from_id', $parsed_data[0]);
		} else {
			return apply_filters('nnr_data_man_db_get_data_from_id', null);
		}

	}

	/**
	 * Returns the name of a data object from the id
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function get_name_from_id( $id ) {

		do_action('nnr_data_man_before_db_get_name_from_id');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_name_from_id_query', $wpdb->prepare("SELECT `name` FROM `" . $this->get_table_name() . "` WHERE `id` = %d", $id)), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_name_from_id');

		if ( $data ) {
			return apply_filters('nnr_data_man_db_get_name_from_id', $data[0]['name']);
		} else {
			return apply_filters('nnr_data_man_db_get_name_from_id', 'No Name Found');
		}

	}

	/**
	 * Get active data
	 *
	 * @since 1.0.0
	 *
	 * @param	id
	 * @return	data of optin_fire
	 */
	function get_active_data() {

		do_action('nnr_data_man_before_db_get_active_data');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_active_data_query', "SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 1"), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_active_data');

		return apply_filters('nnr_data_man_db_get_active_data', $this->parse_data( $data ));
	}

	/**
	 * Set data to active
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function set_active( $id ) {

		do_action('nnr_data_man_before_db_set_active');

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_db_set_active_query', $wpdb->prepare("UPDATE `" . $this->get_table_name() . "` SET `active` = 1 WHERE `id` = %d"), $id));

		do_action('nnr_data_man_after_db_set_active');

		return $result;

	}

	/**
	 * Get active data
	 *
	 * @since 1.0.0
	 *
	 * @param	id
	 * @return	data of optin_fire
	 */
	function get_inactive_data() {

		do_action('nnr_data_man_before_db_get_inactive_data');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_inactive_data_query', "SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 0"), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_inactive_data');

		return apply_filters('nnr_data_man_db_get_inactive_data', $this->parse_data( $data ));
	}

	/**
	 * Set data to inactive
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function set_inactive( $id ) {

		do_action('nnr_data_man_before_db_set_inactive');

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_after_db_set_inactive_query', $wpdb->prepare("UPDATE `" . $this->get_table_name() . "` SET `active` = 0 WHERE `id` = %d"), $id));

		do_action('nnr_data_man_after_db_set_inactive');

		return $result;

	}

	/**
	 * Get data in date range
	 *
	 * @since 1.0.0
	 *
	 * @param	id
	 * @return	data of optin_fire
	 */
	function get_data_from_date_range( $start_date, $end_date ) {

		do_action('nnr_data_man_before_db_get_data_from_date_range');

		global $wpdb;

		$data = $wpdb->get_results( apply_filters('nnr_data_man_db_get_data_from_date_range_query', $wpdb->prepare( "SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 1 AND `start_date` <= %s AND `end_date` >= %s", $start_date, $end_date )), 'ARRAY_A');

		do_action('nnr_data_man_after_db_get_data_from_date_range');

		return apply_filters('nnr_data_man_db_get_data_from_date_range', $this->parse_data( $data ));
	}

	/**
	 * Delete some data
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function delete_data( $id ) {

		do_action('nnr_data_man_before_db_delete_data');

		global $wpdb;

		$result = $wpdb->query( apply_filters('nnr_data_man_db_delete_data_query', $wpdb->prepare("DELETE FROM `" . $this->get_table_name() . "` WHERE `id` = %d"), $id));

		do_action('nnr_data_man_after_db_delete_data');

		return $result;

	}

	/**
	 * Validate that the data is in the correct format
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function validate_data( $data ){
		return apply_filters('nnr_data_man_db_validate_data', array_merge($this->default_data, $data));
	}

	/**
	 * Parse the returned data
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function parse_data( $data ) {

		do_action('nnr_data_man_before_db_parse_data');

		$parsed_data = array();

		foreach($data as $row) {

			$entry = array();

			$entry["id"]           			= $row["id"];
			$entry["name"]         			= $row["name"];
			$entry["active"]        		= $row["active"];
			$entry["start_date"]        	= $row["start_date"];
			$entry["end_date"]        		= $row["end_date"];
			$entry['display_conditions']   	= $this->mb_unserialize($row['display_conditions']);
			$entry['args']         			= $this->mb_unserialize($row['args']);

			$parsed_data[] = $entry;
		}

		do_action('nnr_data_man_after_db_parse_data');

		return apply_filters('nnr_data_man_db_parse_data', $parsed_data);

	}

	/**
	 * Returns the proper table name for Multisies
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function get_table_name() {

		global $wpdb;

		return apply_filters('nnr_data_man_db_get_table_name', $wpdb->prefix . $this->table_name);
	}

}

endif;