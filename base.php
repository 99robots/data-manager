<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

// Check if class already exists

if (!class_exists("NNR_Data_Manager_Base_v1")):

/* ================================================================================
 *
 * Base is the base class for Data Manager to help with managing repetitive tasks
 *
 ================================================================================ */

class NNR_Data_Manager_Base_v1 {

	/**
	 * Mulit-byte Unserialize
	 *
	 * UTF-8 will screw up a serialized string
	 *
	 * @access private
	 * @param string
	 * @return string
	 */
	function mb_unserialize($string) {

		if ( !is_string($string) ) {
			return $string;
		}

	    $string2 = preg_replace_callback(
	        '!s:(\d+):"(.*?)";!s',
	        function($m){
	            $len = strlen($m[2]);
	            $result = "s:$len:\"{$m[2]}\";";
	            return $result;

	        },
	        $string);
	    return unserialize($string2);
	}

	/**
	 * Sanitize the input value
	 *
	 * @access public
	 * @param mixed $value
	 * @param mixed $html
	 * @return void
	 */
	function sanitize_value( $value, $html = false ) {
		return stripcslashes( sanitize_text_field( $value ) );
	}

}

endif;