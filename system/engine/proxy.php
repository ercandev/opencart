<?php
namespace Opencart\System\Engine;
class Proxy {
  protected $data = [];
  
  public function &__get(string $key) {
    if (isset($this->data[$key])) {
      return $this->data[$key];
    } else {
      throw new \Exception('Error: Could not call proxy key ' . $key . '!');
    }
  }
	
  public function __set(string $key, object $value) {
    $this->data[$key] = $value;
  }
	
	public function __call($method, $args) {
	  // Hack for pass-by-reference
	  foreach ($args as $key => &$value) ;
	  
	  if (isset($this->data[$method])) {
	    return call_user_func_array($this->data[$method], $args);
	  } else {
	    $trace = debug_backtrace();
	    
	    throw new \Exception('<b>Notice</b>:  Undefined property: Proxy::' . $method . ' in <b>' . $trace[0]['file'] . '</b> on line <b>' . $trace[0]['line'] . '</b>');
	  }
	}
}