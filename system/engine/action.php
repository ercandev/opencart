<?php
namespace Opencart\System\Engine;
class Action {
	private $route;
	private $class;
	private $method = 'index';

	// TODO Ercan
	public function __construct($route) {
	  $this->route = preg_replace('/[^a-zA-Z0-9_\/]/', '', $route);
		
	  $parts = explode('/', $this->route);

		// Break apart the route
		while ($parts) {
			$file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';

			if (is_file($file)) {
				$this->route = implode('/', $parts);		
				$this->class = 'Controller\\' . str_replace(['_', '/'], ['', '\\'], ucwords($this->route, '_/'));
				
				break;
			} else {
				$this->method = array_pop($parts);
			}
		}
	}
	
	public function getId() {
		return $this->route;
	}
	
	public function execute($registry, array $args = array()) {
	  // Stop any magical methods being called
	  if (substr($this->method, 0, 2) == '__') {
	    return new \Exception('Error: Calls to magic methods are not allowed!');
	  }
	  
	  // Get the current namespace being used by the config
	  $class = 'Opencart\\' . $registry->get('config')->get('application') . '\\' . $this->class;
	  
	  // Initialize the class
	  if (class_exists($class)) {
	    $controller = new $class($registry);
	  } else {
	    return new \Exception('Error: Could not call route ' . $this->route . '!');
	  }
	  
	  if (is_callable([$controller, $this->method])) {
	    return call_user_func_array([$controller, $this->method], $args);
	  } else {
	    return new \Exception('Error: Could not call route ' . $this->route . '!');
	  }
	}
}
