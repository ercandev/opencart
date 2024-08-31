<?php
namespace Opencart\System\Library;
class Affiliate {
  private $registry;
	private $affiliate_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $code;

	public function __construct($registry) {
	  $this->registry = $registry;

		if (isset($this->registry->get('session')->data['affiliate_id'])) {
			$affiliate_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$this->registry->get('session')->data['affiliate_id'] . "' AND status = '1'");

			if ($affiliate_query->num_rows) {
				$this->affiliate_id = $affiliate_query->row['affiliate_id'];
				$this->firstname = $affiliate_query->row['firstname'];
				$this->lastname = $affiliate_query->row['lastname'];
				$this->email = $affiliate_query->row['email'];
				$this->telephone = $affiliate_query->row['telephone'];
				$this->fax = $affiliate_query->row['fax'];
				$this->code = $affiliate_query->row['code'];

				$this->registry->get('db')->query("UPDATE " . DB_PREFIX . "affiliate SET ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "' WHERE affiliate_id = '" . (int)$this->registry->get('session')->data['affiliate_id'] . "'");
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password) {
		$affiliate_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->registry->get('db')->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->registry->get('db')->escape($password) . "'))))) OR password = '" . $this->registry->get('db')->escape(md5($password)) . "') AND status = '1' AND approved = '1'");

		if ($affiliate_query->num_rows) {
			$this->registry->get('session')->data['affiliate_id'] = $affiliate_query->row['affiliate_id'];

			$this->affiliate_id = $affiliate_query->row['affiliate_id'];
			$this->firstname = $affiliate_query->row['firstname'];
			$this->lastname = $affiliate_query->row['lastname'];
			$this->email = $affiliate_query->row['email'];
			$this->telephone = $affiliate_query->row['telephone'];
			$this->fax = $affiliate_query->row['fax'];
			$this->code = $affiliate_query->row['code'];

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->registry->get('session')->data['affiliate_id']);

		$this->affiliate_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
	}

	public function isLogged() {
		return $this->affiliate_id;
	}

	public function getId() {
		return $this->affiliate_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getFax() {
		return $this->fax;
	}

	public function getCode() {
		return $this->code;
	}
}