<?php

/**
 * Модуль
 *
 * Набор функционала для реализации контроллера и вида,
 * а также их иерархического вызова
 */
class Module
{
	private $_template, // Шаблон применяющийся для обертки вида (если false - не применять)
		$_controller, // Путь к контроллеру (false - не использовать контроллер)
		$_view; // Путь к файлу вида (false - не использовать вид)
	private $_data = array(); // Перегруженные данные

	/**
	 * Настройка модуля
	 *
	 * Создает объект и задает ему пути к файлам контроллера и вида
	 *
	 * @param string|bool $controller Путь к файлу контроллера. Если false - контроллер использоваться не будет
	 * @param string|bool $view Пукть к файлу вида. Если false - вид использоваться не будет
	 */
	function __construct( $controller = false, $view = false ) {
		$this->_controller = $controller;
		$this->_view = $view;
	}

	/**
	 * Запуск контроллера
	 */
	public function perform() {
		include $this->_controller;
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
	 */
	public function run() {
		if ( $this->_controller )
			$this->perform();

		if ( $this->_view )
			$this->render();
	}

	/**
	 * Запуск дочернего модуля
	 * 
	 * Запускает на исполнение указанный модуль
	 *
	 * @param string $module_name Название модуля
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 */
	public function runModule( $module_name, $opt = NULL ) {
		$module = App::$i->router->drive( $module_name, $opt )->run();
	}

	/**
	 * Обертка для запуска дочернего модуля
	 * 
	 * Запускает на исполнение указанный модуль, но не выводит его,
	 * а возвращает в виде строки
	 *
	 * @param string $module_name Название модуля
	 * @param mixed $opt Данные которые требуется передать в дочерний модуль
	 * @param string Данные которые вывел запущенный модуль
	 */
	public function getModule( $module_name, $opt = NULL ) {
		$oid = ob_start();
		$this->runModule( $module_name, $opt );
		return ob_get_clean( $oid );
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