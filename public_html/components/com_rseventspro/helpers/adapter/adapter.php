<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '3.0', '>=')) 
{
	// Joomla! 3.0
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/3.0/tabs.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/3.0/fieldsets.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/3.0/filterbar.php';
}
elseif (version_compare(JVERSION, '2.5.0', '>=')) 
{
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/2.5/tabs.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/2.5/fieldsets.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/2.5/filterbar.php';
	
	jimport('joomla.application.component.model');
	jimport('joomla.application.component.modelform');
	jimport('joomla.application.component.modellist');
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelitem');
	jimport('joomla.application.component.view');
	jimport('joomla.application.component.controller');
	jimport('joomla.application.component.controlleradmin');
	jimport('joomla.application.component.controllerform');
	jimport('joomla.html.editor');
	
	// Joomla! 2.5
	if (!class_exists('JModelLegacy')) {
		class JModelLegacy extends JModel { 
			public static function addIncludePath($path = '', $prefix = '') {
				return parent::addIncludePath($path, $prefix);
			}
		}
	}
	
	if (!class_exists('JViewLegacy')) {
		class JViewLegacy extends JView { }
	}
	
	if (!class_exists('JControllerLegacy')) {
		class JControllerLegacy extends JController { }
	}
	
	if (JFactory::getApplication()->isAdmin()) {
		class JHtmlSidebar extends JSubMenuHelper { }
	}
} 
elseif (version_compare(JVERSION, '1.5.0', '>=')) 
{
	// Joomla! 1.5
}