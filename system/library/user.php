<?php
namespace Opencart\System\Library;
class User {
  private $registry;
	private $user_id;
	private $username;
	private $user_group_id;
	private $permission = array();

	public function __construct($registry) {
		$this->registry = $registry;

		if (isset($this->registry->get('session')->data['user_id'])) {
			$user_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->registry->get('session')->data['user_id'] . "' AND status = '1'");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				$this->user_group_id = $user_query->row['user_group_id'];

				$this->registry->get('db')->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->registry->get('session')->data['user_id'] . "'");

				$user_group_query = $this->registry->get('db')->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

				$permissions = json_decode($user_group_query->row['permission'], true);

				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password) {
		$user_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->registry->get('db')->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->registry->get('db')->escape(htmlspecialchars($password, ENT_QUOTES)) . "'))))) OR password = '" . $this->registry->get('db')->escape(md5($password)) . "') AND status = '1'");

		if ($user_query->num_rows) {
			$this->registry->get('session')->data['user_id'] = $user_query->row['user_id'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->user_group_id = $user_query->row['user_group_id'];

			$user_group_query = $this->registry->get('db')->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

			$permissions = json_decode($user_group_query->row['permission'], true);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->registry->get('session')->data['user_id']);

		$this->user_id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}

	public function getGroupId() {
		return $this->user_group_id;
	}
}
