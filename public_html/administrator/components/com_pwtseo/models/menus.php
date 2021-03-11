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
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

class PWTSEOModelMenus extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'menu.published',
				'menu.title',
				'menu.access',
				'menu.language',
				'seo.pwtseo_score',
				'seo.focus_word',
				'published',
				'access',
				'language'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.2.0
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
							'menu.id',
							'menu.title',
							'menu.checked_out',
							'menu.checked_out_time',
							'menu.alias',
							'menu.published',
							'menu.language',
							'seo.focus_word',
							'seo.pwtseo_score',
							'seo.flag_outdated',
							'language.title',
							'language.image'
						),
						array(
							'id',
							'title',
							'checked_out',
							'checked_out_time',
							'alias',
							'published',
							'language',
							'focus_word',
							'pwtseo_score',
							'flag_outdated',
							'language_title',
							'language_image'
						)
					)
				)
			)
			->from($db->quoteName('#__menu', 'menu'))
			->leftJoin($db->quoteName('#__plg_pwtseo', 'seo') . ' ON seo.context_id = menu.id')
			->leftJoin($db->quoteName('#__languages', 'language') . ' ON language.lang_code = menu.language')
			->where($db->quoteName('menu.client_id') . ' = 0')
			->where($db->quoteName('seo.context_id') . ' NOT IN (' . implode(',', $db->quote(array('com_content.article', 'com_pwtseo.custom'))) . ')')
			->where($db->quoteName('menu.alias') . ' <> ' . $db->quote('root'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('menu.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('menu.title LIKE ' . $search . ' OR seo.focus_word LIKE ' . $search);
			}
		}

		if ($access = $this->getState('filter.access'))
		{
			$query->where('menu.access = ' . (int) $access);
		}

		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('menu.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(menu.published = 0 OR menu.published = 1)');
		}

		$published = $this->getState('filter.language');

		if (!empty($published))
		{
			$query->where('menu.language = ' . $db->quote($published));
		}

		$orderCol  = $this->getState('list.ordering', 'menu.id');
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
	 * @since   1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		parent::populateState($ordering, $direction);
	}
}
