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
 * Log model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelPays extends ListModel
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
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'user_email', 'pays.user_email',
				'amount', 'pays.amount',
				'status', 'pays.status',
				'remark', 'pays.remark',
				'cdate', 'pays.cdate',
			);
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
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('pays.id',  'DESC');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @throws  Exception
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
					array(
						'pays.id',
						'pays.user_email',
						'pays.amount',
						'pays.status',
						'pays.remark',
						'pays.cdate',
					)
				)
			)
			->from($db->quoteName('#__jdidealgateway_pays', 'pays'));

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			$search      = $db->quote('%' . $search . '%');
			$searchArray = array(
				$db->quoteName('pays.id') . ' LIKE ' . $search,
				$db->quoteName('pays.user_email') . ' LIKE ' . $search,
				$db->quoteName('pays.amount') . ' LIKE ' . $search,
				$db->quoteName('pays.remark') . ' LIKE ' . $search,
			);

			$query->where('(' . implode(' OR ', $searchArray) . ')');
		}

		// Filter by status field
		$status = $this->getState('filter.status');

		if ($status)
		{
			$query->where($db->quoteName('pays.status') . ' = ' . $db->quote($status));
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'pays.date_added');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
