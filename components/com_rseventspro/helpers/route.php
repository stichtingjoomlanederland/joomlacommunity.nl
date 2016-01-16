<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * RSEvents!Pro Component Route Helper
 *
 * @static
 * @package		RSEvents!Pro
 * @subpackage	Events
 * @since 1.5
 */
class RseventsproHelperRoute
{
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

		if($item = RseventsproHelperRoute::_findItem('rseventspro', 'default')) {
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}
	
	public static function getEventsItemid() {
		$itemid = '';

		//return the itemid
		if($item = RseventsproHelperRoute::_findItem('rseventspro', 'default'))
			$itemid = $item->id;
		
		return (int) $itemid;
	}

	public static function getCalendarItemid() {
		$itemid = '';

		//return the itemid
		if($item = RseventsproHelperRoute::_findItem('calendar', 'default')) 
			$itemid = $item->id;
		
		return (int) $itemid;
	}

	protected static function _findItem($view, $layout) {
		$component	= JComponentHelper::getComponent('com_rseventspro');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$items		= $menus->getItems('component_id', $component->id);
		$match		= null;
		
		if (!empty($items)) {
			foreach ($items as $item) {
				if (!isset($item->query['view'])) {
					$item->query['view'] = 'rseventspro';
				}
				if (!isset($item->query['layout'])) {
					$item->query['layout'] = 'default';
				}
					
				if ($item->query['view'] == $view && $item->query['layout'] == $layout) {
					$match = $item;
					break;
				}
				
				if ($item->query['view'] == 'rseventspro' && $item->query['layout'] == 'default') {
					$parent = $item;
				}
			}
			
			// second try, get the parent RSEvents!Pro menu if available
			if (!$match && !empty($parent))
				$match = $parent;
		}

		return $match;
	}
}