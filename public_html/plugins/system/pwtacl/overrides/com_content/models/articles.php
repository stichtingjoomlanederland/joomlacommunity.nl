<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;

// No direct access.
defined('_JEXEC') or die;

/**
 * Extend Joomla core backend Articles class
 */
class ContentModelArticles extends ContentModelArticlesCore
{
	/**
	 * Method to get a list of articles.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   3.0
	 */
	public function getItems()
	{
		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, 0, 10000);

		// Get User
		$user = Factory::getUser();

		// Check for user permissions
		for ($x = 0, $count = count($items); $x < $count; $x++)
		{
			// Check the access level. Remove articles the user shouldn't see
			$canEdit    = $user->authorise('core.edit', 'com_content.article.' . $items[$x]->id);
			$canEditOwn = $user->authorise('core.edit.own', 'com_content.article.' . $items[$x]->id) && $items[$x]->created_by == $user->id;
			$canChange  = $user->authorise('core.edit.state', 'com_content.article.' . $items[$x]->id);

			if (!$canEdit && !$canEditOwn && !$canChange)
			{
				unset($items[$x]);
			}
		}

		// Only load the required items
		if ($this->getState('list.start') || $this->getState('list.limit'))
		{
			$items = array_slice($items, $this->getState('list.start'), $this->getState('list.limit'));
		}

		return $items;
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since  3.0
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, 0, 10000);

		// Get User
		$user = Factory::getUser();

		// Check for user permissions
		for ($x = 0, $count = count($items); $x < $count; $x++)
		{
			//Check the access level. Remove articles the user shouldn't see
			$canEdit    = $user->authorise('core.edit', 'com_content.article.' . $items[$x]->id);
			$canEditOwn = $user->authorise('core.edit.own', 'com_content.article.' . $items[$x]->id) && $items[$x]->created_by == $user->id;
			$canChange  = $user->authorise('core.edit.state', 'com_content.article.' . $items[$x]->id);

			if (!$canEdit && !$canEditOwn && !$canChange)
			{
				unset($items[$x]);
			}
		}

		try
		{
			// Load the total and add the total to the internal cache.
			$this->cache[$store] = (int) count($items);
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}
}
