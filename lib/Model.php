<?php

Abstract class Model {

	protected static $_PDO = null;

	protected static $_transactionCounter = 0;

	public static function setPDO( PDO $pdo ) {
		static::$_PDO = $pdo;
	}

	protected static function assertPDO() {
		if( is_null(static::$_PDO) )
			Throw new Exception("PDO connection has not been established");
	}

	public static function startTransaction() {
		return static::$_PDO->beginTransaction();
	}

	public static function commitTransaction() {
		return static::$_PDO->commit();
	}

	public static function rollbackTransaction() {
			return static::$_PDO->rollBack();
	}

	public static function find_by( array $criteria ) {
		static::assertPDO();

		if( get_called_class() == __CLASS__ )
			Throw new Exception("Model::find_by() must be called from a subclass of model");

		$valuePairs = array();
		$conditions = array();

		$properties = array_keys( static::$_properties );
		foreach( $criteria as $key => $value ) {
			if( !in_array($key, $properties))
				continue;
			$valuePairs[ $key ] = $value;
			$conditions[]       = "{$key} = :{$key}";
		}

		if( !count($conditions) )
			Throw new Exception("Model::find_by() expects at least one valid critera to query on");
		$conditions = implode(' AND ', $conditions );

		$table     = static::$_table;
		$statement = "SELECT * FROM {$table} WHERE {$conditions}";
		$query     = static::$_PDO->prepare( $statement ); //
		$query->execute( $valuePairs );
		
		$result = $query->fetchAll();
		$rows   = array();
		$class  = get_called_class();
		
		foreach( $result as $rowData ) {
			$row = new $class;
			$row->inflate( $rowData );
			$row->_exists = true;

			$rows[] = $row;
		}

		return $rows;
	}

	protected $_data   = array();
	protected $_exists = false;

	public function __get( $key ) {
		if( !key_exists($key, static::$_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		return $this->_data[ $key ];
	}

	public function __set( $key, $value ) {
		if( !key_exists($key, static::$_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		$this->_data[ $key ] = $value;
	}

	public function save() {
		static::assertPDO();
		return ( $this->_exists ) ? $this->update() : $this->insert() ;
	}

	private function insert() {

		$valuePairs   = array();
		$placeholders = array();

		foreach( $this->_data as $key => $value ) {
			// Don't set the primary key
			if( in_array('pk', static::$_properties[$key]) ) {
				continue;
			}

			$valuePairs[$key] = $value;
			$placeholders[]   = ":{$key}";
		}

		$table        = static::$_table;
		$fields       = implode(', ', array_keys($valuePairs) );
		$placeholders = implode(', ', $placeholders );
		$statement = "INSERT INTO {$table} ( {$fields} ) VALUES ( $placeholders );";

		$query  = static::$_PDO->prepare( $statement );
		$result = $query->execute( $valuePairs );

		if( $result ) {
			$primaryKey = null;

			foreach( static::$_properties as $field => $attributes ) {
				if( in_array('pk', $attributes) )
					$primaryKey = $field;
			}

			if( $primaryKey ) {
				$this->$primaryKey = static::$_PDO->lastInsertId();
				$this->_exists     = true;
			}
		}
	}

	protected function update() {

		$valuePairs   = array();
		$placeholders = array();
		$primaryKey   = null;

		foreach( $this->_data as $key => $value ) {
			// Don't set the primary key
			if( in_array('pk', static::$_properties[$key]) ) {
				$primaryKey = "{$key}=" . $this->_data[$key];
				continue;
			}

			$valuePairs[$key] = $value;
			$placeholders[]   = "{$key}=:{$key}";
		}

		$table        = static::$_table;
		$placeholders = implode(', ', $placeholders );
		$statement = "UPDATE {$table} SET $placeholders WHERE {$primaryKey};";

		$query  = static::$_PDO->prepare( $statement );
		$result = $query->execute( $valuePairs );
		
		return $result;
	}

	protected function inflate( $data ) {
		$this->_data = $data;
	}


	
}