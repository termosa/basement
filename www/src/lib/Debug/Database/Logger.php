<?php

class Debug_Database_Logger
{
	public static $log = array();

	public static function set( $query = NULL ) {
		$trace = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 2 );
		if ( is_null( $query ))
			$sql = $trace[1]['args'][0];
		elseif ( gettype($query) == 'object' && get_class( $query ))
			$sql = $query->queryString;
		else
			$sql = (string) $query;

		array_push( self::$log, array(
			'sql' => $sql,
			'line' => $trace[1]['line'],
			'file' => $trace[1]['file']));
	}
}