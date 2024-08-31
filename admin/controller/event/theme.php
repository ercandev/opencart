<?php
namespace Opencart\Admin\Controller\Event;
class Theme extends \Opencart\System\Engine\Controller {
	public function index(&$view, &$data) {
		// This is only here for compatibility with old templates
		if (substr($view, -3) == 'tpl') {
			$view = substr($view, 0, -3);
		}
	}
}
