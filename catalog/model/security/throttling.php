<?php
namespace Opencart\Catalog\Model\Security;
class Throttling extends \Opencart\System\Engine\Model {
  
  public function isIpAllowed($key, $hourly_limit, $daily_limit) {
    $sql = "SELECT (SELECT COUNT(*) FROM " . DB_PREFIX . "throttling WHERE date_added > date_sub(now(), interval 1 hour) AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "') as hourly_usage, " .
        "(SELECT COUNT(*) FROM " . DB_PREFIX . "throttling WHERE date_added > date_sub(now(), interval 1 day) AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "') as daily_usage FROM DUAL";
    
    $query = $this->db->query($sql);
    
    if ($query->row['hourly_usage'] >= $hourly_limit || $query->row['daily_usage'] >= $daily_limit) {
      return false;
    }
        
    return true;
  }
  
  public function isDataAllowed($key, $data, $hourly_limit, $daily_limit) {
    $sql = "SELECT (SELECT COUNT(*) FROM " . DB_PREFIX . "throttling WHERE date_added > date_sub(now(), interval 1 hour) AND data = '" . $this->db->escape(utf8_strtolower($data)) . "') as hourly_usage, " .
        "(SELECT COUNT(*) FROM " . DB_PREFIX . "throttling WHERE date_added > date_sub(now(), interval 1 day) AND data = '" . $this->db->escape(utf8_strtolower($data)) . "') as daily_usage FROM DUAL";
    
    $query = $this->db->query($sql);
    
    if ($query->row['hourly_usage'] >= $hourly_limit || $query->row['daily_usage'] >= $daily_limit) {
      return false;
    }
    
    $this->db->query("INSERT INTO " . DB_PREFIX . "throttling SET `key` = '" . $this->db->escape($key) . "', data = '" . $this->db->escape(utf8_strtolower($data)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
    
    return true;
  }
  
  public function save($key, $data) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "throttling SET `key` = '" . $this->db->escape($key) . "', data = '" . $this->db->escape(utf8_strtolower($data)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
  }
  
}