<?php
namespace Opencart\Catalog\Controller\Startup;
class Session extends \Opencart\System\Engine\Controller {
	public function index() {
		if (isset($this->request->get['token']) && isset($this->request->get['route']) && substr($this->request->get['route'], 0, 4) == 'api/') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE TIMESTAMPADD(HOUR, 1, date_modified) < NOW()");
		
			$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "api` `a` LEFT JOIN `" . DB_PREFIX . "api_session` `as` ON (a.api_id = as.api_id) LEFT JOIN " . DB_PREFIX . "api_ip `ai` ON (as.api_id = ai.api_id) WHERE a.status = '1' AND as.token = '" . $this->db->escape($this->request->get['token']) . "' AND ai.ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
		
			if ($query->num_rows) {
				$this->session->start($query->row['session_id']);
				
				// keep the session alive
				$this->db->query("UPDATE `" . DB_PREFIX . "api_session` SET date_modified = NOW() WHERE api_session_id = '" . (int)$query->row['api_session_id'] . "'");
			}
		} else {
		  if (isset($this->request->cookie[$this->config->get('session_name')])) {
		    $session_id = $this->request->cookie[$this->config->get('session_name')];
		  } else {
		    $session_id = '';
		  }
		  
		  $this->session->start($session_id);
		  
		  // Require higher security for session cookies
		  $option = [
		      'expires'  => 0,
		      'path'     => $this->config->get('session_path'),
		      'domain'   => $this->config->get('session_domain'),
		      'secure'   => $this->request->server['HTTPS'],
		      'httponly' => true,
		      'SameSite' => $this->config->get('session_samesite')
		  ];
		  
		  setcookie($this->config->get('session_name'), $this->session->getId(), $option);
		}
	}
}