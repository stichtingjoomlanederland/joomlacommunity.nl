<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallIPv6 extends RSFirewallIPBase implements RSFirewallIPInterface
{
	// Tests if supplied IP address is IPv6.
	public static function test($ip) {
		if (defined('FILTER_VALIDATE_IP') && defined('FILTER_FLAG_IPV6')) {
			return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
		} else {
			return (strpos($ip, ':') !== false);
		}
	}
	
	// Provides an unpacking method for IPv6
	public function toUnpacked() {
		$unpacked = unpack('A16', $this->toPacked());
		if (!isset($unpacked[1])) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_COULD_NOT_UNPACK_IP', $this->ip));
		}
		
		return $unpacked[1];
	}
	
	// Provides a variable that can be used with comparison operators.
	// IPv6 uses in_addr for string comparison.
	public function toComparable() {
		return $this->toPacked();
	}
	
	// Makes sure mask is valid.
	public function cleanMask($mask) {
		$mask = (int) $mask;
		if ($mask > 128 || $mask < 1) {
			throw new Exception(JText::sprintf('COM_RSFIREWALL_NETWORK_MASK_OUTSIDE_RANGE', $mask, '1-128'));
		}
		
		return $mask;
	}
	
	protected function inet_pton($address) {
		// Create an array with the delimited substrings
		$r = explode(':', $address);

		// Count the number of items
		$rcount = count($r);

		// If we have empty items, fetch the position of the first one
		if (($doub = array_search('', $r, 1)) !== false) {

			// We fill a $length variable with this rule:
			// - If it's the first or last item ---> 2
			// - Otherwhise                     ---> 1
			$length = (!$doub || $doub == $rcount - 1 ? 2 : 1);

			// Remove a portion of the array and replace it with something else
			array_splice($r,

				// We skip items before the empty one
				$doub,

				// We remove one or two items
				$length,

				// We replace each removed value with zeros
				array_fill(0, 8 + $length - $rcount, 0)

			);
		}

		// We convert each item from hexadecimal to decimal
		$r = array_map('hexdec', $r);
		// We add 'n*' at the beginning of the array (just a trick to use pack on all the items)
		array_unshift($r, 'n*');
		// We pack all the items as unsigned shorts (always 16 bit, big endian byte order)
		$r = call_user_func_array('pack', $r);
		// Return the resulting string
		return $r;
	}
}