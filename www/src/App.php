<?php

global $_cfg;
$_cfg = array(
	'module' => array(
		'default' => 'site/home',
		'404' => 'error/404',
	),
	'template' => 'main',
);

function lib( $class_name, $return = false, $path = LIB_FOLDER ) {
	if ( ! class_exists( $class_name ) ) {
		$path .= '/' . implode( '/', explode( '_', $class_name ));
		include( $path . '.php' );
	}

	if ( $return ) return new $class_name;
}

/**
 * ROUTER
 */
global $_cfg, $_request;
$_request = '';
if ( isset( $_GET['r'] ) )
	$_request = $_GET['r'];
else
	$_request = $_cfg['module']['default'];

function drive( $path ) {
	global $_cfg;
	$path = '/' . $path;
	if ( ! file_exists( $controller = C_FOLDER . $path . '.php' ) )
		$controller = false;
	
	if ( ! file_exists( $view = V_FOLDER . $path . '.php' ) )
		$view = false;

	if ( ! ( $controller || $view )) {
		$path = '/' . $_cfg['module']['404'];
		$controller = C_FOLDER . $path . '.php';
		$view = V_FOLDER . $path . '.php';
	}

	return array( $controller, $view );
}

function lnk( $link = '/', $print = true ) {
	global $_request;
	if ( $link == '/' ) {
		$link = $_cfg['module']['default'];
	} elseif ( $link == '.' ) {
		$link = $_request;
	} elseif ( strpos( $link, '/' ) === 0 ) {
		$link = substr( $link, 1 );
	} else {
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

define('JUST_V', 3);
define('JUST_C', 1);

function run($module_name, $opt=NULL, $mode=2) {
	$return = NULL;
	list($controller, $view) = drive( $module_name );
	if ( $mode < 3 && $controller )
		$return = include $controller;

	if ( $mode > 1 && $view )
		include $view;

	return $return;
}