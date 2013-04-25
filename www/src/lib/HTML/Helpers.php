<?php

global $_helpers;
$_head = array( // Хранилище стилей и скриптов для функции head
	'css'=>array(
		'all'=>array(), // Для media="all"
		'print'=>array()), // Для media="print"
	'js'=>array(),
	'style'=>'', // Стили в шапку (без файла)
	'script'=>''); // Скрипты в шапку (без файла)
define('IMG_DIR', URL . '/i/image/');
define('JS_DIR', URL . '/i/script/');
define('CSS_DIR', URL . '/i/style/');

function getStyleTag($style, $media = 'all') {
	return sprintf('<link rel="stylesheet" type="text/css" href="%s%s.css" media="%s" />',
		CSS_DIR, $style, $media);
}

function getScriptTag($script) {
	return sprintf('<script type="text/javascript" src="%s%s.js"></script>',
		JS_DIR, $script);
}

function headPrint() {
	global $_head;
	foreach ($_head['css'] as $media => $styles)
		foreach ($styles as $style)
			echo getStyleTag($style, $media);
	foreach ($_head['js'] as $script)
		echo getScriptTag($script);
	if (! empty($_head['style']))
		echo '<style type="text/css">', $_head['style'], '</style>';
	if (! empty($_head['script']))
		echo '<script type="text/javascript">', $_head['script'], '</script>';
}

function head() {
	global $_head;
	$args = func_get_args();
	$type = array_shift($args);
	if ('js' == $type)
		foreach ($args as $script)
			array_push($_head['js'], $script);
	else
		foreach ($args as $style)
			array_push($_head['css'][$type], $style);
}

function addStyle($style) {
	global $_head;
	if (is_array($style)) {
		$styleBuffer = '';
		foreach ($style as $selector => $properties) {
			$styleBuffer .= $selector . '{';
			foreach ($properties as $selector => $property)
				$styleBuffer .= $selector . ':' . $property . ';';
			$styleBuffer .= '}';
		}
		$_head['style'] .= $styleBuffer;
	} else
		$_head['style'] .= $style;
}

function addScript($script) {
	global $_head;
	$_head['script'] .= $script;
}