<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Setup the autoloader
JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

try
{
	// Get the input object
	$jinput = JFactory::getApplication()->input;

	$controller = JControllerLegacy::getInstance('jdidealgateway');
	$controller->execute($jinput->get('task'));
	$controller->redirect();
}
catch (Exception $e)
{
	JFactory::getApplication()->redirect('index.php', $e->getMessage(), 'error');
}
