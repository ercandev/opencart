<?php
namespace Opencart\Catalog\Model\Checkout;
class Marketing extends \Opencart\System\Engine\Model {
	public function getMarketingByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "marketing WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}
}