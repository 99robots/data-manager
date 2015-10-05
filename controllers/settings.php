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
	 * text_domain
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	public $text_domain = '';

	/**
	 * Called when the object is first created
	 *
	 * @access public
	 * @param mixed $prefix
	 * @return void
	 */
	function __construct( $prefix = '', $text_domain = '' ) {

		do_action('nnr_data_man_before_new_settings_controller_v1');

		$this->prefix = $prefix;
		$this->text_domain = $text_domain;

		$this->include_scripts();

		do_action('nnr_data_man_after_new_settings_controller_v1');
	}

	/**
	 * Include all scripts needed for the settings
	 *
	 * @access public
	 * @return void
	 */
	function include_scripts() {

		do_action('nnr_data_man_before_settings_scripts_v1');

		wp_register_style( 'bootstrap-datepicker-css', plugins_url( 'css/bootstrap-datetimepicker.min.css', dirname(__FILE__)) );
		wp_enqueue_style( 'bootstrap-datepicker-css' );

		wp_register_script( 'moment', plugins_url( 'js/moment.js', dirname(__FILE__)) );
		wp_enqueue_script( 'moment' );

		wp_register_script( 'bootstrap-datepicker-js', plugins_url( 'js/bootstrap-datetimepicker.min.js', dirname(__FILE__)), array('jquery', 'moment') );
		wp_enqueue_script( 'bootstrap-datepicker-js' );

		wp_register_script( 'data-manager-settings-js', plugins_url( 'js/settings.js', dirname(__FILE__)), array('jquery', 'bootstrap-datepicker-js') );
		wp_enqueue_script( 'data-manager-settings-js' );
		wp_localize_script( 'data-manager-settings-js', 'nnr_data_manager_data' , apply_filters('nnr_data_man_settings_script_data_v1', array(
			'prefix'	=> $this->prefix,
		)));

		do_action('nnr_data_man_after_settings_scripts_v1');

	}

	/**
	 * Display the Data Manager Settings
	 *
	 * @access public
	 * @param mixed $data_settings
	 * @param string $args (default: array('default' => array())
	 * @param array 'help-text' (default: > array()))
	 * @return void
	 */
	function display_all_settings( $data_settings, $args = array('default' => array(), 'help-text' => array()) ) {

		do_action('nnr_data_man_before_settings_display_all_v1');

		echo $this->display_name($data_settings['name']);
		echo $this->display_start_date($data_settings['start_date']);
		echo $this->display_end_date($data_settings['end_date']);

		do_action('nnr_data_man_after_settings_display_all_v1');
	}

	/**
	 * Display the name field
	 *
	 * @access public
	 * @return void
	 */
	function display_name( $name, $default = '', $help_text = null, $format = 'inline' ) {

		do_action('nnr_data_man_before_settings_name_v1');

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		if ( $format == 'inline' ) {
			$code = '<!-- Name -->
			<div class="form-group">
				<label for="' . $this->prefix . 'name" class="col-sm-3 control-label">' . __('Name', $this->text_domain) . '</label>
				<div class="col-sm-9">
					<input class="form-control" id="' . $this->prefix . 'name" name="' . $this->prefix . 'name" value="' . (isset($name) ? esc_attr($name) : $default ) . '">' .
					$help_text .
				'</div>
			</div>';
		} else {
			$code = '<!-- Name -->
			<div class="nnr-block-group">
				<label for="' . $this->prefix . 'name" class="control-label">' . __('Name', $this->text_domain) . '</label>
				<input class="form-control" id="' . $this->prefix . 'name" name="' . $this->prefix . 'name" value="' . (isset($name) ? esc_attr($name) : $default ) . '">' . $help_text .
			'</div>';
		}

		do_action('nnr_data_man_after_settings_name_v1');

		return apply_filters('nnr_data_man_settings_name_v1', $code);
	}

	/**
	 * Display the start date field
	 *
	 * @access public
	 * @return void
	 */
	function display_start_date( $start_date, $default = null, $help_text = null, $format = 'inline' ) {

		do_action('nnr_data_man_before_settings_start_date_v1');

		if ( !isset($default) ) {
			$default = date('m/d/Y h:i A', strtotime(current_time('mysql')));
		}

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		if ( $format == 'inline' ) {
			$code = '<!-- Start Date -->
			<div class="form-group">
				<label for="' . $this->prefix . 'start-date" class="col-sm-3 control-label">' . __('Start Date', $this->text_domain) . '</label>
				<div class="col-sm-9">
					<div id="' . $this->prefix . 'start-datepicker" class="date input-group">
						<input id="' . $this->prefix . 'start-date" name="' . $this->prefix . 'start-date" type="text" class="' . $this->prefix . 'start-date form-control" value="' . ( isset($start_date) ? esc_attr($start_date) : $default ) . '"/>
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>' .
					$help_text .
				'</div>
			</div>';
		} else {
			$code = '<!-- Start Date -->
			<div class="nnr-block-group">
				<label for="' . $this->prefix . 'start-date" class="control-label">' . __('Start Date', $this->text_domain) . '</label>
				<div id="' . $this->prefix . 'start-datepicker" class="date input-group">
						<input id="' . $this->prefix . 'start-date" name="' . $this->prefix . 'start-date" type="text" class="' . $this->prefix . 'start-date form-control" value="' . ( isset($start_date) ? esc_attr($start_date) : $default ) . '"/>
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>' .
				$help_text .
			'</div>';
		}

		do_action('nnr_data_man_after_settings_start_date_v1');

		return apply_filters('nnr_data_man_settings_start_date_v1', $code);

	}

	/**
	 * Display the end date field
	 *
	 * @access public
	 * @return void
	 */
	function display_end_date( $end_date, $default = null, $help_text = null, $format = 'inline' ) {

		do_action('nnr_data_man_before_settings_end_date_v1');

		if ( !isset($default) ) {
			$default = date("m/d/Y h:i A", mktime(0, 0, 0, date("m"), date("d"), date("Y")+20));
		}

		if ( isset($help_text) ) {
			$help_text = '<em class="help-block">' . __($help_text, $this->text_domain) . '</em>';
		}

		if ( $format == 'inline' ) {
			$code = '<!-- End Date -->
			<div class="form-group">
				<label for="' . $this->prefix . 'end-date" class="col-sm-3 control-label">' . __('End Date', $this->text_domain) . '</label>
				<div class="col-sm-9">
					<div id="' . $this->prefix . 'end-datepicker" class="date input-group">
						<input id="' . $this->prefix . 'end-date" name="' . $this->prefix . 'end-date" type="text" class="' . $this->prefix . 'end-date form-control" value="' . ( isset($end_date) ? esc_attr($end_date) : $default ) . '"/>
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>' .
					$help_text .
				'</div>
			</div>';
		} else {
			$code = '<!-- End Date -->
			<div class="nnr-block-group">
				<label for="' . $this->prefix . 'end-date" class="control-label">' . __('End Date', $this->text_domain) . '</label>
				<div id="' . $this->prefix . 'end-datepicker" class="date input-group">
					<input id="' . $this->prefix . 'end-date" name="' . $this->prefix . 'end-date" type="text" class="' . $this->prefix . 'end-date form-control" value="' . ( isset($end_date) ? esc_attr($end_date) : $default ) . '"/>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>' .
				$help_text .
			'</div>';
		}

		do_action('nnr_data_man_after_settings_end_date_v1');

		return apply_filters('nnr_data_man_settings_end_date_v1', $code);

	}

	/**
	 * Display the name field
	 *
	 * @access public
	 * @return void
	 */
	function get_data() {

		return apply_filters('nnr_data_man_settings_get_data_v1', array(
			'name'			=> isset($_POST[$this->prefix . 'name']) ? $this->sanitize_value($_POST[$this->prefix . 'name']) : '',
			'start_date'	=> isset($_POST[$this->prefix . 'start-date']) ? $this->sanitize_value($_POST[$this->prefix . 'start-date']) : '',
			'end_date'		=> isset($_POST[$this->prefix . 'end-date']) ? $this->sanitize_value($_POST[$this->prefix . 'end-date']) : '',
		));

	}

}

endif;