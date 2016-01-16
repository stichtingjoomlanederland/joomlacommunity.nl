<?php
/**
 * @package     Slider
 * @subpackage  mod_slider
 *
 * @copyright   Copyright (C) 2015 Perfect Web Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include helper file
require_once __DIR__ . '/helper.php';

// Get slides
$slides = ModSliderHelper::getSlides($params);

// Include layout file
require JModuleHelper::getLayoutPath('mod_slider', $params->get('layout', 'default'));
