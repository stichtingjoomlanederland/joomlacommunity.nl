<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallVersion
{
	public $version;
	public $key = 'FW6AL534B2';
	// Unused
	public $revision = null;
	
	public function __construct() {
		if (preg_match('/<version>([0-9.]+)<\/version>/s', file_get_contents(JPATH_ADMINISTRATOR.'/components/com_rsfirewall/rsfirewall.xml'), $match)) {
			$this->version = $match[1];
		}
	}

	public function __toString() {
		return $this->version;
	}
}