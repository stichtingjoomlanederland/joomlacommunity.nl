<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallUsersHelper
{
	protected static $groups = null;
	protected static $users = null;
	
	public static function getAdminGroups() {
		if (!is_array(self::$groups)) {
			self::$groups = array();
			
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select($db->qn('id'))
				  ->from($db->qn('#__usergroups'));
			$db->setQuery($query);
			if ($groups = $db->loadColumn()) {
				$rules = JAccess::getAssetRules(1);
				
				foreach ($groups as $group_id) {				
					if ($rules->allow('core.admin', array($group_id)) || $rules->allow('core.login.admin', array($group_id))) {
						self::$groups[] = $group_id;
					}
				}
			}
		}
		
		return self::$groups;
	}
	
	public static function getAdminUsers() {
		if (!is_array(self::$users)) {
			self::$users = array();
			
			if ($groups	= self::getAdminGroups()) {
				$ids = array();
				foreach ($groups as $group) {
					$ids = array_merge($ids, JAccess::getUsersByGroup($group, true));
				}
				$ids = array_unique($ids);
				
				if ($ids) {
					$db 	= JFactory::getDbo();
					$query 	= $db->getQuery(true);
					$query->select('u.*')
						  ->from('#__users u')
						  ->where('u.id IN ('.implode(',', $ids).')')
						  ->order('u.username ASC');
					$db->setQuery($query);
					self::$users = $db->loadObjectList();
				}
			}
		}
		
		return self::$users;
	}
}