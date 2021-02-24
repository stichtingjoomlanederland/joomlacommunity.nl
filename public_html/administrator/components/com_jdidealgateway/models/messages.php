<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Messages model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelMessages extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   4.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'orderstatus',
				'psp',
				'language',

				'messages.subject',
				'messages.orderstatus',
				'profiles.name',
				'messages.language',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function populateState($ordering = null, $direction = null): void
	{
		if ($ordering === null)
		{
			$ordering = 'messages.subject';
		}

		if ($direction === null)
		{
			$direction = 'ASC';
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @throws  RuntimeException
	 *
	 * @since   4.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				[
					'messages.id',
					'messages.subject',
					'messages.orderstatus',
					'messages.language',
					'profiles.name',
				]
			)
		)
			->from($db->quoteName('#__jdidealgateway_messages', 'messages'))
			->leftJoin(
				$db->quoteName('#__jdidealgateway_profiles', 'profiles')
				. ' ON ' . $db->quoteName('profiles.id') . ' = ' . $db->quoteName('messages.profile_id')
			)
			// Join over the language
			->select($db->quoteName('languages.title',  'language_title'))
			->select($db->quoteName('languages.image',  'language_image'))
			->leftJoin(
				$db->quoteName('#__languages', 'languages')
				. ' ON ' . $db->quoteName('languages.lang_code') . ' = ' . $db->quoteName('messages.language')
			);

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			$searchArray = array(
				$db->quoteName('messages.id') . ' LIKE ' . $db->quote('%' . $search . '%'),
				$db->quoteName('messages.subject') . ' LIKE ' . $db->quote('%' . $search . '%'),
				$db->quoteName('messages.orderstatus') . ' LIKE ' . $db->quote('%' . $search . '%')
			);

			$query->where('(' . implode(' OR ', $searchArray) . ')');
		}

		// Filter by order status
		$orderStatus = $this->getState('filter.orderstatus');

		if ($orderStatus)
		{
			$query->where($db->quoteName('messages.orderstatus') . ' = ' . $db->quote($orderStatus));
		}

		// Filter by provider field
		$psp = $this->getState('filter.psp');

		if ($psp)
		{
			$query->where($db->quoteName('messages.profile_id') . ' = ' . (int) $psp);
		}

		// Filter by provider field
		$language = $this->getState('filter.language');

		if ($language)
		{
			$query->where($db->quoteName('messages.language') . ' = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'messages.id')
				)
			) . ' ' . $db->escape($this->getState('list.direction', 'DESC'))
		);

		return $query;
	}
}
