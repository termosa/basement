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
// global $_request, $_template, $_db, $_runStack;
$_request = 'page/home'; // Выполняем запрос & запрос по умолчанию
$_template = 'main'; // Шаблон & шаблон по умолчанию
$_db = NULL; // Ячейка для адаптера баз данных
$_runStack = array('/'); // Стэк для запускаемых модулей

if (isset($_GET['r']) && ! empty($_GET['r']) && $_GET['r'] != '/')
		$_request = '/' . $_GET['r'];

// Подключает библиотеки в зендовском формате, может создать объект
function inc($class, $returnObj = false, $from = L_PATH) {
	$r = include_once $from . '/' . implode('/', explode('_', $class)) . '.php';

	if ($returnObj)
		return new $class;
	return $r;
}

// Обертка для inc() которая подключает модели
function incM($class, $returnObj = false) {
	return inc($class, $returnObj, M_PATH);
}

/**
 * Генерирует путь пригодный для использования в ссылках и при подключении модулей
 * @param  string $path    Путь к модулю. '.' или пустая строка - текущий модуль. '/' - ведет в корень приложения. '../../a' - подымается на два модуля выше и вызывает модуль 'a'. 'a/b' - вызывает модуль 'b', который является дочерним модулю 'a', который в свою очередь вложен в текущий модуль.
 * @param  string $current Корневой путь для пути к модулю. Должен начинаться с символа '/'. Может заканчиваться на '/' только если это весь путь.
 * @return string          Возвращает путь от корня приложения
 */
function parsePath( $path, $current ) {
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

function getLnk($link = '/', $get = array()) {
	global $_runStack;

	$link = parsePath($link, $_runStack[count($_runStack)-1]);
	if (empty($link))
		$link = '/';
	$link = URL . "/index.php?r=" . $link;

	foreach ($get as $key => $val)
		$link .= "&{$key}={$val}";

	return $link;
}

function lnk($link = '/', $get = array()) {
	$link = getLnk($link, $get);
	echo $link;
	return $link;
}

function run($module, $opt=NULL, $mode=2) {
	global $_runStack, $_template;
	$return = NULL;

	$module = '/' . parsePath($module, $_runStack[count($_runStack)-1]);

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