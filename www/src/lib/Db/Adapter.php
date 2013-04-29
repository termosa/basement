<?php

inc('Debug_Database_Adapter'); // TODO: [РЕЛИЗ] удалить строку

/**
 * Адаптер базы данных
 */
class Db_Adapter extends PDO
{
	private static $cfg = NULL, // Настройки соединения с базами данных
		$db = array(); // Объекты класса PDO для всех БД

	/**
	 * Раздатчик PDO объектов
	 *
	 * @param  string $tag    Название сектора в ini-файле настроек соединения с базой данных
	 * @param  bool   $update Если true и объект PDO для переданного тега уже существует, то он заменится новым объектом
	 * @return PDO            Возвратит созданный объект соединения с базой данных
	 */
	public static function getPDO( $tag = 'default', $update = false )
	{
		if ( is_null( self::$cfg )) // Если мы здесь в первый раз - загружаем настройки
			self::$cfg = parse_ini_file( BASEPATH . '/cfg/database.ini', true );

		if ( ! isset( self::$db[ $tag ] ))
			self::$db[$tag] = new Debug_Database_Adapter( self::$cfg[ $tag ]['connection'], // TODO: [РЕЛИЗ] Заменить класс на PDO
				self::$cfg[ $tag ]['username'],
				self::$cfg[ $tag ]['password']
				);

		self::$db[ $tag ]->setAttribute( PDO::ATTR_STATEMENT_CLASS, // TODO: [РЕЛИЗ] удалить строку
			array( 'Debug_Database_Statement', array(self::$db[ $tag ]) )); // TODO: [РЕЛИЗ] удалить строку

		return self::$db[ $tag ];
	}
}