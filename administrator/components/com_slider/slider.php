<?php
/**
 * @package     Slider
 * @subpackage  com_slider
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$controller	= JControllerLegacy::getInstance('Slider');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
