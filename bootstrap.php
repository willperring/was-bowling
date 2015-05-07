<?php

set_include_path( __DIR__ );

// Load in required files
$lib_files = glob( __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '*.php' );
foreach( $lib_files as $library ) {
	require_once( $library );
}

// For developing - would normally be set to totally off...
error_reporting( E_ALL );
ini_set('display_errors', 'On');

// Define an autoloader...
$loadPaths = array('controllers', 'models');
spl_autoload_register( function( $class ) use ( $loadPaths ) {
	foreach( $loadPaths as $path ) {
		$checkPath = __DIR__ . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $class . '.php';
		if( file_exists($checkPath) )
			require_once( $checkPath );
	}
});

// Pull in config...
$configPath =  __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
if( !file_exists($configPath) )
	die('Local configuration file has not been configured. See config/README.md');
require_once( $configPath );

// Fire up a DB connection...
$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=bowling", DB_USERNAME, DB_PASSWORD, array());
$pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
Model::setPDO( $pdo );


