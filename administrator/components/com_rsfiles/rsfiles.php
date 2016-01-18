<?php
/**
* @package RSFiles!
* @copyright (C) 2009 - 2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_rsfiles'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

require_once JPATH_SITE.'/components/com_rsfiles/helpers/version.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/adapter/adapter.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsfiles/controller.php';

// Load scripts
rsfilesHelper::initialize();

$controller	= JControllerLegacy::getInstance('RSFiles');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();