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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Model for handling the payment.
 *
 * @package  JDiDEAL
 * @since    2.0
 */
class JdidealgatewayModelCheckideal extends BaseDatabaseModel
{
	/**
	 * Load the transaction details.
	 *
	 * @param   int  $logId  The log ID to get the details for.
	 *
	 * @return  object  The object with payment details.
	 *
	 * @since   2.0
	 */
	public function loadDetails(int $logId): stdClass
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where($db->quoteName('id') . ' = ' . $logId);
		$db->setQuery($query);

		return $db->loadObject() ?? new stdClass;
	}

	/**
	 * Load the username for a given transaction.
	 *
	 * @param   string  $transactionId  The transaction ID
	 * @param   string  $pid            The unique ID
	 *
	 * @return  string  The username or empty if not found.
	 *
	 * @since   6.4.0
	 */
	public function getUsername(string $transactionId, string $pid): string
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('users.username'))
			->from($db->quoteName('#__jdidealgateway_logs', 'logs'))
			->innerJoin(
				$db->quoteName('#__users', 'users')
				. ' ON ' . $db->quoteName('users.id') . ' = ' . $db->quoteName(
					'logs.user_id'
				)
			)
			->where(
				$db->quoteName('logs.trans') . ' = ' . $db->quote(
					$transactionId
				)
			)
			->where(
				$db->quoteName('logs.pid') . ' = ' . $db->quote($pid)
			);

		$db->setQuery($query);

		return (string) $db->loadResult();
	}
}
