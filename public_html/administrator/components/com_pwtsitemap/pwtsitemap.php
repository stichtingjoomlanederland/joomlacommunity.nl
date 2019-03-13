<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_pwtsitemap'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Load PWT CSS
HTMLHelper::_('stylesheet', 'com_pwtsitemap/pwtsitemap.css', array('relative' => true, 'version' => 'auto'));

JLoader::register('PwtSitemapHelper', __DIR__ . '/helpers/pwtsitemap.php');
JLoader::register('PwtSitemapMenuHelper', __DIR__ . '/helpers/pwtsitemapmenu.php');
JLoader::register('PwtHtmlPwtSitemap', __DIR__ . '/helpers/html/pwtsitemap.php');

$controller = BaseController::getInstance('PwtSitemap');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
