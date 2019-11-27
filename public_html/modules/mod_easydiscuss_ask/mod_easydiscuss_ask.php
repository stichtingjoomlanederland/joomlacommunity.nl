<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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

require_once ($engine);

ED::init();

JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

$work = ED::work(ED::date());
$status = $work->status();

$options = $work->getData();


require(JModuleHelper::getLayoutPath('mod_easydiscuss_ask'));
