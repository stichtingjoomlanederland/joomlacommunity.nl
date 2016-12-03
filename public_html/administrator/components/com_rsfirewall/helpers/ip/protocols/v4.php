<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallIPv4 extends RSFirewallIPBase implements RSFirewallIPInterface
{
	// Tests if supplied IP address is IPv4.
	public static function test($ip) {
		if (defined('FILTER_VALIDATE_IP') && defined('FILTER_FLAG_IPV4')) {
			return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
		} else {
			return (strpos($ip, '.') !== false && strpos($ip, ':') === false);
		}
	}
	
	// Provides an unpacking method for IPv4
	public function toUnpacked() {
		$unpacked = unpack('A4', $this->toPacked());
		if (!isset($unpacked[1])) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_COULD_NOT_UNPACK_IP', $this->ip));
		}
		
		return $unpacked[1];
	}
	
	// Provides a variable that can be used with comparison operators.
	// IPv4 uses float.
	public function toComparable() {
		return $this->toLong();
	}
	
	// Provides numeric representation (float) of IPv4 address.
	public function toLong() {
		$long = ip2long($this->ip);
		if ($long === false) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_COULD_NOT_CONVERT_TO_LONG', $this->ip));
		}
		
		return (float) sprintf('%u', $long);
	}
	
	// Makes sure mask is valid.
	public function cleanMask($mask) {
		if (strpos($mask, '.') !== false) {
			// We have a /255.255.255.0 notation
			$maskIP = new RSFirewallIP($mask);
			$baseIP = new RSFirewallIP('255.255.255.255');
			
            $long = $maskIP->toLong();
            $base = $baseIP->toLong();
            $mask = 32 - log(($long ^ $base) + 1, 2);
		}
		
		$mask = (int) $mask;
		if ($mask > 32 || $mask < 1) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_NETWORK_MASK_OUTSIDE_RANGE', $mask, '1-32'));
		}
		
		return $mask;
	}
	
	protected function inet_pton($address) {
		$parts = explode('.', $address);
		if (count($parts) != 4) {
			return false;
		}
		
		return chr((int) $parts[0]).chr((int) $parts[1]).chr((int) $parts[2]).chr((int) $parts[3]);
	}
}