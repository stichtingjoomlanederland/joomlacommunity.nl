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
	require_once JPATH_SITE.'/modules/mod_rseventspro_locations/helper.php';

	JFactory::getDocument()->addStyleSheet(JURI::root().'modules/mod_rseventspro_locations/style.css');

	$locations	= modRseventsProLocations::getLocations($params);
	$suffix		= $params->get('moduleclass_sfx');
	$links		= $params->get('links',0);

	// Get the Itemid
	$itemid = $params->get('itemid');
	$itemid = !empty($itemid) ? $itemid : RseventsproHelperRoute::getEventsItemid();

	require JModuleHelper::getLayoutPath('mod_rseventspro_locations');
}