<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PWTSEOModelCustoms extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     BaseDatabaseModel
	 * @since   1.1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'item.url',
				'item.pwtseo_score',
				'item.focus_word'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.1.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				$this->getState(
					'list.select',
					$db->quoteName(
						array(
							'item.id',
							'item.url',
							'item.focus_word',
							'item.pwtseo_score',
							'item.flag_outdated'
						),
						array(
							'id',
							'url',
							'focus_word',
							'pwtseo_score',
							'flag_outdated'
						)
					)
				)
			)
			->from($db->quoteName('#__plg_pwtseo', 'item'))
			->where($db->quoteName('context') . ' = ' . $db->quote('com_pwtseo.custom'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('item.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('item.url LIKE ' . $search . ' OR item.focus_word LIKE ' . $search);
			}
		}

		$orderCol  = $this->getState('list.ordering', 'item.url');
		$orderDirn = $this->getState('list.direction', 'DESC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);
	}
}
