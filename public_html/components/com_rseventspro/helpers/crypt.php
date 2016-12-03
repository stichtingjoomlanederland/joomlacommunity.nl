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
	protected $container = array();
	protected $_key = 'RSEVENTSPRO';
	
	
	/**
	* Main constructor
	*/
	
	public function __construct($cc_number, $cc_csc, $key) {
		$this->_key		= $key;
		$cc_number		= is_null($cc_number) ? false : $this->encrypt($cc_number);
		$cc_csc			= is_null($cc_csc) ? false : $this->encrypt($cc_csc);
		
		if ($cc_number !== FALSE)
			$this->set($cc_number,'cc_number');
		
		if ($cc_csc !== FALSE)
			$this->set($cc_csc,'cc_csc');
	}
	
	
	/**
	* Encrypt message
	*/

	public function encrypt($message) {
		if (!$crypt = mcrypt_module_open('rijndael-256', '', 'ctr', ''))
			return false;
		
		$iv  = mcrypt_create_iv(32, MCRYPT_RAND);

		if (mcrypt_generic_init($crypt, $this->_key, $iv) !== 0 )
			return false;

		$message  = mcrypt_generic($crypt, $message);
		$message  = $iv . $message;
		$mac  = $this->createMac($message);
		$message .= $mac;

		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);

		$message = base64_encode($message);

		return $message;
	}
	
	/**
	* Decrypt message
	*/

	public function decrypt($message) {
		$message = base64_decode($message);
		
		if ( ! $crypt = mcrypt_module_open('rijndael-256', '', 'ctr', '') )
			return false;

		$iv  = substr($message, 0, 32);
		$mo  = strlen($message) - 32;
		$em  = substr($message, $mo);
		$message = substr($message, 32, strlen($message)-64);
		$mac = $this->createMac($iv . $message);

		if ( $em !== $mac )
			return false;

		if ( mcrypt_generic_init($crypt, $this->_key, $iv) !== 0 )
			return false;

		$message = mdecrypt_generic($crypt, $message);

		mcrypt_generic_deinit($crypt);
		mcrypt_module_close($crypt);

		return $message;
	}
	
	/**
	* Create the mac hash
	*/

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
	
	/**
	* Add a calculated hash
	*/
	
	public function set($hash, $type) {
		$this->container[$type] = $hash;
	}
	
	/**
	* Get a calculated hash
	*/
	
	public function get($type) {
		if (isset($this->container[$type]))
			return $this->container[$type];
		
		return;
	}
}