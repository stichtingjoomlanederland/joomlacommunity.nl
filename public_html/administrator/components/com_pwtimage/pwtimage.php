<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
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
if (!Factory::getUser()->authorise('core.manage', 'com_pwtimage'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Add stylesheet
HTMLHelper::_('stylesheet', 'com_pwtimage/pwtimage.min.css', array('relative' => true, 'version' => 'auto'));

try
{
	// Get the input object
	$input = Factory::getApplication()->input;

	// Global helper
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pwtimage.php';

	$controller = BaseController::getInstance('Pwtimage');
	$controller->execute($input->get('task', 'pwtimage'));
	$controller->redirect();
}
catch (Exception $e)
{
	// Check if we are in display format
	$format = $input->getCmd('format', $input->getCmd('tmpl', null));

	if (0 === strlen($format))
	{
		JToolbarHelper::title(Text::_('com_pwtimage'), 'image');
		Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
	}
	else
	{
		echo $e->getMessage();
	}
}
