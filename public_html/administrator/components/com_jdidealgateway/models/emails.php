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
 * Emails model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelEmails extends ListModel
{
	/**
	 * Load a list of emails to send a test for.
	 *
	 * @return  array  List of emails.
	 *
	 * @since   6.2.0
	 */
	public function getEmails(): array
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'id',
						'subject',
					],
					[
						'value',
						'text',
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_emails'));
		$db->setQuery($query);

		return $db->loadObjectList() ?? [];
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
		parent::populateState('id', 'desc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 *
	 * @since   4.0.0
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
					'id',
					'subject',
					'body',
					'trigger',
				)
			)
		);

		$query->from($db->quoteName('#__jdidealgateway_emails'));

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'id')
				)
			) . ' ' . $db->escape($this->getState('list.direction', 'DESC'))
		);

		return $query;
	}
}
