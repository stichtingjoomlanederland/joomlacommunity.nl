<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
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
// Set some task that are not available in the front-end
rseventsproHelper::task();

JFactory::getCache('page')->clean();

$controller	= JControllerLegacy::getInstance('Rseventspro');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();