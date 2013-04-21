<?php

/**
 * Центральный класс
 *
 * Запускает приложение. Хранит в себе основной арсенал данных и методов:
 * настройки, часто используемые библиотеки, функцию подключения библиотек.
 * В этом классе НЕ реализован паттерн Singleton, но к классу
 * нужно обращаться так, буд-то он реализован (self::$i)
 */
class App
{
	public $cfg = array( // Настройки по умолчанию
		'module' => array(
			'default' => 'site/home', // Модуль запускающийся при отсутствии запроса к конкретному модулю
			'404' => 'error/404', // Модуль который вызывается если роутер не может найти нужный модуль
		),
		'template' => 'main', // Шаблон по умолчанию
	);
	public $router; // Объект роутера

	/**
	 * Подключение библиотек
	 *
	 * Этот метод служит для подключения библиотек и модулей
	 *
	 * @param string $class_name Имя класса который нужно подключить
	 * @param bool $return Если true - создаст и вернет объект запрашиваемого класса
	 * @param string $path Папка из которой будет подключаться класс
	 * @return class Если $return установлен в true - вернет объект созданного класса, если в false - ничего не вернет
	 */
	public static function lib( $class_name, $return = false, $path = LIB_FOLDER ) {
		if ( ! class_exists( $class_name ) ) { // Проверяем есть ли необходимость подключать библиотеку
			$p = explode( '_', $class_name ); // Отделяем путь от имени класса
			foreach ($p as $elm)
				$path .= '/' . $elm; // Создаем путь к файлу с нашим классом
			include( $path . '.php' );
		}

		if ( $return ) return new $class_name;
	}

	// Дальше реализация паттерна Singleton

	public static $i = NULL; // Уникальный объект класса
	
	/**
	 * Инициализация приложения
	 * 
	 * Производит запуск приложения и его настройку (вместо __construct)
	 *
	 * @param array $cfg Конфигурации заменяющие настройки по умолчанию
	 */
	public function init( $cfg = array() ) {
		self::$i = new self;
		self::$i->cfg = array_merge( self::$i->cfg, $cfg ); // Обновляем настройки
		self::$i->router = self::$i->lib( 'Route_Router', true ); // Создаем объект роутера
		self::$i->router->drive( self::$i->router->request )->run(); // Запускаем HMVC!
		
		return self::$i;
	}
}