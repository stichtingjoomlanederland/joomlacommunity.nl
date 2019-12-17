<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/menus.php';

/**
 * PWT Sitemap menus model
 *
 * @since   1.0.0
 */
class PwtSitemapModelMenus extends MenusModelMenus
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0.0
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		// Add our own filter fields
		$config['filter_fields'][] = 'ordering';
		$config['filter_fields'][] = 'pwtsitemap_menu_types.ordering';
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		/** @var JDatabaseQuery $query */
		$query = parent::getListQuery()
			->select(
				$db->quoteName(
					[
						'pwtsitemap_menu_types.ordering',
						'pwtsitemap_menu_types.custom_title'
					]
				)
			)
			->leftJoin(
				$db->quoteName('#__pwtsitemap_menu_types', 'pwtsitemap_menu_types')
				. ' ON ' . $db->quoteName('pwtsitemap_menu_types.menu_types_id') . ' = ' . $db->quoteName('a.id')
			);

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   The column to order
	 * @param   string  $direction  The direction of the order
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = 'pwtsitemap_menu_types.ordering', $direction = 'asc')
	{
		parent::populateState($ordering, $direction);
	}

	/**
	 * Save the order of the menu items.
	 *
	 * @param   array  $pks    The list of IDs to order
	 * @param   array  $order  The list of orders
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function saveorder($pks, $order)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		foreach ($pks as $index => $pk)
		{
			$query
				->clear()
				->update($db->quoteName('#__pwtsitemap_menu_types'))
				->set($db->quoteName('ordering') . ' = ' . (int) $order[$index])
				->where($db->quoteName('menu_types_id') . ' = ' . (int) $pk);
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				return false;
			}
		}

		return true;
	}
}
