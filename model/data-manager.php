<?php

/* ================================================================================
 *
 * Data Manger is a MVC addon to help yu manager custom data within custom tables
 * in WordPress.
 *
 ================================================================================ */

include('../base.php');

/**
 * NNR_Data_Manager class.
 */
class NNR_Data_Manager extends NNR_Data_Manager_Base {

	/**
	 * Returns the proper table name for Multisies
	 *
	 * @access public
	 * @param mixed $table_name
	 * @return void
	 */
	function get_table_name($table_name) {

		global $wpdb;

		return $wpdb->prefix . $table_name;
	}

}