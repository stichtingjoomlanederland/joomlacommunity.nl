<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

abstract class RSFormProToolbarHelper
{
	public static $isJ30 = null;
	
	public static function addToolbar($view='') {
		// load language file (.sys because the toolbar has the same options as the components dropdown)
		JFactory::getLanguage()->load('com_rsform.sys', JPATH_ADMINISTRATOR);
		
		// add toolbar entries
		// overview
		self::addEntry('MANAGE_FORMS', 'index.php?option=com_rsform&view=forms', $view == 'forms');
		self::addEntry('MANAGE_SUBMISSIONS', 'index.php?option=com_rsform&view=submissions', $view == 'submissions');
		self::addEntry('MANAGE_DIRECTORY_SUBMISSIONS', 'index.php?option=com_rsform&view=directory', $view == 'directory');
		self::addEntry('CONFIGURATION', 'index.php?option=com_rsform&view=configuration', $view == 'configuration');
		self::addEntry('BACKUP_RESTORE', 'index.php?option=com_rsform&view=backuprestore', $view == 'backuprestore');
		self::addEntry('UPDATES', 'index.php?option=com_rsform&view=updates', $view == 'updates');
	}
	
	protected static function addEntry($lang_key, $url, $default=false) {
		$lang_key = 'COM_RSFORM_'.$lang_key;
		
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
RSFormProToolbarHelper::$isJ30 = $jversion->isCompatible('3.0');