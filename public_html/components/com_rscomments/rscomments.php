<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.keepalive');

// Load the component main helper
require_once JPATH_SITE.'/components/com_rscomments/helpers/version.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/tooltip.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/adapter/adapter.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
require_once JPATH_SITE.'/components/com_rscomments/helpers/akismet/akismet.class.php';
require_once JPATH_SITE.'/components/com_rscomments/controller.php';

RSCommentsHelper::loadLang();
RSCommentsHelper::loadScripts();

$controller	= JControllerLegacy::getInstance('Rscomments');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();