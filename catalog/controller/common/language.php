<?php
namespace Opencart\Catalog\Controller\Common;
class Language extends \Opencart\System\Engine\Controller {
	public function index() {
		$this->load->language('common/language');

		$data['text_language'] = $this->language->get('text_language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
			  
			  $redirect = '';
			  if (!isset($this->request->get['route'])) {
			    $redirect = $this->url->link('common/home');
			  } else {
			    $url_data = $this->request->get;
			    $url_data['language'] = $result['code'];
			    
			    $route = $url_data['route'];
			    
			    unset($url_data['route']);
			    
			    $url = '';
			    
			    if ($url_data) {
			      $url = '&' . urldecode(http_build_query($url_data, '', '&'));
			    }
			    
			    $redirect = $this->url->link($route, $url, $this->request->server['HTTPS']);
			  }
			  
				$data['languages'][] = array(
					'name'     => $result['name'],
					'code'     => $result['code'],
			    'redirect' => $redirect
				);
			}
		}

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->session->data['language'] = $this->request->post['code'];
		}

		if (isset($this->request->post['redirect'])) {
			$this->response->redirect($this->request->post['redirect']);
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}
}