<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

jimport( 'joomla.filesystem.file' );

if (!JFile::exists($path)) {
	return;
}

require_once($path);

ED::init();

$app = JFactory::getApplication();

$view = $app->input->get('view', '', 'var');
$id = $app->input->get('id');

// Return if this is not entry view
if ($view != 'post' || !$id) {
    return;
}

// Load the post library
$post = ED::post($id);

if (!$post) {
    return false;
}

$post->created = ED::date($post->created)->display(JText::_('DATE_FORMAT_LC1'));

require(JModuleHelper::getLayoutPath('mod_easydiscuss_post_info'));



