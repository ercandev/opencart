<?php
namespace Opencart\Catalog\Controller\Event;
class Debug extends \Opencart\System\Engine\Controller {
	public function before(&$route, &$data) {
		if ($route == '') { // add the route you want to test
			$this->session->data['debug'][$route] = microtime();
		}
	}
	
	public function after(&$route, &$data, &$output) {
		if ($route == '') { // add the route you want to test
			if (isset($this->session->data['debug'][$route])) {
				$data = array(
					'route' => $route,
					'time'  => microtime() - $this->session->data['debug'][$route]
				);
				
				$this->log->write($data);
			}
		}
	}	
}
