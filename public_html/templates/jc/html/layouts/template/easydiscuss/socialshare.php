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
	$params = json_decode($my->params);
	$params = array_filter((array)$params);

	if (!empty($params)) {
		$output = [];
		$output['ul-start'] = '<ul class="list-inline">';
		foreach ($params as $key => $param) {

			$keyName = $key;
			if ($key === 'website') {
				$keyName = 'globe';
			}

			$icon = '<i class="fa fa-2x fa-' . $keyName . '" aria-hidden="true"></i><span class="sr-only">' . ucfirst($key) . '</span>';
			$output[$key] = '<li class="share__item">' . HTMLHelper::_('link', $param, $icon, ['target' => '_blank']) . '</li>';

			// Remove key when starts with show_
			// Remove referring key when show_ equal 0
			// ToDo use str_starts_with() when PHP8 is used.
			if (substr($key, 0, 5) === "show_") {
				if ($param === "0") {
					$unsetKey = substr($key, 5);
					unset($output[$unsetKey]);
				}

				unset($output[$key]);
			}
		}
		$output['ul-end'] = '</ul>';

		echo implode('', $output);
	}
}
