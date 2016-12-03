<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

abstract class RSFirewallToolbarHelper
{
	public static $isJ30 = null;
	
	public static function addToolbar($view='') {
		$user = JFactory::getUser();
		
		// load language file (.sys because the toolbar has the same options as the components dropdown)
		JFactory::getLanguage()->load('com_rsfirewall.sys', JPATH_ADMINISTRATOR);
		
		// add toolbar entries
		// overview
		self::addEntry('OVERVIEW', 'index.php?option=com_rsfirewall', $view == '' || $view == 'rsfirewall');
		// system check
		if ($user->authorise('check.run', 'com_rsfirewall')) {
			self::addEntry('SYSTEM_CHECK', 'index.php?option=com_rsfirewall&view=check', $view == 'check');
		}
		// database check
		if ($user->authorise('dbcheck.run', 'com_rsfirewall')) {
			self::addEntry('DATABASE_CHECK', 'index.php?option=com_rsfirewall&view=dbcheck', $view == 'dbcheck');
		}
		// system logs
		if ($user->authorise('logs.view', 'com_rsfirewall')) {
			self::addEntry('SYSTEM_LOGS', 'index.php?option=com_rsfirewall&view=logs', $view == 'logs');
		}
		// configuration
		if ($user->authorise('core.admin', 'com_rsfirewall')) {
			self::addEntry('FIREWALL_CONFIGURATION', 'index.php?option=com_rsfirewall&view=configuration', $view == 'configuration');
		}
		// blacklist/whitelist
		if ($user->authorise('lists.manage', 'com_rsfirewall')) {
			self::addEntry('LISTS', 'index.php?option=com_rsfirewall&view=lists', $view == 'lists');
		}
		// exceptions
		if ($user->authorise('exceptions.manage', 'com_rsfirewall')) {
			self::addEntry('EXCEPTIONS', 'index.php?option=com_rsfirewall&view=exceptions', $view == 'exceptions');
		}
		// feeds
		if ($user->authorise('feeds.manage', 'com_rsfirewall')) {
			self::addEntry('RSS_FEEDS_CONFIGURATION', 'index.php?option=com_rsfirewall&view=feeds', $view == 'feeds');
		}
		// updates
		if ($user->authorise('updates.view', 'com_rsfirewall')) {
			self::addEntry('UPDATES', 'index.php?option=com_rsfirewall&view=updates', $view == 'updates');
		}
	}
	
	protected static function addEntry($lang_key, $url, $default=false) {
		$lang_key = 'COM_RSFIREWALL_'.$lang_key;
		
		if (self::$isJ30) {
			JHtmlSidebar::addEntry(JText::_($lang_key), JRoute::_($url), $default);
		} else {
			JSubMenuHelper::addEntry(JText::_($lang_key), JRoute::_($url), $default);
		}
	}
	
	public static function addFilter($text, $key, $options) {
		if (self::$isJ30) {
			JHtmlSidebar::addFilter($text, $key, $options);
		}
		
		// nothing for 2.5
	}
	
	public static function render() {
		if (self::$isJ30) {
			return JHtmlSidebar::render();
		} else {
			return '';
		}
	}
}

$jversion = new JVersion();
RSFirewallToolbarHelper::$isJ30 = $jversion->isCompatible('3.0');