<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProLocations {

	public static function getLocations($params) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$order	= $params->get('ordering','DESC');
		$limit	= (int) $params->get('limit');
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->from($db->qn('#__rseventspro_locations'))
			->where($db->qn('published').' = 1')
			->order($db->qn('name').' '.$db->escape($order));
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		if ($limit)
			$items = array_slice($items,0,$limit);
		
		return $items;
	}
}