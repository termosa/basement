<?php

class Debug_Database_Statement extends PDOStatement
{
	public static $_log = array();
	const NO_MAX_LENGTH = -1;

	protected $connection;
	protected $bound_params = array();

	protected function __construct(PDO $connection)
	{
		$this->connection = $connection;
	}

	public function bindParam($paramno, &$param, $type = PDO::PARAM_STR, $maxlen = null, $driverdata = null)
	{
		$this->bound_params[$paramno] = array(
			'value' => &$param,
			'type' => $type,
			'maxlen' => (is_null($maxlen)) ? self::NO_MAX_LENGTH : $maxlen,
			// ignore driver data
		);

		$result = parent::bindParam($paramno, $param, $type, $maxlen, $driverdata);
	}

	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
	{
		$this->bound_params[$parameter] = array(
			'value' => $value,
			'type' => $data_type,
			'maxlen' => self::NO_MAX_LENGTH
		);
		parent::bindValue($parameter, $value, $data_type);
	}

	public function getSQL($values = array())
	{
		$sql = $this->queryString;

		/**
		 * param values
		 */
		if (sizeof($values) > 0) {
			foreach ($values as $key => $value) {
				$sql = str_replace($key, $this->connection->quote($value), $sql);
			}
		}

		/**
		 * or already bounded values
		 */
		if (sizeof($this->bound_params)) {
			// pa($this->bound_params);
			foreach ($this->bound_params as $key => $param) {
				$value = $param['value'];
				if (!is_null($param['type'])) {
					$value = self::cast($value, $param['type']);
				}
				if ($param['maxlen'] && $param['maxlen'] != self::NO_MAX_LENGTH) {
					$value = self::truncate($value, $param['maxlen']);
				}
				if (!is_null($value)) {
					$sql = str_replace($key, $this->connection->quote($value), $sql);
				} else {
					$sql = str_replace($key, 'NULL', $sql);
				}
			}
		}
		return $sql;
	}

	static protected function cast($value, $type)
	{
		switch ($type) {
			case PDO::PARAM_BOOL:
				return (bool)$value;
				break;
			case PDO::PARAM_NULL:
				return null;
				break;
			case PDO::PARAM_INT:
				return (int)$value;
			case PDO::PARAM_STR:
			default:
				return $value;
		}
	}

	static protected function truncate($value, $length)
	{
		return substr($value, 0, $length);
	}

	public function execute( $bound_input_params = NULL ) {
		$query = parent::execute( $bound_input_params );
		$this->bound_params = $bound_input_params;
		Debug_Database_Logger::set($this);
		return $query;
	}

	public function showQuery()
	{
		$query = $this->queryString;
		$params = $this->bound_params;
		
		$keys = array();
		$values = array();
		
		# build a regular expression for each parameter
		foreach ($params as $key=>$value)
		{
			if (is_string($key))
			{
				$keys[] = '/:'.$key.'/';
			}
			else
			{
				$keys[] = '/[?]/';
			}
			
			if(is_numeric($value))
			{
				$values[] = intval($value);
			}
			else
			{
				$values[] = '"'.$value .'"';
			}
		}
		
		$query = preg_replace($keys, $values, $query, 1, $count);
		return $query;
	}
}