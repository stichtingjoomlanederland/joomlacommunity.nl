<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_spupgrade')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

//import KAINOTOMO PH LTD libraries and language
JFactory::getLanguage()->load('lib_spcyend', JPATH_SITE);
jimport('spcyend.utilities.factory');
jimport('spcyend.database.source');
jimport('joomla.filesystem.file');

include_once JPATH_COMPONENT . '/libraries/general.php';
include_once JPATH_COMPONENT . '/libraries/simplexml.php';
include_once JPATH_COMPONENT . '/models/com.php';
include_once JPATH_COMPONENT . '/models/extension.php';

// require helper file
JLoader::register('SPUpgradeHelper', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'spupgrade.php');

// import joomla controller library
jimport('joomla.application.component.controller');                                           

// Get an instance of the controller prefixed by SPUpgrade
$controller = JControllerLegacy::getInstance('SPUpgrade');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

