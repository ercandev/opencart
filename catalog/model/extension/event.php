<?php
namespace Opencart\Catalog\Model\Extension;
class Event extends \Opencart\System\Engine\Model {
	function getEvents() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `trigger` LIKE 'catalog/%' AND status = '1' ORDER BY `event_id` ASC");

		return $query->rows;
	}
}