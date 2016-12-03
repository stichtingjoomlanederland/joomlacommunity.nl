<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE.'/components/com_rsfiles/helpers/version.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/adapter/adapter.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/file.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/rsfiles.php';
require_once JPATH_SITE.'/components/com_rsfiles/controller.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/securimage/securimage.php';
require_once JPATH_SITE.'/components/com_rsfiles/helpers/recaptcha/recaptchalib.php';

rsfilesHelper::initialize('site');

$controller	= JControllerLegacy::getInstance('RSFiles');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();