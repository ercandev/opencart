<?php
namespace Opencart\Catalog\Model\Extension;
class Extension extends \Opencart\System\Engine\Model {
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
}