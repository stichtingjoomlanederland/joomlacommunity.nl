<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);

if(!$images) {
	$image = 'none';
} elseif($images->image_fulltext) {
	$image = 'large';
} elseif ($images->image_intro) {
	$image = 'small';
}

$route = $this->item->parent_route;
$route = explode('/',$route);
$sub = $route[0];
?>

<?php
	if($sub == 'nieuws') {
		echo $this->loadTemplate('nieuws');
	} elseif($sub == 'documentatie') {
		echo $this->loadTemplate('documentatie');
	}
?>