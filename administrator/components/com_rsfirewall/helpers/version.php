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
	public $version = '2.10.1';
	public $key		= 'FW6AL534B2';
	// Unused
	public $revision = null;
	
	public function __toString() {
		return $this->version;
	}
}