<?php
namespace Opencart\System\Library;
class Session {
  protected $adaptor;
  protected $session_id = '';
	public $data = array();

	public function __construct($adaptor, $registry) {
	  $class = 'Opencart\System\Library\Session\\' . $adaptor;
	  
	  if (class_exists($class)) {
	    if ($registry) {
	      $this->adaptor = new $class($registry);
	    } else {
	      $this->adaptor = new $class();
	    }
	    
	    register_shutdown_function([&$this, 'close']);
	    register_shutdown_function([&$this, 'gc']);
	  } else {
	    throw new \Exception('Error: Could not load session adaptor ' . $adaptor . ' session!');
	  }
	}
	
	public function getId() {
	  return $this->session_id;
	}
		
	public function start($session_id = '') {
	  if (!$session_id) {
	    $this->session_id = $this->createId();
	  } elseif (preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $session_id)) {
	    $this->session_id = $session_id;
	  } else {
	    throw new \Exception('Error: Invalid session ID!');
	  }
	  
	  $this->data = $this->adaptor->read($session_id);
		
		return $this->session_id;
	}	
	
	public function createId() {
	  if (function_exists('random_bytes')) {
	    return substr(bin2hex(random_bytes(26)), 0, 26);
	  }
	  
    return substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
	}
	
	public function close() {
	  $this->adaptor->write($this->session_id, $this->data);
	}
	
	public function gc() {
	  $this->adaptor->gc();
	}
		
	public function destroy($key = 'default') {
	  $this->data = [];
	  $this->adaptor->destroy($this->session_id);
	}
}