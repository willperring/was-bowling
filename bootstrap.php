<?php

set_include_path( __DIR__ );

// Load in required files
$lib_files = glob( __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '*.php' );
foreach( $lib_files as $library ) {
	require_once( $library );
}

error_reporting( E_ALL );
ini_set('display_errors', 'On');

$loadPaths = array('controllers', 'models');
spl_autoload_register( function( $class ) use ( $loadPaths ) {
	foreach( $loadPaths as $path ) {
		$checkPath = __DIR__ . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $class . '.php';
		if( file_exists($checkPath) )
			require_once( $checkPath );
	}
});
