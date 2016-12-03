<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.helper');
require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';

/**
 * RSEvents!Pro Component Route Helper
 *
 * @static
 * @package		RSEvents!Pro
 * @subpackage	Events
 * @since 1.5
 */
abstract class RseventsproHelperRoute
{
	protected static $lookup;
	
	
	/**
	 * @param	int	The route of the content item
	 */
	public static function getEventRoute($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$name = $db->loadResult();
		
		//Create the link
		$link = 'index.php?option=com_rseventspro&layout=show&id='. rseventsproHelper::sef($id,$name);

		$needles = array(
			'view'  => 'rseventspro',
			'layout'  => 'default'
		);
		
		if ($itemid = (int) RseventsproHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$itemid;
		}

		return $link;
	}
	
	public static function getCategoryRoute($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear()
			->select($db->qn('title'))
			->from($db->qn('#__categories'))
			->where($db->qn('extension').' = '.$db->q('com_rseventspro'))
			->where($db->qn('id').' = '.(int) $id);

		$db->setQuery($query);
		$title = $db->loadResult();
		
		// Create the link
		$link = 'index.php?option=com_rseventspro&category='.rseventsproHelper::sef($id, $title);
		
		$needles = array(
			'view'  => 'rseventspro',
			'layout'  => 'default'
		);
		
		if ($itemid = (int) RseventsproHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$itemid;
		}

		return $link;
	}
	
	public static function getEventsItemid($default = null) {
		$needles = array(
			'view'  => 'rseventspro',
			'layout'  => 'default'
		);

		$itemid = (int) RseventsproHelperRoute::_findItem($needles);
		
		if (empty($itemid) && !is_null($default)) {
			$itemid = (int) $default;
		}		
		
		return $itemid;
	}

	public static function getCalendarItemid() {
		$needles = array(
			'view'  => 'calendar',
			'layout'  => 'default'
		);
		
		return (int) RseventsproHelperRoute::_findItem($needles);
	}
	
	
	public static function eventRoute($id, $route = false) {
		$needles = array(
			'view'  => 'rseventspro',
			'layout'  => 'show',
			'id'  => $id
		);
		
		$itemid = RseventsproHelperRoute::_findItem($needles);
		
		if ($route) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)->select($db->qn('name'))->from($db->qn('#__rseventspro_events'))->where($db->qn('id').' = '.(int) $id);
			$db->setQuery($query);
			$name = $db->loadResult();
			
			// Create the link
			$link = 'index.php?option=com_rseventspro&layout=show&id='. rseventsproHelper::sef($id,$name);
			
			if ($itemid) {
				$link .= '&Itemid='.$itemid;
			}
		}
		
		return $route ? $link : $itemid;
	}

	protected static function _findItem($needles = null) {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_rseventspro');
			$items		= $menus->getItems('component_id', $component->id);
			
			foreach ($items as $item) {
				if (isset($item->query) && isset($item->query['view'])) {
					$view	= isset($item->query['view']) ? $item->query['view'] : 'rseventspro';
					$layout = isset($item->query['layout']) ? '_'.$item->query['layout'] : '_default';
					$id		= isset($item->query['id']) ? '_'.$item->query['id'] : '';
					
					self::$lookup[$view.$layout.$id] = $item->id;
				}
			}
		}
		
		if ($needles) {
			if (isset(self::$lookup)) {
				$search = '';
				$searchById = false;
				
				if (is_array($needles)) {
					if (isset($needles['view'])) {
						$search .= $needles['view'];
					}
					
					if (isset($needles['layout'])) {
						$search .= '_'.$needles['layout'];
					}
					
					if (isset($needles['id'])) {
						$search .= '_'.$needles['id'];
						$searchById = true;
					}
				}
				$search = !empty($search) ? $search : 'rseventspro_default';
				
				if (isset(self::$lookup[$search])) {
					return self::$lookup[$search];
				} else {
					if ($searchById && isset(self::$lookup['rseventspro_default'])) {
						return self::$lookup['rseventspro_default'];
					}
				}
			}
		} else {
			$active = $menus->getActive();
			if ($active && $active->component == 'com_rseventspro') {
				return $active->id;
			}
		}

		return null;
	}
}