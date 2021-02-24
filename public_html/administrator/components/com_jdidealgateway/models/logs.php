<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Log Model.
 *
 * @package  JDiDEAL
 * @since    3.0.0
 */
class JdidealgatewayModelLogs extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 *
	 * @since   4.0.0
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'origin', 'logs.origin',
				'order_id', 'logs.order_id',
				'order_number', 'logs.order_number',
				'currency', 'logs.currency',
				'amount', 'logs.amount',
				'card', 'logs.card',
				'trans', 'logs.trans',
				'psp', 'logs.psp',
				'result', 'logs.result',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function populateState($ordering = null, $direction = null): void
	{
		parent::populateState('logs.date_added',  'DESC');
	}

	/**
	 * Build an SQL query to load the list datlogs.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   4.0.0
	 */
	protected function getListQuery()
	{
		// Build the query
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'logs.id',
						'logs.trans',
						'logs.order_id',
						'logs.order_number',
						'logs.currency',
						'logs.amount',
						'logs.card',
						'logs.origin',
						'logs.date_added',
						'logs.result',
						'logs.paymentId',
						'profiles.alias',
						'profiles.psp',
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_logs', 'logs'))
			->leftJoin(
				$db->quoteName('#__jdidealgateway_profiles', 'profiles')
				. ' ON ' . $db->quoteName('profiles.id') . ' = ' . $db->quoteName('logs.profile_id')
			);

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			$search      = $db->quote('%' . $search . '%');
			$searchArray = [
				$db->quoteName('logs.order_id') . ' LIKE ' . $search,
				$db->quoteName('logs.order_number') . ' LIKE ' . $search,
				$db->quoteName('logs.amount') . ' LIKE ' . $search,
				$db->quoteName('logs.trans') . ' LIKE ' . $search,
			];

			$query->where('(' . implode(' OR ', $searchArray) . ')');
		}

		// Filter by origin field
		$origin = $this->getState('filter.origin');

		if ($origin)
		{
			$query->where($db->quoteName('logs.origin') . ' = ' . $db->quote($origin));
		}

		// Filter by card field
		$card = $this->getState('filter.card');

		if ($card)
		{
			$query->where($db->quoteName('logs.card') . ' = ' . $db->quote($card));
		}

		// Filter by provider field
		$psp = $this->getState('filter.psp');

		if ($psp)
		{
			$query->where($db->quoteName('logs.profile_id') . ' = ' . (int) $psp);
		}

		// Filter by currency field
		$currency = $this->getState('filter.currency');

		if ($currency)
		{
			$query->where($db->quoteName('logs.currency') . ' = ' . $db->quote($currency));
		}

		// Filter by result field
		$result = $this->getState('filter.result');

		if ($result)
		{
			$query->where($db->quoteName('logs.result') . ' = ' . $db->quote($result));
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'logs.date_added');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Load the history of a log entry.
	 *
	 * @return  string  The log history.
	 *
	 * @throws  Exception
	 *
	 * @since   3.0.0
	 */
	public function getHistory()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('history'))
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where($db->quoteName('id') . ' = ' . (int) Factory::getApplication()->input->getInt('log_id', 0));
		$db->setQuery($query);

		return $db->loadResult();
	}
}
