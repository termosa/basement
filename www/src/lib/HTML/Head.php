<?php

global $_head;
$_head = array('css'=>array('all'=>array(),'print'=>array()),
	'js'=>array()); // Хранилище стилей и скриптов для функции head

function headPrint() {
	global $_head;
	foreach ($_head['css'] as $media => $styles)
		foreach ($styles as $style)
			printf('<link rel="stylesheet" type="text/css" href="%s/i/style/%s.css" media="%s" />',
				URL, $style, $media);
	foreach ($_head['js'] as $script)
		printf('<script type="text/javascript" src="%s/i/script/%s.js"></script>',
			URL, $script);
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