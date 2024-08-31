<?php
namespace Opencart\System\Library;
class Cache {
	private $adaptor;

	public function __construct($adaptor, $expire = 3600) {
	  $class = 'Opencart\System\Library\Cache\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($expire);
		} else {
			throw new \Exception('Error: Could not load cache adaptor ' . $adaptor . ' cache!');
		}
	}
	
	public function get($key) {
		return $this->adaptor->get($key);
	}

	public function set($key, $value, $expire = 0) {
		return $this->adaptor->set($key, $value, $expire);
	}

	public function delete($key) {
		return $this->adaptor->delete($key);
	}
}
