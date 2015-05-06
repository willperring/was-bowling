<?php

Abstract class Model {

	protected static $_PDO = null;

	public static function setPDO( PDO $pdo ) {
		static::$_PDO = $pdo;
	}

	protected $_data   = array();
	protected $_exists = false;

	public function __get( $key ) {
		if( !key_exists($key, $this->_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		return $this->_data[ $key ];
	}

	public function __set( $key, $value ) {
		if( !key_exists($key, $this->_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		$this->_data[ $key ] = $value;
	}

	public function save() {

	}
	
}