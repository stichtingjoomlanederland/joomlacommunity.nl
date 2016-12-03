<?php
/**
 * @package     JVersions
 * @subpackage  mod_jversions
 *
 * @copyright   Copyright (C) 2016 Niels van der Veer. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/helper.php';

// Get the module layout parameter
$layout = $params->get('layout', 'default');

// Include the layout file 
require JModuleHelper::getLayoutPath('mod_jversions', $layout);
