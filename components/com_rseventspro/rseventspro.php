<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Load the component main helper
require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/adapter.php';
require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
// Load Router Helper
require_once JPATH_SITE.'/components/com_rseventspro/helpers/route.php';
// Load the component main controller
require_once JPATH_COMPONENT.'/controller.php';
// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rseventspro/tables');
// Initialize main helper
rseventsproHelper::loadHelper();
// Add the Joomla! 2.5 metat title to the menus
rseventsproHelper::metatitle();
// Feed fix
rseventsproHelper::feed();

$controller	= JControllerLegacy::getInstance('RSEventspro');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();