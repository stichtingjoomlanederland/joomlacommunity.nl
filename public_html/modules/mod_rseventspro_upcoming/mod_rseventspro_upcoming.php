<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php')) {
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/route.php';
	require_once JPATH_SITE.'/modules/mod_rseventspro_upcoming/helper.php';

	// Get events
	$events = modRseventsProUpcoming::getEvents($params);

	// Load language
	JFactory::getLanguage()->load('com_rseventspro');

	// Add stylesheets
	JFactory::getDocument()->addStyleSheet(JURI::root().'modules/mod_rseventspro_upcoming/style.css');

	// Get the Itemid
	$itemid = $params->get('itemid');
	$itemid = !empty($itemid) ? $itemid : RseventsproHelperRoute::getEventsItemid();

	$suffix	= $params->get('moduleclass_sfx');
	$links	= $params->get('links',0);
	
	require JModuleHelper::getLayoutPath('mod_rseventspro_upcoming', $params->get('layout', 'default'));
}