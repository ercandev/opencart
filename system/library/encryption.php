<?php
namespace Opencart\System\Library;
class Encryption {
	private $key;

	public function __construct($key) {
		$this->key = hash('sha256', $key, true);
	}

	public function encrypt($value) {
		return strtr(base64_encode(openssl_encrypt($value, 'aes-128-cbc', $this->key)), '+/=', '-_,');
	}

	public function decrypt($value) {
		return trim(openssl_decrypt(base64_decode(strtr($value, '-_,', '+/=')), 'aes-128-cbc', $this->key));
	}
}