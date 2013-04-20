<?php

// Настройка путей и ссылок
define( 'BASEPATH', dirname( __FILE__ ) . '/src' );
define( 'URL', 'http://' . $_SERVER['HTTP_HOST'] );
define( 'LIB_FOLDER', BASEPATH . '/lib' );
define( 'M_FOLDER', BASEPATH . '/m' );
define( 'V_FOLDER', BASEPATH . '/v' );
define( 'C_FOLDER', BASEPATH . '/c' );
define( 'T_FOLDER', V_FOLDER . '/template' );

// Запуск приложения
require_once BASEPATH . '/App.php';
App::init();






// Любопытство

// echo "<pre>";
// var_dump(App::$i);
