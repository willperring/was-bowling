<?php

Class Model {

	protected static $_PDO = null;

	public static function setPDO( PDO $pdo ) {
		static::$_PDO = $pdo;
	}
	
}