<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.jc
 *
 * @copyright   Copyright (C) 2021 Volunteers
 * @license     GNU General Public License version 3 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var array $displayData */
extract($displayData);

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($path)) {
	return;
}

require_once($path);

ED::init();

if (isset($id)) {
	$my = ED::user($id);
	$type = isset($type) ? $type : 'user.username';
	$size = isset($size) ? $size : 'md';

	switch ($type) {
		case 'user.avatar':
			$options = ['rank' => true, 'status' => true, 'size' => $size];
			break;

		default:
			$options = [];
	}

	if ($type === 'custom') {
		if (isset($field)) {
			echo $my->{$field};
		}
	} else {
		echo ED::themes()->html($type, $my, $options);
	}
}
