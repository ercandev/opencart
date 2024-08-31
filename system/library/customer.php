<?php
namespace Opencart\System\Library;
class Customer {
  private $registry;
	private $customer_id;
	private $firstname;
	private $lastname;
	private $customer_group_id;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $address_id;

	public function __construct($registry) {
	  $this->registry = $registry;

		if (isset($this->registry->get('session')->data['customer_id'])) {
			$customer_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->registry->get('session')->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->address_id = $customer_query->row['address_id'];

				$this->registry->get('db')->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->registry->get('config')->get('config_language_id') . "', ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

				$query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->registry->get('session')->data['customer_id'] . "' AND ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "'");

				if (!$query->num_rows) {
					$this->registry->get('db')->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->registry->get('session')->data['customer_id'] . "', ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->registry->get('db')->escape(utf8_strtolower($email)) . "' AND status = '1'");
		} else {
			$customer_query = $this->registry->get('db')->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->registry->get('db')->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->registry->get('db')->escape($password) . "'))))) OR password = '" . $this->registry->get('db')->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
		}

		if ($customer_query->num_rows) {
			$this->registry->get('session')->data['customer_id'] = $customer_query->row['customer_id'];

			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->address_id = $customer_query->row['address_id'];

			$this->registry->get('db')->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->registry->get('config')->get('config_language_id') . "', ip = '" . $this->registry->get('db')->escape($this->registry->get('request')->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->registry->get('session')->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->customer_group_id = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->address_id = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getGroupId() {
		return $this->customer_group_id;
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

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getAddressId() {
		return $this->address_id;
	}

	public function getBalance() {
		$query = $this->registry->get('db')->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardPoints() {
		$query = $this->registry->get('db')->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}
}
