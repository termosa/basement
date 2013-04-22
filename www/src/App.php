<?php
global $_cfg;
/**
 * Центральный класс
 *
 * Запускает приложение. Хранит в себе основной арсенал данных и методов:
 * настройки, часто используемые библиотеки, функцию подключения библиотек.
 * В этом классе НЕ реализован паттерн Singleton, но к классу
 * нужно обращаться так, буд-то он реализован (self::$i)
 */
$_cfg = array( // Настройки по умолчанию
	'module' => array(
		'default' => 'site/home', // Модуль запускающийся при отсутствии запроса к конкретному модулю
		'404' => 'error/404', // Модуль который вызывается если роутер не может найти нужный модуль
	),
	'template' => 'main', // Шаблон по умолчанию
);
$router; // Объект роутера

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
function lib( $class_name, $return = false, $path = LIB_FOLDER ) {
	if ( ! class_exists( $class_name ) ) { // Проверяем есть ли необходимость подключать библиотеку
		$path .= '/' . implode( '/', explode( '_', $class_name )); // Создаем путь к файлу с вызванным классом
		include( $path . '.php' );
	}

	if ( $return ) return new $class_name;
}

/**
 * Инициализация приложения
 * 
 * Создает объект приложения и передает ему настройки (вместо __construct)
 *
 * @param array $cfg Конфигурации заменяющие настройки по умолчанию
 * @return App Возвращает объект приложения
 */
// $_cfg = array_merge( $_cfg, $cfg ); // Обновляем настройки


/**
 * ROUTER
 */

global $_cfg, $_request;
$_request = ''; // Запрашиваемый модуль
if ( isset( $_GET['r'] ) )
	$_request = $_GET['r'];
else
	$_request = $_cfg['module']['default'];

/**
 * Генератор модулей
 *
 * Ищет запрашиваемые файлы и включает их в создаемый модуль
 *
 * @param string $path Путь к файлам модуля
 * @param mixed $opt Данные которые требуется передать в дочерний модуль
 * @return Module Объект созданного модуля
 */
function drive( $path ) {
	global $_cfg;
	$path = '/' . $path;
	if ( ! file_exists( $controller = C_FOLDER . $path . '.php' ) )
		$controller = false;
	
	if ( ! file_exists( $view = V_FOLDER . $path . '.php' ) )
		$view = false;

	if ( ! ( $controller || $view )) { // Если не можем найти ни одного соответствующего запросу файла - посылаем на 404-ю страницу
		$path = '/' . $_cfg['module']['404'];
		$controller = C_FOLDER . $path . '.php';
		$view = V_FOLDER . $path . '.php';
	}

	return array( $controller, $view );
}

/**
 * Генератор ссылок
 *
 * Нужен для того чтобы при создании ссылок учитывать то,
 * как роутер их обрабатывает.
 *
 * @param string $link Короткий формат сылки. К примеру: '.', '/', '/admin', 'edit' или 'nav/search'
 * @param bool $print Если true - сразу выведет ссылку
 * @return string Сгенерированная ссылка
 */
function lnk( $link = '/', $print = true ) {
	global $_request;
	if ( $link == '/' ) { // Ссылка на корень сайта сошлеться на модуль по умолчанию
		$link = $_cfg['module']['default'];
	} elseif ( $link == '.' ) { // Точка ссылается на текущую страницу
		$link = $_request;
	} elseif ( strpos( $link, '/' ) === 0 ) { // Полная ссылка
		$link = substr( $link, 1 );
	} else { // Эти ссылки будут ссылаться на модули в одной директории или на модули в дочерних каталогах
		$r = $_request;
		if ( $pos = strrpos( $r, '/' ) )
			$r = substr( $r, 0, $pos );
		$link = $r . '/' . $link;
	}

	$link = URL . '/index.php?r=' . $link;
	if ( $print )
		echo $link;
	return $link;
}


/**
 * Модуль
 *
 * Набор функционала для реализации контроллера и вида,
 * а также их иерархического вызова
 */
define('JUST_V', 3); // Запускать только вид
define('JUST_C', 1); // Запускать только контроллер

/**
 * Запуск модуля
 *
 * Запускает модуль и контролирует его выполнение
 *
 * @param string $module_name Название модуля
 * @param mixed $opt Данные которые требуется передать в дочерний модуль
 * @param int $mode Если 1 - выполняется только контроллер, если 3 - только вид, 2 - оба
 * @return mixed Возвращает все, что вернет контроллер
 */
function run($module_name, $opt=NULL, $mode=2) {
	$return = NULL;
	list($controller, $view) = drive( $module_name );
	if ( $mode < 3 && $controller )
		$return = include $controller;

	if ( $mode > 1 && $view )
		include $view;

	return $return;
}