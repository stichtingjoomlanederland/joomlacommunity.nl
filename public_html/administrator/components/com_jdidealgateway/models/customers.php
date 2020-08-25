<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Customers model.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayModelCustomers extends ListModel
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
			$config['filter_fields'] = array(
				'id', 'customers.id',
				'name', 'customers.name',
				'email', 'customers.email',
				'customerId', 'customers.customerId',
				'created', 'customers.created',
			);
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
		parent::populateState('customers.name', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @throws  Exception
	 *
	 * @since   4.0
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
					'id',
					'name',
					'email',
					'customerId',
					'created'
				)
			)
		)
			->from($db->quoteName('#__jdidealgateway_customers', 'customers'));

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			if (substr($search, 0, 3) === 'id:')
			{
				$searchArray = array(
					$db->quoteName('customers.id') . ' = ' . (int) substr($search, 3)
				);
			}
			else
			{
				$searchArray = array(
					$db->quoteName('customers.name') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('customers.email') . ' LIKE ' . $db->quote('%' . $search . '%'),
					$db->quoteName('customers.customerId') . ' LIKE ' . $db->quote('%' . $search . '%')
				);
			}

			$query->where('(' . implode(' OR ', $searchArray) . ')');
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'customers.name')
				)
			) . ' ' . $db->escape($this->getState('list.direction', 'ASC'))
		);

		return $query;
	}
}
