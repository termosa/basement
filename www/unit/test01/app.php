<?php

/**
 * Генерирует путь пригодный для использования в ссылках и при подключении модулей
 * @param  string $path    Путь к модулю. '.' или пустая строка - текущий модуль. '/' - ведет в корень приложения. '../../a' - подымается на два модуля выше и вызывает модуль 'a'. 'a/b' - вызывает модуль 'b', который является дочерним модулю 'a', который в свою очередь вложен в текущий модуль.
 * @param  string $current Корневой путь для пути к модулю. Должен начинаться с символа '/'. Может заканчиваться на '/' только если это весь путь.
 * @return string          Возвращает путь от корня приложения
 */
function parsePath( $path, $current ) {
	if ( strpos( $path, '/' ) === 0 ) // Все что начинается со слеша - выдается как есть
	 	return $path;

	if ( empty( $path ) || $path == '.' ) // Для пустых урлов и точек создаем рекурсию (оставляем существующий путь)
		return $current;

	if ( '/' == $current ) // Если текущий путь является корнем - просто возвращаем переданный путь от корня
		return '/' . $path;

	if ( strpos( $path, '..' ) === 0 ) { // Если речь идет о выходе на верхний уровень ...
		$break = false; // Нам понадобиться флажок для того чтобы выйти из цикла в нужный момент
		do {
			$current = substr( $current, 0, strrpos( $current, '/' )); // Подымаем текущий путь на один уровень

			if ( ! isset( $path{2} )) // Если в пути остались только две точки - возвращаем сгенерированный путь
				return $current;
			else // В противном случае срезаем вначале точки со слешем
				$path = substr( $path, 3 );

			if ( strpos( $path, '..' ) !== 0 ) // Если двоеточий больше нет - устанавливаем флагу значение true чтобы выйти из цикла
				$break = true;
		} while ( $break == false );
	}

	return substr( $current, 0, strrpos( $current, '/' ) + 1 ) . $path; // Убираем последний модуль в текущем пути и крепим переданный
}

if ( isset( $_GET['render'] ) && $_GET['render'] == 'browser' ) {
	var_dump(
		array( 'path' => '/', 'current' => '/a',
			'result' => parsePath( '/', '/a' )),
		array( 'path' => '/', 'current' => '/a/b',
			'result' => parsePath( '/', '/a/b' )),
		array( 'path' => '/c', 'current' => '/a',
			'result' => parsePath( '/c', '/a' )),
		array( 'path' => 'c', 'current' => '/a/b',
			'result' => parsePath( 'c', '/a/b' )),
		array( 'path' => 'c', 'current' => '/a/b',
			'result' => parsePath( 'c', '/a/b' )),
		array( 'path' => 'c/d', 'current' => '/a/b',
			'result' => parsePath( 'c/d', '/a/b' )),
		array( 'path' => '.', 'current' => '/a/b',
			'result' => parsePath( '.', '/a/b' )),
		array( 'path' => '.', 'current' => '/a/b',
			'result' => parsePath( '.', '/a/b' )),
		array( 'path' => '', 'current' => '/a/b',
			'result' => parsePath( '', '/a/b' )),
		array( 'path' => '', 'current' => '/a/b',
			'result' => parsePath( '', '/a/b' )),
		array( 'path' => 'a', 'current' => '/',
			'result' => parsePath( 'a', '/' )),
		array( 'path' => '..', 'current' => '/a/b/c',
			'result' => parsePath( '..', '/a/b/c' )),
		array( 'path' => '../d', 'current' => '/a/b/c',
			'result' => parsePath( '../d', '/a/b/c' )),
		array( 'path' => '../../e', 'current' => '/a/b/c/d',
			'result' => parsePath( '../../e', '/a/b/c/d' ))
	);
}