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
		$this->table_name = $table_name;
	}

	/**
	 * Create the table
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function create_table() {

		global $wpdb;

		$result = $wpdb->query("
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
		");

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

		$data = array_merge($this->default_data, $data);

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("INSERT INTO `" . $this->get_table_name() . "` (
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
			)
		);

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

		$data = array_merge($this->default_data, $data);

		if ( !isset($id) || empty($id) ) {
			return false;
		}

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare(
			"UPDATE `" . $this->get_table_name() . "` SET
				`name` = %s,
				`active` = %d,
				`start_date` = %s,
				`end_date` = %s,
				`display_conditions` = %s,
				`args` = %s
			WHERE id = %d",
				$data['name'],
				$dataval['active'],
				date($this->data_format, strtotime($data['start_date'])),
				date($this->data_format, strtotime($data['end_date'])),
				serialize($data['display_conditions']),
				serialize($data['args']),
				$id
			)
		);

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

		global $wpdb;

		$data = $wpdb->get_results("SELECT * FROM `" . $this->get_table_name() . "`", 'ARRAY_A');

		return $this->parse_data( $data );
	}

	/**
	 * Get specfic data based on id
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function get_data_from_id( $id ){

		global $wpdb;

		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $this->get_table_name() . "` WHERE `id` = %d", $id), 'ARRAY_A');

		if ( $data ) {

			$parsed_data = $this->parse_data( $data );

			return $parsed_data[0];
		} else {
			return null;
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

		global $wpdb;

		$data = $wpdb->get_results("SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 1", 'ARRAY_A');

		return $this->parse_data( $data );
	}

	/**
	 * Set data to active
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function set_active( $id ) {

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("UPDATE `" . $this->get_table_name() . "` SET `active` = 1 WHERE `id` = %d", $id));

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

		global $wpdb;

		$data = $wpdb->get_results("SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 0", 'ARRAY_A');

		return $this->parse_data( $data );
	}

	/**
	 * Set data to inactive
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function set_inactive( $id ) {

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("UPDATE `" . $this->get_table_name() . "` SET `active` = 0 WHERE `id` = %d", $id));

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

		global $wpdb;

		$start_date = date($this->data_format, strtotime($start_date));
		$end_date = date($this->data_format, strtotime($end_date));

		$data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $this->get_table_name() . "` WHERE `active` = 1 AND `start_date` >= %s AND `end_date` <= %s", $start_date, $end_date ), 'ARRAY_A');

		return $this->parse_data( $data );
	}

	/**
	 * Delete some data
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function delete_data( $id ) {

		global $wpdb;

		$result = $wpdb->query($wpdb->prepare("DELETE FROM `" . $this->get_table_name() . "` WHERE `id` = %d", $id));

		return $result;

	}

	/**
	 * Parse the returned data
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	function parse_data( $data ) {

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

		return $parsed_data;

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

		return $wpdb->prefix . $this->table_name;
	}

}

endif;