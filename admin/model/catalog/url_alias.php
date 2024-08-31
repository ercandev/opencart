<?php
namespace Opencart\Admin\Model\Catalog;
class UrlAlias extends \Opencart\System\Engine\Model {
	public function getUrlAlias($keyword) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");

		return $query->row;
	}
}