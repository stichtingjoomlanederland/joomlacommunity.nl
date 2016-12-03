<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallIPBase
{
	// Hold our IP address in human readable format.
	protected $ip;
	
	// Constructor
	public function __construct($ip) {
		// Assign provided IP for class access.
		$this->ip = $ip;
	}
	
	// Returns the human readable format of the IP address.
	// @return string
	public function toAddress() {
		return $this->ip;
	}
	
	// Returns the binary representation of the IP address.
	// @return string
	public function toBinary() {
		$unpacked 	= str_split($this->toUnpacked());
		$bin 		= '';
		foreach ($unpacked as $char) {
			$bin .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}
		
		return $bin;
	}
	
	// Returns the packed representation of IP (in_addr).
	// @return string
	public function toPacked() {
		if (function_exists('inet_pton')) {
			$packed = @inet_pton($this->ip);
		} else {
			$packed = $this->inet_pton($this->ip);
		}
		if ($packed === false) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_COULD_NOT_TRANSFORM_PTON', $this->ip));
		}
		
		return $packed;
	}
	
	// Applies a mask to current IP to get the bits.
	// @return string
	public function applyMask($mask) {
		return substr($this->toBinary(), 0, $this->cleanMask($mask));
	}
}