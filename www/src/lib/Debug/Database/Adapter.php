<?php

class Debug_Database_Adapter extends PDO
{
	public function query( $sql, $var2 = NULL, $var3 = NULL, $var4 = NULL ) {
		if ( ! is_null( $var4 ))
			$query = parent::query( $sql, $var2, $var3, $var4 );
		elseif ( ! is_null( $var3 ))
			$query = parent::query( $sql, $var2, $var3 );
		elseif ( ! is_null( $var2 ) )
			$query = parent::query( $sql, $var2 );
		else
			$query = parent::query( $sql );

		Debug_Database_Logger::set($query);
		return $query;
	}

	public function exec( $sql ) {
		Debug_Database_Logger::set( $sql );
		return parent::exec( $sql );
	}
}