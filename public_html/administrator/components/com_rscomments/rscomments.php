<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// ACL Check
if (!JFactory::getUser()->authorise('core.manage', 'com_rscomments')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Load files
require_once JPATH_SITE.'/components/com_rscomments/helpers/version.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/tooltip.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/adapter/adapter.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rscomments/helpers/rscomments.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rscomments/controller.php';

RSCommentsHelper::setScripts();

$controller	= JControllerLegacy::getInstance('Rscomments');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();