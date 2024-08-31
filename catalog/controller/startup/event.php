<?php
namespace Opencart\Catalog\Controller\Startup;
class Event extends \Opencart\System\Engine\Controller {
	public function index() {
		// Add events from the DB
		$this->load->model('extension/event');
		
		$results = $this->model_extension_event->getEvents();
		
		foreach ($results as $result) {
			$this->event->register(substr($result['trigger'], strpos($result['trigger'], '/') + 1), new \Opencart\System\Engine\Action($result['action']));
		}
	}
}