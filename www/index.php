<?php
require 'src/lib/Debug/Debugger.php';
require 'src/App.php';
inc('HTML_Helpers');
run($_request); // Запускаем приложение
// TODO: для маршрутов сделать возможность указывать родительскую папку (..)