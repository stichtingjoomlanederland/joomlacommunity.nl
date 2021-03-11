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

class PWTSEOModelDatalayers extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     BaseDatabaseModel
	 * @since   1.3.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'datalayer.id', 'id',
				'datalayer.title', 'title',
				'datalayer.language', 'language',
				'datalayer.template', 'template',
				'datalayer.published', 'published',
				'datalayer.ordering', 'ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.3.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				$this->getState(
					'list.select',
					array_merge(
						[
							'GROUP_CONCAT(templates.title) AS template_title'
						],
						$db->quoteName(
							array(
								'datalayer.id',
								'datalayer.title',
								'datalayer.name',
								'datalayer.language',
								'datalayer.template',
								'datalayer.published',
								'datalayer.ordering',
								'language.title',
								'language.image'
							),
							array(
								'id',
								'title',
								'name',
								'language',
								'template',
								'published',
								'ordering',
								'language_title',
								'language_image'
							)
						)
					)
				)
			)
			->from($db->quoteName('#__plg_pwtseo_datalayers', 'datalayer'))
			->leftJoin($db->quoteName('#__languages', 'language') . ' ON ' . $db->quoteName('language.lang_code') . ' = ' . $db->quoteName('datalayer.language'))
			->leftJoin($db->quoteName('#__template_styles', 'templates') . ' ON FIND_IN_SET(' . $db->quoteName('templates.id') . ', ' . $db->quoteName('datalayer.template') . ')')
			->group($db->quoteName('datalayer.id'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('datalayer.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(' . $db->quoteName('datalayer.title') . ' LIKE ' . $search . ' OR ' . $db->quoteName('datalayer.name') . ' LIKE ' . $search . ')');
			}
		}

		$search = $this->getState('filter.language');

		if (!empty($search) && $search !== '*')
		{
			$query->where($db->quoteName('datalayer.language') . ' = ' . $db->quote($search));
		}

		$search = $this->getState('filter.published');

		if (!empty($search))
		{
			$query->where($db->quoteName('datalayer.published') . ' = ' . (int) $search);
		}

		$orderCol  = $this->getState('list.ordering', 'datalayer.ordering');
		$orderDirn = $this->getState('list.direction', 'ASC');

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
	 * @since   1.3.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);
	}
}
