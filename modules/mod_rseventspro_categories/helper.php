<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProCategories {

	public static function getCategories(&$params)
	{
		$categories = JCategories::getInstance('Rseventspro',array('hash' => uniqid('rsepro')));
		$category = $categories->get($params->get('parent', 'root'));
		
		if ($category != null) {
			$items = $category->getChildren();
			if($params->get('limit', 0) > 0 && count($items) > $params->get('limit', 0)) {
				$items = array_slice($items, 0, $params->get('limit', 0));
			}
			return $items;
		}
	}
	
	public static function getCount($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$events = 0;
		
		$query->clear()
			->select($db->qn('e.id'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('e.id').' = '.$db->qn('t.ide'))
			->where($db->qn('t.type').' = '.$db->q('category'))
			->where($db->qn('t.id').' = '.(int) $id);
		
		$db->setQuery($query);
		$eventids = $db->loadColumn();	
		
		if (!empty($eventids)) {
			foreach ($eventids as $eid) {
				if (!rseventsproHelper::canview($eid)) continue;
				$events++;
			}
		}
		
		return $events;
	}
}