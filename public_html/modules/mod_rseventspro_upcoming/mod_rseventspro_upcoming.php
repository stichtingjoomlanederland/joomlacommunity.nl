<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
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
	
	// Get number of columns
	$columns = (int) $params->get('columns', 1);
	$items	 = array_chunk($events, $columns);
	
	// Load language
	JFactory::getLanguage()->load('com_rseventspro');

	// Add stylesheets
	JHtml::stylesheet('mod_rseventspro_upcoming/style.css', array('relative' => true, 'version' => 'auto'));

	// Get the Itemid
	$itemid = $params->get('itemid');
	$itemid = !empty($itemid) ? $itemid : RseventsproHelperRoute::getEventsItemid();

	$suffix	= $params->get('moduleclass_sfx');
	$links	= $params->get('links',0);
	
	require JModuleHelper::getLayoutPath('mod_rseventspro_upcoming', $params->get('layout', 'default'));
}