<?php

// Настройка путей и ссылок
define( 'BASEPATH', dirname( __FILE__ ) . '/src' );
define( 'URL', 'http://' . $_SERVER['HTTP_HOST'] );
define( 'LIB_FOLDER', BASEPATH . '/lib' );
define( 'M_FOLDER', BASEPATH . '/m' );
define( 'V_FOLDER', BASEPATH . '/v' );
define( 'C_FOLDER', BASEPATH . '/c' );
define( 'T_FOLDER', V_FOLDER . '/template' );
define( 'CFG_FOLDER', BASEPATH . '/cfg' );

chdir( BASEPATH );


require_once 'App.php';
$__debugger = lib( 'Debug_Debugger', true ); // Подключаем дебаггер
run( $_request ); // Запускаем приложение



$start = memory_get_usage();

$a = NULL;
$a = 'a';
$a = 'abcdefgh';
$a = 'abcdefghijklmnop';
// $a = str_repeat('Hello',1);

echo "<pre>";
var_dump(memory_get_usage() - $start);