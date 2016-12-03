<?php
/**
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// Prevent direct access
defined('_JEXEC') or die();


class ThisRSEventsProHelper
{

	public function getCategoryName($eventId)
	{
		$categories = $this->getCategoryNames();

		foreach ($categories as $category)
		{
			if ($category->event_id == $eventId)
			{
				return $category->category_name;
			}
		}
	}

	private function getCategoryNames()
	{
		static $categories = null;

		if (empty($categories))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select($db->qn('c.title', 'category_name'))
				->select($db->qn('t.ide', 'event_id'))
				->from($db->qn('#__rseventspro_taxonomy', 't'))
				->join('left', $db->qn('#__categories', 'c') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('c.id'))
				->where($db->qn('t.type') . ' = "category"');
			$db->setQuery($query);

			$categories = $db->loadObjectList();
		}

		return $categories;

	}
}
