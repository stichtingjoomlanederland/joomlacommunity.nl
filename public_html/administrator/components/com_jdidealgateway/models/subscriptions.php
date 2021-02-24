<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Subscriptions model.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayModelSubscriptions extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 *
	 * @since   4.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'customers.name',
				'subscriptions.status',
				'subscriptions.currency',
				'subscriptions.amount',
				'subscriptions.times',
				'subscriptions.interval',
				'subscriptions.description',
				'subscriptions.subscriptionId',
				'subscriptions.start',
				'subscriptions.cancelled',
				'subscriptions.created',

				'psp'
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('subscriptions.created', 'desc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		// Create a new query object.
		$query = parent::getListQuery();

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				array(
					'subscriptions.id',
					'subscriptions.subscriptionId',
					'subscriptions.status',
					'subscriptions.currency',
					'subscriptions.amount',
					'subscriptions.times',
					'subscriptions.interval',
					'subscriptions.description',
					'subscriptions.created',
					'subscriptions.start',
					'subscriptions.cancelled',
					'customers.name',
				)
			)
		)
			->from($db->quoteName('#__jdidealgateway_subscriptions', 'subscriptions'))
			->leftJoin(
				$db->quoteName('#__jdidealgateway_customers', 'customers')
				. ' ON ' . $db->quoteName('customers.customerId') . ' = ' . $db->quoteName('subscriptions.customerId')
			);

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			if (substr($search, 0, 3) === 'id:')
			{
				$searchArray = array(
					$db->quoteName('subscriptions.id') . ' = ' . (int) substr($search, 3)
				);
			}
			else
			{
				$searchArray = array(
					$db->quoteName('subscriptions.status') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.currency') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.subscriptionId') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.amount') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.times') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.interval') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('subscriptions.description') . ' LIKE ' . $db->quote('%' . $search . '%')
				);
			}

			$query->where('(' . implode(' OR ', $searchArray) . ')');
		}

		$psp = $this->getState('filter.psp', '');

		if ($psp)
		{
			$query->where($db->quoteName('subscriptions.profileId') . ' = ' . (int) $psp);
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'subscriptions.created')
				)
			) . ' ' . $db->escape($this->getState('list.direction', 'DESC'))
		);

		return $query;
	}
}
