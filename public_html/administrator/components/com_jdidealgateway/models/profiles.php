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
 * RO Payments Profiles model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelProfiles extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 *
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = [
				'profiles.ordering',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   integer  $cid  A ID to set published.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   5.3.0
	 */
	public function publish($cid): bool
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__jdidealgateway_profiles'))
			->set($db->quoteName('published') . ' = 0');
		$db->setQuery($query)
			->execute();

		$query->clear('set')
			->set($db->quoteName('published') . ' = 1')
			->where($db->quoteName('id') . ' = ' . $cid);
		$db->setQuery($query)
			->execute();

		return true;
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
	 * @since   4.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('profiles.ordering', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   4.0.0
	 *
	 * @throws  \RuntimeException
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				array(
					'profiles.id',
					'profiles.name',
					'profiles.psp',
					'profiles.alias',
					'profiles.ordering',
					'profiles.published',
				)
			)
		);

		$query->from($db->quoteName('#__jdidealgateway_profiles', 'profiles'));

		// If the model is set to check item state, add to the query.
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('profiles.published = ' . (int) $published);
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'profiles.ordering')
				)
			) . ' ' . $db->escape($this->getState('list.direction', 'ASC'))
		);

		return $query;
	}
}
