<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussModelSidebar extends EasyDiscussAdminModel
{
	public function sortSidebar($a, $b)
	{
		return $a->order < $b->order ? -1 : 1;
	}

	/**
	 * Returns a list of menus for the admin sidebar.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getItems($activeView)
	{
		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = file_get_contents($file);

		$items = json_decode($contents);

		// Initialize default result.
		$result = array();

		foreach ($items as $item) {

			// Generate a unique id.
			$uid = uniqid();

			// Generate a new group object for the sidebar.
			$obj = clone($item);

			// Assign the unique id.
			$obj->uid = $uid;

			// Initialize the counter
			$obj->count	= 0;

			// Determine the type to check for to determine if the child is active
			$obj->activeChildType = $this->input->get($obj->active, '', 'string');

			// Parent would always get the counter from its child
			$obj->count = 0;

			if (isset($obj->counter) && !$obj->childs) {
				$obj->count = $this->getCount($obj->counter);
			}

			$obj->views = $this->getViews($obj);
			$obj->isActive = in_array($activeView, $obj->views) ? true : false;

			if (!isset($obj->link)) {
				$obj->link = 'index.php?option=com_easydiscuss&view=' . $obj->view;
			}

			// Ensure that each menu item has a child property
			if (!isset($obj->childs)) {
				$obj->childs = array();
			}

			if (!empty($obj->childs)) {
				$childItems = array();

				foreach ($obj->childs as $child) {

					// Clone the child object.
					$childObj = clone($child);

					// Let's get the URL.
					$url = array('index.php?option=com_easydiscuss');

					$query = $child->url;

					// Set the url into the child item so that we can determine the active submenu.
					$childObj->url = $child->url;

					if ($query) {

						foreach ($query as $queryKey => $queryValue) {

							if ($queryValue) {
								$url[]	= $queryKey . '=' . $queryValue;
							}

							// If this is a call to the controller, it must have a valid token id.
							if ($queryKey == 'controller') {
								$url[] = ED::token() . '=1';
							}
						}
					}

					// Set the item link.
					$childObj->link = implode('&amp;', $url);

					// Initialize the counter
					$childObj->count = 0;

					// Check if there's any sql queries to execute.
					if (isset($childObj->counter)) {
						$childObj->count = $this->getCount($childObj->counter);

						$obj->count += $childObj->count;
					}

					// Add a unique id for the side bar for accordion purposes.
					$childObj->uid = $uid;

					// Determine if the current child menu should be active
					$childObj->isActive = false;

					if (
						($obj->activeChildType == $childObj->url->{$obj->active} || (isset($childObj->activeLayouts) && in_array($obj->activeChildType, $childObj->activeLayouts))) &&
						($activeView == $childObj->url->view || (isset($childObj->activeViews) && in_array($activeView, $childObj->activeViews)))
					) {
						$childObj->isActive = true;
					}

					// Add the menu item to the child items.
					$childItems[] = $childObj;
				}

				$obj->childs = $childItems;
			}

			$result[] = $obj;
		}

		return $result;
	}

	/**
	 * Given a list of sidebar structure, determine all the views
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getViews($menuItem)
	{
		$views = array($menuItem->view);

		if (isset($menuItem->childs) && $menuItem->childs) {
			foreach ($menuItem->childs as $childMenu) {

				// Precedence if the child menu has a set of active views
				if (isset($childMenu->activeViews)) {
					$views = array_merge($views, $childMenu->activeViews);

					continue;
				}

				$views[] = $childMenu->url->view;
			}
		}

		$views = array_unique($views);

		return $views;
	}

	/**
	 * Retrieves a specific count item based on the namespace
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getCount($namespace)
	{
		list($modelName, $method) = explode('/', $namespace);

		$model = ED::model($modelName);
		$count = $model->$method();

		return $count;
	}

	public static function sortItems($a, $b)
	{
		$al = EDJString::strtolower(JText::_($a->title));
		$bl = EDJString::strtolower(JText::_($b->title));

		if ($al == $bl) {
			return 0;
		}

		return ( $al > $bl ) ? +1 : -1;
	}
}
