<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
    return;
}

require_once($path);

// Load up ED dependencies
ED::init();

// Load language files
JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$lib = ED::modules($module);
$helper = $lib->getHelper(false);

// Determines if the module should be rendered
if (!$helper->shouldRender()) {
	return;
}

$lib->addScript('filter.js');

// Determine filter type
$filterType = $params->get('type', 'category');
$filters = $helper->getFilters();

require(JModuleHelper::getLayoutPath('mod_easydiscuss_filters', $filterType));
