<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/items.php';

/**
 * PWT Sitemap items model
 *
 * @since   1.0.0
 */
class PwtSitemapModelItems extends MenusModelItems
{
	/**
	 * Get all available menu items
	 *
	 * @return  stdClass
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $i => $item)
		{
			$item->params = new Registry(json_decode($item->params));
		}

		return $items;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   Ordering
	 * @param   string  $direction  Direction
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = 'a.lft', $direction = 'asc')
	{
		parent::populateState($ordering, $direction);
	}
}
