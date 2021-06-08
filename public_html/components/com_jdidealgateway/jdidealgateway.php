<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

if (class_exists(Gateway::class) === false)
{
	JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES . '/Jdideal');
}

try
{
	$input      = Factory::getApplication()->input;
	$controller = BaseController::getInstance('jdidealgateway');
	$controller->execute($input->get('task'));
	$controller->redirect();
}
catch (Exception $exception)
{
	$app = Factory::getApplication();
	$app->enqueueMessage($exception->getMessage(), 'error');
	$app->redirect('index.php');
}
