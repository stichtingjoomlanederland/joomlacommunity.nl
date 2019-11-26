<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($engine)) {
    return;
}

require_once($engine);

JFactory::getLanguage()->load('mod_easydiscuss_most_hit', JPATH_ROOT);
$config = ED::config();

ED::init();

$options = array(
	'params' => $params,
	'sort' => 'hits'
	);

$posts = ED::modules()->getData($options);

if (!$posts) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easydiscuss_most_hit'));