<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

// Check if class already exists

if ( !class_exists("NNR_Data_Manager_Settings_v1") ):

/* ================================================================================
 *
 * Data Manger Settings is used to output the settings HTML and handle the input.
 *
 ================================================================================ */

if ( !class_exists('NNR_Data_Manager_Base_v1') ) {
	require_once( dirname(dirname(__FILE__)) . '/base.php');
}

/**
 * NNR_Data_Manager_Settings_v1 class.
 */
class NNR_Data_Manager_Settings_v1 extends NNR_Data_Manager_Base_v1 {

	/**
	 * Used in displaying the settings
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $prefix = '';

	/**
	 * Called when the object is first created
	 *
	 * @access public
	 * @param mixed $prefix
	 * @return void
	 */
	function __construct( $prefix = '', $text_domain = '' ) {
		$this->prefix = $prefix;
		$this->text_domain = $text_domain;

		$this->include_scripts();
	}

	/**
	 * Include all scripts needed for the settings
	 *
	 * @access public
	 * @return void
	 */
	function include_scripts() {

		wp_register_style( $this->prefix . 'bootstrap-datepicker-css', plugins_url( 'css/bootstrap-datetimepicker.min.css', dirname(__FILE__)) );
		wp_enqueue_style( $this->prefix . 'bootstrap-datepicker-css' );

		wp_register_script( 'moment', plugins_url( 'js/moment.js', dirname(__FILE__)) );
		wp_enqueue_script( 'moment' );

		wp_register_script( $this->prefix . 'bootstrap-datepicker-js', plugins_url( 'js/bootstrap-datetimepicker.min.js', dirname(__FILE__)), array('moment') );
		wp_enqueue_script( $this->prefix . 'bootstrap-datepicker-js' );

		wp_register_script( $this->prefix . 'data-manager-settings-js', plugins_url( 'js/settings.js', dirname(__FILE__)), array($this->prefix . 'bootstrap-datepicker-js') );
		wp_enqueue_script( $this->prefix . 'data-manager-settings-js' );
		wp_localize_script( $this->prefix . 'data-manager-settings-js', 'nnr_data_manager_data' , array(
			'prefix'	=> $this->prefix,
		));

	}

	/**
	 * Display the name field
	 *
	 * @access public
	 * @return void
	 */
	function display_name( $name, $default = '', $help_text = null ) {

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		$code = '<!-- Name -->
		<div class="form-group">
			<label for="' . $this->prefix . 'name" class="col-sm-3 control-label">' . __('Name', $this->text_domain) . '</label>
			<div class="col-sm-9">
				<input class="form-control" id="' . $this->prefix . 'name" name="' . $this->prefix . 'name" value="' . (isset($name) ? esc_attr($name) : $default ) . '">' .
				$help_text .
			'</div>
		</div>';

		return $code;
	}

	/**
	 * Display the start date field
	 *
	 * @access public
	 * @return void
	 */
	function display_start_date( $start_date, $default = null, $help_text = null ) {

		if ( !isset($default) ) {
			$default = date('m/d/Y h:i A', strtotime(current_time('mysql')));
		}

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		$code = '<!-- Start Date -->
		<div class="form-group">
			<label for="' . $this->prefix . 'start-date" class="col-sm-3 control-label">' . __('Start Date', $this->text_domain) . '</label>
			<div class="col-sm-3">
				<div id="' . $this->prefix . 'start-datepicker" class="date input-group">
					<input id="' . $this->prefix . 'start-date" name="' . $this->prefix . 'start-date" type="text" class="' . $this->prefix . 'start-date form-control" value="' . ( isset($start_date) ? esc_attr($start_date) : $default ) . '"/>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>' .
				$help_text .
			'</div>
		</div>';

		return $code;

	}

	/**
	 * Display the end date field
	 *
	 * @access public
	 * @return void
	 */
	function display_end_date( $end_date, $default = null, $help_text = null ) {

		if ( !isset($default) ) {
			$default = date("m/d/Y h:i A", mktime(0, 0, 0, date("m"), date("d"), date("Y")+20));
		}

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		$code = '<!-- Start Date -->
		<div class="form-group">
			<label for="' . $this->prefix . 'end-date" class="col-sm-3 control-label">' . __('End Date', $this->text_domain) . '</label>
			<div class="col-sm-3">
				<div id="' . $this->prefix . 'end-datepicker" class="date input-group">
					<input id="' . $this->prefix . 'end-date" name="' . $this->prefix . 'end-date" type="text" class="' . $this->prefix . 'end-date form-control" value="' . ( isset($end_date) ? esc_attr($end_date) : $default ) . '"/>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>' .
				$help_text .
			'</div>
		</div>';

		return $code;

	}

	/**
	 * Display the name field
	 *
	 * @access public
	 * @return void
	 */
	function get_data() {

		return array(
			'name'			=> isset($_POST[$this->prefix . 'name']) ? $this->sanitize_value($_POST[$this->prefix . 'name']) : '',
			'start_date'	=> isset($_POST[$this->prefix . 'start-date']) ? $this->sanitize_value($_POST[$this->prefix . 'start-date']) : '',
			'end_date'		=> isset($_POST[$this->prefix . 'end-date']) ? $this->sanitize_value($_POST[$this->prefix . 'end-date']) : '',
		);

	}

}

endif;