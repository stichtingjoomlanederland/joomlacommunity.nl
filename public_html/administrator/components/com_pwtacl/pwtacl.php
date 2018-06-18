<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// No direct access.
defined('_JEXEC') or die;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_pwtacl'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Register helper
JLoader::register('PwtaclHelper', __DIR__ . '/helpers/pwtacl.php');

// Add stylesheet
HTMLHelper::_('stylesheet', 'com_pwtacl/pwtacl.css', array('relative' => true, 'version' => 'auto'));

// Load language and fall back language
$jlang = Factory::getLanguage();
$jlang->load('com_pwtacl.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_pwtacl.sys', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_pwtacl.sys', JPATH_ADMINISTRATOR, null, true);
$jlang->load('com_pwtacl', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_pwtacl', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_pwtacl', JPATH_ADMINISTRATOR, null, true);

// Additional language files datatables
$jlang->load('com_users', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_users', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_users', JPATH_ADMINISTRATOR, null, true);
$jlang->load('mod_menu', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('mod_menu', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('mod_menu', JPATH_ADMINISTRATOR, null, true);

$controller = BaseController::getInstance('pwtacl');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
