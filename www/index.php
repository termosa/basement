<?php

require_once 'src/App.php';
// $d = inc('Debug_Debugger', true);
inc('HTML_Helpers');
run($_request); // Запускаем приложение
// TODO: для маршрутов сделать возможность указывать родительскую папку (..)