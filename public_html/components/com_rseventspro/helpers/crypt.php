<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * RSEvents!Pro Crypt Helper
 */
class RseventsproCryptHelper
{
	protected $key;
	
	public function __construct($key) {
		$this->key = isset($key) ? $key : $this->key();
	}
	
	public function encrypt($string) {
		$key	= base64_decode($this->key);
		$iv		= openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		$crypt	= openssl_encrypt($string, 'aes-256-cbc', $key, 0, $iv);
		
		return base64_encode($crypt.'::'.$iv);
	}
	
	public function decrypt($string) {
		$key = base64_decode($this->key);
		
		list($crypt, $iv) = explode('::', base64_decode($string), 2);
		
		return openssl_decrypt($crypt, 'aes-256-cbc', $key, 0, $iv);
	}
	
	protected function key() {
		return base64_encode('RSEVENTSPRO');
	}
}

class RseventsproCryptHelperLegacy {
	
	protected $container = array();
	protected $_key = 'RSEVENTSPRO';
	
	public function __construct($cc_number, $cc_csc, $key) {
		$this->_key		= $key;
		$cc_number		= is_null($cc_number) ? false : $this->encrypt($cc_number);
		$cc_csc			= is_null($cc_csc) ? false : $this->encrypt($cc_csc);
		
		if ($cc_number !== FALSE)	$this->set($cc_number,'cc_number');
		if ($cc_csc !== FALSE)		$this->set($cc_csc,'cc_csc');
	}
	
	public function encrypt($message) {
		if (!$crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '')) return false;
		
		$iv  = mcrypt_create_iv(32, MCRYPT_RAND);
		
		if (mcrypt_generic_init($crypt, $this->_key, $iv) !== 0) return false;

		$message  = mcrypt_generic($crypt, $message);
		$message  = $iv . $message;
		$mac  = $this->createMac($message);
		$message .= $mac;

		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);
		
		return base64_encode($message);
	}

	public function decrypt($message) {
		if (!$crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '')) return false;
		
		$message = base64_decode($message);
		$iv  = substr($message, 0, 32);
		$mo  = strlen($message) - 32;
		$em  = substr($message, $mo);
		$message = substr($message, 32, strlen($message)-64);
		$mac = $this->createMac($iv . $message);

		if ($em !== $mac) return false;
		if (mcrypt_generic_init($crypt, $this->_key, $iv) !== 0) return false;

		$message = mdecrypt_generic($crypt, $message);
		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);

		return $message;
	}
	
	protected function createMac($message) {
		$hashL = strlen(hash('sha256', null, true));
		$keyb = ceil(32 / $hashL);
		$thekey = '';

		for ($block = 1; $block <= $keyb; $block ++ ) {
			$iblock = $b = hash_hmac('sha256', $this->_key . pack('N', $block), $message, true);
			for ($i = 1; $i < 1000; $i++) 
				$iblock ^= ($b = hash_hmac('sha256', $b, $message, true));
			$thekey .= $iblock;
		}
		
		return substr($thekey, 0, 32);
	}
	
	public function set($hash, $type) {
		$this->container[$type] = $hash;
	}
	
	public function get($type) {
		if (isset($this->container[$type]))
			return $this->container[$type];
		
		return;
	}
}