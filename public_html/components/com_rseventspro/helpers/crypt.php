<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
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

class RseventsproCrypt {
	
	public static function decrypt($data, $key) {
		$decrypted = '';
		$tmp = $key;

		// Convert the HEX input into an array of integers and get the number of characters.
		$chars = self::_hexToIntArray($data);
		$charCount = count($chars);

		// Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
		for ($i = 0; $i < $charCount; $i = strlen($tmp)) {
			$tmp = $tmp . $tmp;
		}

		// Get the XOR values between the ASCII values of the input and key characters for all input offsets.
		for ($i = 0; $i < $charCount; $i++) {
			$decrypted .= chr($chars[$i] ^ ord($tmp[$i]));
		}

		return $decrypted;
	}
	
	public static function encrypt($data, $key) {
		$encrypted = '';
		$tmp = $key;

		// Split up the input into a character array and get the number of characters.
		$chars = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);
		$charCount = count($chars);

		// Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
		for ($i = 0; $i < $charCount; $i = strlen($tmp)) {
			$tmp = $tmp . $tmp;
		}

		// Get the XOR values between the ASCII values of the input and key characters for all input offsets.
		for ($i = 0; $i < $charCount; $i++) {
			$encrypted .= self::_intToHex(ord($tmp[$i]) ^ ord($chars[$i]));
		}

		return $encrypted;
	}

	private static function _hexToInt($s, $i) {
		$j = (int) $i * 2;
		$k = 0;
		$s1 = (string) $s;

		// Get the character at position $j.
		$c = substr($s1, $j, 1);

		// Get the character at position $j + 1.
		$c1 = substr($s1, $j + 1, 1);

		switch ($c) {
			case 'A': $k += 160; break;
			case 'B': $k += 176; break;
			case 'C': $k += 192; break;
			case 'D': $k += 208; break;
			case 'E': $k += 224; break;
			case 'F': $k += 240; break;
			case ' ': $k += 0; 	 break;
			default:  (int) $k = $k + (16 * (int) $c); break;
		}

		switch ($c1) {
			case 'A': $k += 10; break;
			case 'B': $k += 11; break;
			case 'C': $k += 12; break;
			case 'D': $k += 13; break;
			case 'E': $k += 14; break;
			case 'F': $k += 15; break;
			case ' ': $k += 0; 	break;
			default: $k += (int) $c1; break;
		}

		return $k;
	}
	
	private static function _hexToIntArray($hex) {
		$array = array();

		$j = (int) strlen($hex) / 2;

		for ($i = 0; $i < $j; $i++) {
			$array[$i] = (int) self::_hexToInt($hex, $i);
		}

		return $array;
	}
	
	private static function _intToHex($i) {
		// Sanitize the input.
		$i = (int) $i;

		// Get the first character of the hexadecimal string if there is one.
		$j = (int) ($i / 16);

		if ($j === 0) {
			$s = ' ';
		} else {
			$s = strtoupper(dechex($j));
		}

		// Get the second character of the hexadecimal string.
		$k = $i - $j * 16;
		$s = $s . strtoupper(dechex($k));

		return $s;
	}
}