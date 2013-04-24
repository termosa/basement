<?php

// Объявление констант путей и ссылок
define('BASEPATH', dirname(__FILE__)); // Путь к папке исполняемого кода
define('URL', 'http://' . $_SERVER['HTTP_HOST']); // Адрес сайта
define('L_PATH', BASEPATH . '/lib'); // Папка контроллеров
define('C_PATH', BASEPATH . '/c'); // Папка контроллеров
define('M_PATH', BASEPATH . '/m'); // Папка моделей
define('V_PATH', BASEPATH . '/v'); // Папка видов
define('T_PATH', V_PATH . '/t'); // Папка шаблонов

// Объявление необходимых констант для упрощенной работы с фреймворком
define('JUST_V', 3);
define('JUST_C', 1);

// Объявление глобальных переменных фреймворка (сравнимо с настройками)
global $_request, $_template, $_db, $_runStack;
$_request = 'page/home'; // Выполняем запрос & запрос по умолчанию
$_template = 'main'; // Шаблон & шаблон по умолчанию
$_db = NULL; // Ячейка для адаптера баз данных
$_runStack = array('/'); // Стэк для запускаемых модулей

if (isset($_GET['r']) && ! empty($_GET['r']) && $_GET['r'] != '/')
		$_request = '/' . $_GET['r'];

function parsePath($path, $current) {
	if (strpos($path, '/') === 0)
		return substr($path, 1);

	$current = substr($current, 1);
	if ($path == '.')
		return  $current;

	if ($pos = strrpos($current, '/'))
		$current = substr($current, 0, $pos);
	$path = $current . '/' . $path;

	return $path;
}

function getLnk($link = '/', $get = array()) {
	global $_runStack, $_request;

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
	global $_runStack;

	$module = '/' . parsePath($module, $_runStack[count($_runStack)-1]);

	$return = NULL;
	if (! file_exists($controller = C_PATH . $module . '.php'))
		$controller = false;
	
	if (! file_exists($view = V_PATH . $module . '.php'))
		$view = false;

	if (! ($controller || $view)) {
		$module = '/error/404';
		$controller = C_PATH . $module . '.php';
		$view = V_PATH . $module . '.php';
	}

	array_push($_runStack, $module);

	if ($mode < 3 && $controller)
		$return = include $controller;

	if ($mode > 1 && $view)
		include $view;

	array_pop($_runStack);
	return $return;
}

function inc($class, $returnObj = false, $from = L_PATH) {
	if (! class_exists($class))
		$r = include ($from . '/' . implode('/', explode('_', $class)) . '.php');

	if ($returnObj)
		return new $class;
	return $r;
}

function incM($class, $returnObj = false) {
	return inc($class, $returnObj, M_PATH);
}