<?php

/**
 * Возвращает пригодную для использования ссылку
 * @param  string $link Ссылка. Может указываться от корня ('/'), или относительный путь. Если вначале будет '~', то путь будет рассчитываться от запущенного модуля.
 * @param  array  $get  Массив для передачи GET-запроса
 * @return string       Возвращает сгенерированную ссылку
 */
function getLnk( $link = '/', $get = array() )
{
	$getString = '';
	if ( count( $get )) { // Создаем GET-запрос при наличии необходимых ресурсов
		$getString = array();
		foreach ( $get as $key => $val )
			array_push( $getString, "{$key}={$val}" );
		$getString = '?' . implode( '&', $getString );
	}

	if ( $link != '/' ) { // Если ссылка указывает на корень сайта - то никаких процедур выполнять не будем, тут все просто
		if (( ! empty( $link )) && '~' == $link{0} ) { // Если ссылка не пуста и она начинается с символа '~' - рассчитываем путь от модуля ($_runStack)
			global $_runStack;
			$current = $_runStack[ count( $_runStack ) - 1 ]; // Получаем текущий модуль
			if ( ! isset( $link{1} )) // Если ссылка состоит только из символа '~' - возвращаем ссылку на текущий модуль
				return URL . $current . $getString;
			$link = substr( $link, 2 ); // Стираем '~/' с начала строки
		} else {
			global $_query;
			if ( empty( $link )) // Если ссылка пуста - просто возвращаем текущую ссылку
				return URL . $_query . $getString;
			$current = $_query;
		}

		$link = parsePath( $link, $current );
	}

	$link = URL . $link . $getString;

	return $link;
}