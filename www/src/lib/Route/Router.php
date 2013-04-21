<?php

/**
 * Местный роутер
 *
 * На этот класс возложена ответственность за обработку ссылок
 * и подключение файлов моделей, контроллеров, отображений и шаблонов
 */
class Route_Router
{
	public $request = ''; // Запрашиваемый модуль

	/**
	 * Инициализация роутера
	 *
	 * Определяем текущий модуль
	 */
	function __construct() {
		// Определяем куда хочет попасть пользователь
		if ( isset( $_GET['r'] ) )
			$this->request = $_GET['r'];
		else
			$this->request = App::$i->cfg['module']['default'];
	}

	/**
	 * Генератор модулей
	 *
	 * Ищет запрашиваемые файлы и включает их в создаемый модуль
	 *
	 * @param string $path Путь к файлам модуля
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 * @return Module Объект созданного модуля
	 */
	public function drive( $path, $opt = NULL ) {
		$path = '/' . $path;
		if ( ! file_exists( $controller = C_FOLDER . $path . '.php' ) )
			$controller = false;
		
		if ( ! file_exists( $view = V_FOLDER . $path . '.php' ) )
			$view = false;

		if ( ! ( $controller || $view )) { // Если не можем найти ни одного соответствующего запросу файла - посылаем на 404-ю страницу
			$path = '/' . App::$i->cfg['module']['404'];
			$controller = C_FOLDER . $path . '.php';
			$view = V_FOLDER . $path . '.php';
		}

		App::$i->lib( 'Module' );
		return new Module( $controller, $view, $opt );
	}

	/**
	 * Генератор ссылок
	 *
	 * Нужен для того чтобы при создании ссылок учитывать то,
	 * как роутер их обрабатывает.
	 *
	 * @param string $link Короткий формат сылки. К примеру: '.', '/', '/admin', 'edit' или 'nav/search'
	 * @param bool $print Если true - сразу выведет ссылку
	 * @return string Сгенерированная ссылка
	 */
	public static function link( $link = '/', $print = true ) {
		if ( $link == '/' ) { // Ссылка на корень сайта сошлеться на модуль по умолчанию
			$link = App::$i->cfg['module']['default'];
		} elseif ( $link == '.' ) { // Точка ссылается на текущую страницу
			$link = App::$i->router->request;
		} elseif ( strpos( $link, '/' ) === 0 ) { // Полная ссылка
			$link = substr( $link, 1 );
		} else { // Эти ссылки будут ссылаться на модули в одной директории или на модули в дочерних каталогах
			$r = App::$i->router->request;
			if ( $pos = strrpos( $r, '/' ) )
				$r = substr( $r, 0, $pos );
			$link = $r . '/' . $link;
		}

		$link = URL . '/index.php?r=' . $link;
		if ( $print )
			echo $link;
		return $link;
	}
}

/**
 * Обертка для Route_Router::link
 *
 * @param string $link Короткий формат сылки
 * @param bool $print Если true - сразу выведет ссылку
 * @return string Сгенерированная ссылка
 */
function lnk( $link = '/', $print = true ) {
	return Route_Router::link( $link, $print );
}