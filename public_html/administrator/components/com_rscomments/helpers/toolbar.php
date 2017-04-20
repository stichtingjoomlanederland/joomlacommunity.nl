<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

abstract class RSCommentsToolbarHelper {
	public static $isJ30 = null;

	public static function addToolbar($ViewName) {
		self::addEntry(JText::_('COM_RSCOMMENTS_OVERVIEW'),				'index.php?option=com_rscomments',						$ViewName == 'overview' || $ViewName == '');
		self::addEntry(JText::_('COM_RSCOMMENTS_COMMENTS'),				'index.php?option=com_rscomments&view=comments',		$ViewName == 'comments');
		self::addEntry(JText::_('COM_RSCOMMENTS_EMOTICONS'),			'index.php?option=com_rscomments&view=emoticons',		$ViewName == 'emoticons');
		self::addEntry(JText::_('COM_RSCOMMENTS_SUBSCRIPTIONS'),		'index.php?option=com_rscomments&view=subscriptions',	$ViewName == 'subscriptions');
		self::addEntry(JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS'),	'index.php?option=com_rscomments&view=groups',			$ViewName == 'groups');
		self::addEntry(JText::_('COM_RSCOMMENTS_IMPORT'),				'index.php?option=com_rscomments&view=import',			$ViewName == 'import');
		self::addEntry(JText::_('COM_RSCOMMENTS_MESSAGES'),				'index.php?option=com_rscomments&view=messages',		$ViewName == 'messages');
	}

	protected static function addEntry($string, $url, $default=false) {
		if (self::$isJ30) {
			JHtmlSidebar::addEntry($string, JRoute::_($url), $default);
		} else {
			JSubMenuHelper::addEntry($string, JRoute::_($url), $default);
		}
	}

	public static function addFilter($text, $key, $options) {
		if (self::$isJ30) {
			JHtmlSidebar::addFilter($text, $key, $options);
		}
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
RSCommentsToolbarHelper::$isJ30 = $jversion->isCompatible('3.0');