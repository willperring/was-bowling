<?php
require_once( dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php' );

Router::route( array(
	'/' => 'AppController::index',
));

