<?php
/**
 * @package     Slider
 * @subpackage  mod_slider
 *
 * @copyright   Copyright (C) 2015 Perfect Web Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class ModSliderHelper
{
	public static function getSlides($params)
	{
		// Get connection.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Make query
		$query->select("*");
		$query->from("#__slider");

		if ($params->get('slides'))
		{
			// Where
			$slides = implode(",", $params->get('slides', ''));
			$query->where("id IN (" . $slides . ")");
		}

		// Set query
		$db->setQuery($query);

		// Get result
		return $db->loadObjectList();
	}
}
