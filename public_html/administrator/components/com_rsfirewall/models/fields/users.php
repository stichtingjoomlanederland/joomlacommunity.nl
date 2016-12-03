<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('rscheckboxes');

class JFormFieldUsers extends JFormFieldRSCheckboxes
{
	protected $type = 'Users';
	
	protected function getOptions() {
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/users.php';
		
		// Initialize variables.
		$options = array();
		
		$users = RSFirewallUsersHelper::getAdminUsers();
		
		foreach ($users as $user) {
			$tmp = JHtml::_('select.option', $user->id, $user->username);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);
		
		return $options;
	}
}
