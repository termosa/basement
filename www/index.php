<?php

require_once 'src/App.php';
// $d = inc('Debug_Debugger', true);
inc('HTML_Head');
inc('Route_FormAction');
run($_request); // Запускаем приложение