<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Check for access
if (!JFactory::getUser()->authorise('core.manage', 'com_rseventspro'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

// Load files
require_once JPATH_SITE. '/components/com_rseventspro/helpers/adapter/adapter.php';
require_once JPATH_SITE. '/components/com_rseventspro/helpers/rseventspro.php';
require_once JPATH_COMPONENT.'/controller.php';

// Initialize main helper
rseventsproHelper::loadHelper();

$controller	= JControllerLegacy::getInstance('RSEventspro');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();