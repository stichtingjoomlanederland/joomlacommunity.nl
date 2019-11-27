<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

try
{
	$controller = BaseController::getInstance('Pwtimage');
	$controller->execute(Factory::getApplication()->input->get('task'));
	$controller->redirect();
}
catch (Exception $e)
{
	echo $e->getMessage();
}
