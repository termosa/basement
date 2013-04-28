<?php

// Объявление констант путей и ссылок
define('PUBLIC', $_SERVER['DOCUMENT_ROOT']); // Публичная папка
define('URL', 'http://' . $_SERVER['HTTP_HOST']); // Адрес сайта
define('BASEPATH', dirname(__FILE__)); // Путь к папке исполняемого кода
define('L_PATH', BASEPATH . '/lib'); // Папка контроллеров
define('C_PATH', BASEPATH . '/c'); // Папка контроллеров
define('M_PATH', BASEPATH . '/m'); // Папка моделей
define('V_PATH', BASEPATH . '/v'); // Папка видов
define('T_PATH', V_PATH . '/_t'); // Папка шаблонов

// Объявление необходимых констант для упрощенной работы с фреймворком
define('JUST_V', 3);
define('JUST_C', 1);

// Объявление глобальных переменных фреймворка (сравнимо с настройками)
// global $_query, $_template, $_db, $_runStack;
$_query = '/page/home'; // Выполняем запрос & запрос по умолчанию
$_template = 'main'; // Шаблон & шаблон по умолчанию
$_db = NULL; // Ячейка для адаптера баз данных
$_runStack = array('/'); // Стэк для запускаемых модулей

// if (isset($_GET['r']) && ! empty($_GET['r']) && $_GET['r'] != '/')
// 	$_query = '/' . $_GET['r'];
if ('/' != $_SERVER['REQUEST_URI']) {
	if ($get = strpos($_SERVER['REQUEST_URI'], '?'))
		$_query = substr($_SERVER['REQUEST_URI'], 0, $get);
	else
		$_query = $_SERVER['REQUEST_URI'];
}

/**
 * Подключает файлы
 * @param  string  $class     Имя класса в формате зенда (Html_Form_DropDown - подключит файл Html/Form/DropDown.php)
 * @param  boolean $returnObj Если установлен в true - создаст объект класса с именем аналогичным имени запрашиваемого файла и вернет его
 * @param  string  $from      Путь с которого нужно подключать файл. По умолчанию - стандартная папка для библиотек
 * @return mixed              Если $returnObj установлен в true - вернет объект класса, а если в false - то вернет то, что вернул подключенный файл
 */
function inc($class, $returnObj = false, $from = L_PATH)
{
	$return = include_once $from . '/' . implode('/', explode('_', $class)) . '.php'; // Генерируем путь к файлу и тут же его подключаем
	if ($returnObj)
		return new $class;
	return $return;
}

/**
 * Подключает модели
 * @param  string  $class     Имя класса в формате зенда (Profile_Edit - подключит файл Profile/Edit.php)
 * @param  boolean $returnObj Если установлен в true - создаст объект класса с именем аналогичным имени запрашиваемого файла и вернет его
 * @return mixed              Если $returnObj установлен в true - вернет объект класса, а если в false - то вернет то, что вернул подключенный файл
 */
function incM($class, $returnObj = false)
{
	return inc($class, $returnObj, M_PATH);
}

/**
 * Генерирует путь пригодный для использования в ссылках и при подключении модулей
 * @param  string $path    Путь к модулю. '.' или пустая строка - текущий модуль. '/' - ведет в корень приложения. '../../a' - подымается на два модуля выше и вызывает модуль 'a'. 'a/b' - вызывает модуль 'b', который является дочерним модулю 'a', который в свою очередь вложен в текущий модуль.
 * @param  string $current Корневой путь для пути к модулю. Должен начинаться с символа '/'. Может заканчиваться на '/' только если это весь путь.
 * @return string          Возвращает путь от корня приложения
 */
function parsePath( $path, $current )
{
	if ( strpos( $path, '/' ) === 0 ) // Все что начинается со слеша - выдается как есть
	 	return $path;

	if ( empty( $path ) || $path == '.' ) // Для пустых урлов и точек создаем рекурсию (оставляем существующий путь)
		return $current;

	if ( '/' == $current ) // Если текущий путь является корнем - просто возвращаем переданный путь от корня
		return '/' . $path;

	if ( strpos( $path, '..' ) === 0 ) { // Если речь идет о выходе на верхний уровень ...
		$break = false; // Нам понадобиться флажок для того чтобы выйти из цикла в нужный момент
		do {
			$current = substr( $current, 0, strrpos( $current, '/' )); // Подымаем текущий путь на один уровень

			if ( ! isset( $path{2} )) // Если в пути остались только две точки - возвращаем сгенерированный путь
				return $current;
			else // В противном случае срезаем вначале точки со слешем
				$path = substr( $path, 3 );

			if ( strpos( $path, '..' ) !== 0 ) // Если двоеточий больше нет - устанавливаем флагу значение true чтобы выйти из цикла
				$break = true;
		} while ( $break == false );
	}

	return substr( $current, 0, strrpos( $current, '/' ) + 1 ) . $path; // Убираем последний модуль в текущем пути и крепим переданный
}

/**
 * Возвращает пригодную для использования ссылку
 * @param  string $link Ссылка. Может указываться от корня ('/'), или относительный путь. Если вначале будет '~', то путь будет рассчитываться от запущенного модуля.
 * @param  array  $get  Массив для передачи GET-запроса
 * @return string       Возвращает сгенерированную ссылку
 */
function getLnk( $link = '/', $get = array() )
{
	$getString = '';
	if ( count( $get )) { // Создаем GET-запрос при наличии необходимых ресурсов
		$getString = array();
		foreach ( $get as $key => $val )
			array_push( $getString, "{$key}={$val}" );
		$getString = '?' . implode( '&', $getString );
	}

	if ( $link != '/' ) { // Если ссылка указывает на корень сайта - то никаких процедур выполнять не будем, тут все просто
		if (( ! empty( $link )) && '~' == $link{0} ) { // Если ссылка не пуста и она начинается с символа '~' - рассчитываем путь от модуля ($_runStack)
			global $_runStack;
			$current = $_runStack[ count( $_runStack ) - 1 ]; // Получаем текущий модуль
			if ( ! isset( $link{1} )) // Если ссылка состоит только из символа '~' - возвращаем ссылку на текущий модуль
				return URL . $current . $getString;
			$link = substr( $link, 2 ); // Стираем '~/' с начала строки
		} else {
			global $_query;
			if ( empty( $link )) // Если ссылка пуста - просто возвращаем текущую ссылку
				return URL . $_query . $getString;
			$current = $_query;
		}

		$link = parsePath( $link, $current );
	}

	$link = URL . $link . $getString;

	return $link;
}

function lnk( $link = '/', $get = array() )
{
	$link = getLnk($link, $get);
	echo $link;
	return $link;
}

function run($module, $opt=NULL, $mode=2)
{
	global $_runStack, $_template;
	$return = NULL;

	$module = parsePath($module, $_runStack[count($_runStack)-1]);

	if (count($_runStack) == 1 && strpos($module, '/_') !== false) {
		$controller = false;
		$view = false;
	} else {
		if (! file_exists($controller = C_PATH . $module . '.php'))
			$controller = false;
		
		if (! file_exists($view = V_PATH . $module . '.php'))
			$view = false;
	}

	if (! ($controller || $view)) {
		$module = '/error/404';
		$controller = C_PATH . $module . '.php';
		$view = V_PATH . $module . '.php';
	}

	array_push($_runStack, $module);

	$template = $_template;
	$_template = false;
	ob_start();
	// -
		if ($mode < 3 && $controller)
			$return = include $controller;

		if ($mode > 1 && $view)
			include $view;
	// -
	$content = ob_get_contents();
	ob_end_clean();
	if ($template)
		include T_PATH . '/' . $template . '.php';
	else
		echo $content;

	array_pop($_runStack);
	return $return;
}