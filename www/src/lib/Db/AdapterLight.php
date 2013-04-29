<?php

inc('Debug_Database_Adapter'); // TODO: УДАЛИТЬ

global $_db;

function getDbAdapter()
{
	global $_db;

	if ( is_null( $_db )) {
		$_db = new Debug_Database_Adapter( // TODO: [РЕЛИЗ] Заменить класс на PDO
			'sqlite::memory:',
			'', '');

		$_db->setAttribute( PDO::ATTR_STATEMENT_CLASS, // TODO: [РЕЛИЗ] удалить строку
			array( 'Debug_Database_Statement', array( $_db ))); // TODO: [РЕЛИЗ] удалить строку
	}

	return $_db;
}