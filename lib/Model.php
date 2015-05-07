<?php

/**
 * Model Base Class
 *
 * So this is the abstract class that all DB models are derived from.
 * In retrospect, coding this by hand maybe wasn't the most efficient approach,
 * but hopefully it at least makes a good example...
 */
Abstract class Model {

	//
	// CLASS (STATIC) METHODS AND PROPERTIES
	//

	/** @var PDO Database access handler */
	protected static $_PDO = null;

	//protected static $_transactionCounter = 0; // not currently used - maybe implement later?

	/**
	 * Set the PDO Object into the class
	 *
	 * All database access will require a connection. For this app, it's through a MySQL PDO object.
	 */
	public static function setPDO( PDO $pdo ) {
		static::$_PDO = $pdo;
	}

	/**
	 * Ensure that a PDO connection is present
	 *
	 * @throws Exception
	 */
	protected static function assertPDO() {
		if( is_null(static::$_PDO) )
			Throw new Exception("PDO connection has not been established");
	}

	/**
	 * Start a MySQL Transaction
	 */
	public static function startTransaction() {
		return static::$_PDO->beginTransaction();
	}

	/**
	 * Commit a MySQL transaction
	 */
	public static function commitTransaction() {
		return static::$_PDO->commit();
	}

	/**
	 * Rollback a MySQL transaction 
	 */
	public static function rollbackTransaction() {
			return static::$_PDO->rollBack();
	}

	/**
	 * Locate a Model by certain criteria
	 *
	 * This is currently the main (only!) 'simple' query method for locating and inflating
	 * data models. Currently, unless to be expanded, this takes a key-value array of pairs
	 * to be queried against the tables for a direct match ('==')
	 *
	 * TODO: add ordering or grouping?
	 *
	 * @param array $criteria Key/Value pairs (field:value) to query the database with
	 */
	public static function find_by( array $criteria ) {

		// We need a connection for this
		static::assertPDO();

		// Musn't be called from Model::find_by()
		if( get_called_class() == __CLASS__ )
			Throw new Exception("Model::find_by() must be called from a subclass of model");

		$valuePairs = array();
		$conditions = array();

		// Iterate criteria, check they exist in the property definitions, and 
		// build a statement for preparation
		$properties = array_keys( static::$_properties );
		foreach( $criteria as $key => $value ) {
			if( !in_array($key, $properties))
				continue;
			$valuePairs[ $key ] = $value;
			$conditions[]       = "{$key} = :{$key}";
		}

		// Ensure we actually have conditions to query
		if( !count($conditions) )
			Throw new Exception("Model::find_by() expects at least one valid critera to query on");
		$conditions = implode(' AND ', $conditions );

		$table     = static::$_table;
		$statement = "SELECT * FROM {$table} WHERE {$conditions}";
		$query     = static::$_PDO->prepare( $statement ); // prepared statements handle quoting
		$query->execute( $valuePairs );
		
		$result = $query->fetchAll();
		$rows   = array();
		$class  = get_called_class();
		
		// Iterate raw data and inflate model classes
		foreach( $result as $rowData ) {
			$row = new $class;
			$row->inflate( $rowData );
			$row->_exists = true;

			$rows[] = $row;
		}

		return $rows;
	}

	//
	// INSTANCE METHODS AND PROPERTIES
	//

	/** @var array   $_data   Internal storage of instance properties */
	protected $_data   = array();
	/** @var boolean $_exists True if the instance exists in the database */
	protected $_exists = false;

	/**
	 * MAGIC METHOD: Getter
	 *
	 * This method handles the retrieval of properties from the internal storage. This allows
	 * the opportunity to check against the property definition lists
	 *
	 * @throws Exception
	 *
	 * @param  string $key The property to get
	 * @return mixed       The property value
	 */
	public function __get( $key ) {
		if( !key_exists($key, static::$_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		return $this->_data[ $key ];
	}

	/**
	 * MAGIC METHOD: Setter
	 *
	 * This method handles the setting of properties into the internal storage. This allows
	 * the opportunity to check against the property definition lists
	 *
	 * @throws Exception
	 *
	 * @param  string $key   The property to get
	 * @param  mixed  $value The property value to set
	 * @return void
	 */
	public function __set( $key, $value ) {
		if( !key_exists($key, static::$_properties) )
			Throw new Exception("Undefined property '{$key}' in Model " . get_class($this) );
		$this->_data[ $key ] = $value;
	}

	/**
	 * Public SAVE method
	 *
	 * Publicly visible method to determine correct method (INSERT/UPDATE) for saving model
	 * 
	 * @return mixed Result of PDO operation
	 */
	public function save() {
		static::assertPDO();
		return ( $this->_exists ) ? $this->update() : $this->insert() ;
	}

	/**
	 * Insert the model into the database
	 *
	 * @see Model::save()
	 *
	 * @return mixed PDO Operation result
	 */
	private function insert() {

		$valuePairs   = array();
		$placeholders = array();

		// Build SQL placeholders for the prepared statement
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
		$result = $query->execute( $valuePairs ); // prepared statement handles quoting

		if( $result ) {
			$primaryKey = null;

			// Attempt to determine the primary key and set the ID of the new record
			foreach( static::$_properties as $field => $attributes ) {
				if( in_array('pk', $attributes) )
					$primaryKey = $field;
			}

			// TODO: if not allowing compound keys, this could move up into the above loop?
			if( $primaryKey ) {
				$this->$primaryKey = static::$_PDO->lastInsertId();
				$this->_exists     = true;
			}
		}

		return $result;
	}

	/**
	 * Update an existing model in the database
	 *
	 * @see Model::save()
	 *
	 * @return mixed PDO Operation result
	 */
	protected function update() {

		$valuePairs   = array();
		$placeholders = array();
		$primaryKey   = null;

		// Iterate the data to be updated and build placeholders
		foreach( $this->_data as $key => $value ) {
			// Don't update the primary key
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
		$result = $query->execute( $valuePairs ); // prepared statement handles quoting
		
		return $result;
	}

	/**
	 * Inflate a model from a data array
	 *
	 * Currently this just accepts an array and places it directly into the
	 * data container, although could be expanded in the future to provide
	 * some validation or checking.
	 *
	 * @return void
	 */
	protected function inflate( array $data ) {
		$this->_data = $data;
	}

}
