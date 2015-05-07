<?php

Class Router {

	/**
	 * Match the route to a predefined controller and action
	 */
	public static function route( array $routes ) {

		// get the current path, sans query string
		$currentRoute = current(explode('?', $_SERVER['REQUEST_URI']));
		
		// This is a pretty straight match currently - if it's not found, it's not there
		if( !isset($routes[$currentRoute]) ) {
			header('HTTP/1.1 404 Not Found');
			die('404: Not found');
		}

		// Split out the desired action route
		list( $controller, $action ) = explode('::', $routes[$currentRoute]);

		// Validate and instantiate the controller
		if( empty($controller) )
			Throw new Exception('No valid controller specified');
		if( !class_exists($controller) || !is_subclass_of($controller, 'Controller') )
			Throw new Exception("No such controller '{$controller}'");
		$controller = new $controller;

		// Validate and perform the action
		if( empty($action) )
			Throw new Exception('No valid action specfied');
		if( !method_exists($controller, $action) )
			Throw new Exception("No action '{$action}' exists in controller");
		$controller->$action();

	}

}