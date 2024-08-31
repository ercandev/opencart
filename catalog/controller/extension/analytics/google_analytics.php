<?php
namespace Opencart\Catalog\Controller\Extension\Analytics;
class GoogleAnalytics extends \Opencart\System\Engine\Controller {
    public function index() {
		return html_entity_decode($this->config->get('google_analytics_code'), ENT_QUOTES, 'UTF-8');
	}
}
