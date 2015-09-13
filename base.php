<?php

/* ================================================================================
 *
 * Base is the base class for Data Manager to help with managing repetitive tasks
 *
 ================================================================================ */

class NNR_Data_Manager_Base {

	/**
	 * Mulit-byte Unserialize
	 *
	 * UTF-8 will screw up a serialized string
	 *
	 * @access private
	 * @param string
	 * @return string
	 */
	public static function mb_unserialize($string) {

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

}