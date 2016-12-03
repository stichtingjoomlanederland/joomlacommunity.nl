<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProCategories {

	public static function getCategories($params) {
		$categories = JCategories::getInstance('Rseventspro',array('hash' => uniqid('rsepro')));
		$category = $categories->get($params->get('parent', 'root'));
		
		if ($category != null) {
			$items = $category->getChildren();
			if ($params->get('limit', 0) > 0 && count($items) > $params->get('limit', 0)) {
				$items = array_slice($items, 0, $params->get('limit', 0));
			}
			
			return $items;
		}
	}
	
	public static function getCount($id, $params) {
		$cache = JFactory::getCache('mod_rseventspro_categories');
		$cache->setCaching($params->get('use_cache', 1));
		$cache->setLifeTime($params->get('cache_time', 900));
		$cache = $cache->call(array('modRseventsProCategories', 'getCachedCount'), $params);
		
		return !empty($cache[$id]) ? $cache[$id] : 0;
	}
	
	public static function getCategoryIds() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select($db->qn('id'))
			  ->from($db->qn('#__categories'))
			  ->where($db->qn('extension').'='.$db->q('com_rseventspro'));
		return $db->setQuery($query)->loadColumn();
	}
	
	public static function getCachedCount($params) {
		$db	   = JFactory::getDbo();
		$query = $db->getQuery(true);
		$cache = array();
		$ids   = self::getCachedCategoryIds($params);
		
		$query
			->select($db->qn('e.id', 'event_id'))
			->select($db->qn('t.id', 'cat_id'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('e.id').' = '.$db->qn('t.ide'))
			->where($db->qn('t.type').' = '.$db->q('category'))
			->where($db->qn('e.published').' = 1')
			->where($db->qn('e.completed').' = 1')
			->where($db->qn('t.id').' IN ('.implode(',', $ids).')');
		
		$db->setQuery($query);
		if ($taxonomies = $db->loadObjectList()) {
			foreach ($taxonomies as $taxonomy) {
				if (empty($cache[$taxonomy->cat_id])) {
					$cache[$taxonomy->cat_id] = 0;
				}
				if (rseventsproHelper::canview($taxonomy->event_id)) {
					$cache[$taxonomy->cat_id]++;
				}
			}
		}
		
		return $cache;
	}
	
	protected static function getCachedCategoryIds($params) {
		$cache = JFactory::getCache('mod_rseventspro_categories');
		$cache->setCaching($params->get('use_cache', 1));
		$cache->setLifeTime($params->get('cache_time', 900));
		
		return $cache->call(array('modRseventsProCategories', 'getCategoryIds'));
	}
}