<?php
namespace Opencart\System\Engine;
class Loader {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function controller($route, $data = array()) {
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
		
		$output = null;
		
		// Trigger the pre events
		$result = $this->registry->get('event')->trigger('controller/' . $route . '/before', array(&$route, &$data, &$output));
		
		if ($result) {
			return $result;
		}
		
		if (!$output) {
			$action = new \Opencart\System\Engine\Action($route);
			$output = $action->execute($this->registry, array(&$data));
		}
			
		// Trigger the post events
		$result = $this->registry->get('event')->trigger('controller/' . $route . '/after', array(&$route, &$data, &$output));
		
		if ($output instanceof \Exception) {
			return false;
		}

		return $output;
	}
	
	public function model($route) {
	  // Sanitize the call
	  $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', $route);
	  
	  // Converting a route path to a class name
	  $class = 'Opencart\\' . $this->registry->get('config')->get('application') . '\Model\\' . str_replace(['_', '/'], ['', '\\'], ucwords($route, '_/'));
	  
	  // Create a key to store the model object
	  $key = 'model_' . str_replace('/', '_', $route);
	  
	  // Check if the requested model is already stored in the registry.
	  if (!$this->registry->has($key)) {
	    if (class_exists($class)) {
	      $model = new $class($this->registry);
	      
	      $proxy = new \Opencart\System\Engine\Proxy();
	      
	      foreach (get_class_methods($model) as $method) {
	        if ((substr($method, 0, 2) != '__') && is_callable($class, $method)) {
	          // Grab args using function because we don't know the number of args being passed.
	          // https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list
	          // https://wiki.php.net/rfc/variadics
	          $proxy->{$method} = function (mixed &...$args) use ($route, $model, $method): mixed {
	            $route = $route . '/' . $method;
	            
	            $output = '';
	            
	            // Trigger the pre events
	            $result = $this->registry->get('event')->trigger('model/' . $route . '/before', [&$route, &$args]);
	            
	            if ($result) {
	              $output = $result;
	            }
	            
	            if (!$output) {
	              // Get the method to be used
	              $callable = [$model, $method];
	              
	              if (is_callable($callable)) {
	                $output = call_user_func_array($callable, $args);
	              } else {
	                throw new \Exception('Error: Could not call model/' . $route . '!');
	              }
	            }
	            
	            // Trigger the post events
	            $result = $this->registry->get('event')->trigger('model/' . $route . '/after', [&$route, &$args, &$output]);
	            
	            if ($result) {
	              $output = $result;
	            }
	            
	            return $output;
	          };
	        }
	      }
	      
	      $this->registry->set($key, $proxy);
	    } else {
	      throw new \Exception('Error: Could not load model ' . $class . '!');
	    }
	  }
	}

	public function view($route, $data = array()) {
		$output = null;
		
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
		
		// Trigger the pre events
		$result = $this->registry->get('event')->trigger('view/' . $route . '/before', array(&$route, &$data, &$output));
		
		if ($result) {
			return $result;
		}
		
		if (!$output) {
		  $template = new \Opencart\System\Library\Template($this->registry->get('config')->get('template_type'));
			
			foreach ($data as $key => $value) {
				$template->set($key, $value);
			}
		
			$output = $template->render($route . '.tpl');
		}
		
		// Trigger the post events
		$result = $this->registry->get('event')->trigger('view/' . $route . '/after', array(&$route, &$data, &$output));
		
		if ($result) {
			return $result;
		}
		
		return $output;
	}

	public function library($route) {
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
			
		$file = DIR_SYSTEM . 'library/' . $route . '.php';
		$class = str_replace('/', '\\', $route);

		if (is_file($file)) {
			include_once($file);

			$this->registry->set(basename($route), new $class($this->registry));
		} else {
			throw new \Exception('Error: Could not load library ' . $route . '!');
		}
	}
	
	public function helper($route) {
		$file = DIR_SYSTEM . 'helper/' . preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route) . '.php';

		if (is_file($file)) {
			include_once($file);
		} else {
			throw new \Exception('Error: Could not load helper ' . $route . '!');
		}
	}
	
	public function config($route) {
		$this->registry->get('event')->trigger('config/' . $route . '/before', array(&$route));
		
		$this->registry->get('config')->load($route);
		
		$this->registry->get('event')->trigger('config/' . $route . '/after', array(&$route));
	}

	public function language($route) {
		$output = null;
		
		$this->registry->get('event')->trigger('language/' . $route . '/before', array(&$route, &$output));
		
		$output = $this->registry->get('language')->load($route);
		
		$this->registry->get('event')->trigger('language/' . $route . '/after', array(&$route, &$output));
		
		return $output;
	}
	
	protected function callback($registry, $route) {
		return function($args) use($registry, &$route) {
			static $model = array(); 			
			
			$output = null;
			
			// Trigger the pre events
			$result = $registry->get('event')->trigger('model/' . $route . '/before', array(&$route, &$args, &$output));
			
			if ($result) {
				return $result;
			}
			
			// Store the model object
			if (!isset($model[$route])) {
				$file = DIR_APPLICATION . 'model/' .  substr($route, 0, strrpos($route, '/')) . '.php';
				$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', substr($route, 0, strrpos($route, '/')));

				if (is_file($file)) {
					include_once($file);
				
					$model[$route] = new $class($registry);
				} else {
					throw new \Exception('Error: Could not load model ' . substr($route, 0, strrpos($route, '/')) . '!');
				}
			}

			$method = substr($route, strrpos($route, '/') + 1);
			
			$callable = array($model[$route], $method);

			if (is_callable($callable)) {
				$output = call_user_func_array($callable, $args);
			} else {
				throw new \Exception('Error: Could not call model/' . $route . '!');
			}
			
			// Trigger the post events
			$result = $registry->get('event')->trigger('model/' . $route . '/after', array(&$route, &$args, &$output));
			
			if ($result) {
				return $result;
			}
						
			return $output;
		};
	}	
}