<?php

function run($module_name, $opt=NULL, $mode=2) {
	$return = NULL;
	$m = drive( $module_name );
	if ( $mode < 3 && $m->_controller )
		$return = include $m->_controller;

	if ( $mode > 1 && $m->_view )
		include $m->_view;

	return $return;
}

/**
 * Модуль
 *
 * Набор функционала для реализации контроллера и вида,
 * а также их иерархического вызова
 */
class Module
{
	const JUST_V = 3, // Запускать только вид
		JUST_C = 1; // Запускать только контроллер
	public $_template, // Шаблон применяющийся для обертки вида (если false - не применять)
		$_controller, // Путь к контроллеру (false - не использовать контроллер)
		$_view, // Путь к файлу вида (false - не использовать вид)
		$_parent; // Опции переданные от модуля-родителя
	private $_data = array(); // Перегруженные данные

	/**
	 * Настройка модуля
	 *
	 * Создает объект и задает ему пути к файлам контроллера и вида
	 *
	 * @param string|bool $controller Путь к файлу контроллера. Если false - контроллер использоваться не будет
	 * @param string|bool $view Пукть к файлу вида. Если false - вид использоваться не будет
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 */
	function __construct( $controller = false, $view = false, $opt = NULL ) {
		$this->_controller = $controller;
		$this->_view = $view;
		$this->_parent = $opt;
	}

	/**
	 * Запуск контроллера
	 */
	public function perform() {
		return include $this->_controller;
	}

	/**
	 * Запуск вида
	 */
	public function render() {
		include $this->_view;
	}

	/**
	 * Запуск модуля
	 *
	 * Запускает модуль и контролирует его выполнение
	 *
	 * @param string $module_name Название модуля
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 * @param int $mode Если 1 - выполняется только контроллер, если 3 - только вид, 2 - оба
	 * @return mixed Возвращает все, что вернет контроллер
	 */
	public function run( $opt = NULL, $mode = 2 ) {
		$return = NULL;
		if ( $mode < 3 && $this->_controller )
			$return = $this->perform();

		if ( $mode > 1 && $this->_view )
			$this->render();

		return $return;
	}

	/**
	 * Обертка для запуска дочернего модуля
	 * 
	 * Запускает на исполнение указанный модуль, но не выводит его,
	 * а возвращает в виде строки
	 *
	 * @param string $module_name Название модуля
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 * @param int $mode Если 1 - выполняется только контроллер, если 3 - только вид, 2 - оба
	 * @param string Данные которые вывел запущенный модуль
	 */
	// public function getModuleView( $module_name, $opt = NULL, $mode = 2 ) {
	// 	$oid = ob_start();
	// 	$this->runModule( $module_name, $opt, $mode );
	// 	return ob_get_clean( $oid );
	// }
	
	public function model( $class_name, $return = false ) {
		return lib( $class_name, $return, M_FOLDER );
	}

	// Добавляем возможность передавать переменные между controller и view через объект $this

	public function __set( $name, $value ) {
		$this->_data[ $name ] = $value;
	}

	public function __get( $name ) {
		if ( array_key_exists( $name, $this->_data ) )
			return $this->_data[ $name ];
		return NULL;
	}

	public function __isset( $name ) {
		return isset( $this->_data[ $name ] );
	}

	public function __unset( $name ) {
		unset( $this->_data[ $name ] );
	}
}