<?php

Class Router {

	public static function route( array $routes ) {

		$currentRoute = current(explode('?', $_SERVER['REQUEST_URI']));
		
		if( !isset($routes[$currentRoute]) ) {
			header('HTTP/1.1 404 Not Found');
			die('404: Not found');
		}

		list( $controller, $action ) = explode('::', $routes[$currentRoute]);

		if( empty($controller) )
			Throw new Exception('No valid controller specified');
		if( !class_exists($controller) || !is_subclass_of($controller, 'Controller') )
			Throw new Exception("No such controller '{$controller}'");
		$controller = new $controller;

		if( empty($action) )
			Throw new Exception('No valid action specfied');
		if( !method_exists($controller, $action) )
			Throw new Exception("No action '{$action}' exists in controller");

		$controller->$action();

	}

}