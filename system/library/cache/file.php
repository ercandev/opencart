<?php
namespace Opencart\System\Library\Cache;
class File {
	private $expire;

	public function __construct($expire = 3600) {
		$this->expire = $expire;
	}

	public function get($key) {
	  $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
	  
	  if ($files) {
	    return json_decode(file_get_contents($files[0]), true);
	  } else {
	    return [];
	  }
	}

	public function set($key, $value, $expire) {
	  $this->delete($key);
	  
	  if (!$expire) {
	    $expire = $this->expire;
	  }
	  
	  file_put_contents(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $expire), json_encode($value));
	}

	public function delete($key) {
	  $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
	  
	  if ($files) {
	    foreach ($files as $file) {
	      if (!@unlink($file)) {
	        clearstatcache(false, $file);
	      }
	    }
	  }
	}
	
	public function __destruct() {
	  $files = glob(DIR_CACHE . 'cache.*');
	  
	  if ($files && rand(1, 100) == 1) {
	    foreach ($files as $file) {
	      $time = substr(strrchr($file, '.'), 1);
	      
	      if ($time < time()) {
	        if (!@unlink($file)) {
	          clearstatcache(false, $file);
	        }
	      }
	    }
	  }
	}
}