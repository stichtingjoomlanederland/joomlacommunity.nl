<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelUpdates extends JModelLegacy
{
	public function getHash() {
		$version = new RSFirewallVersion();
		return md5(RSFirewallConfig::getInstance()->get('code').$version->key);
	}
	
	public function getJoomlaVersion() {
		$jversion = new JVersion();
		return $jversion->getShortVersion();
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		return RSFirewallToolbarHelper::render();
	}
}