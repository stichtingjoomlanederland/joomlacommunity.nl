<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Recurring\Mollie;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Subscription model.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayModelSubscription extends AdminModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  boolean  False is returned as we use no form.
	 *
	 * @since   5.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		return false;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function delete(&$pks)
	{
		try
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->select(
					$db->quoteName(
						array(
							'subscriptions.subscriptionId',
							'subscriptions.profileId',
							'customers.email'
						)
					)
				)
				->from($db->quoteName('#__jdidealgateway_subscriptions', 'subscriptions'))
				->leftJoin(
					$db->quoteName('#__jdidealgateway_customers', 'customers')
					. ' ON ' . $db->quoteName('customers.id') . ' = ' . $db->quoteName('subscriptions.customerId')
				);

			// Keep track of loded profiles
			$loaded = array();

			// Go through the items to cancel
			foreach ($pks as $index => $pk)
			{
				// Load the subscription to cancel
				$query->clear('where')
					->where($db->quoteName('subscriptions.id') . ' = ' . (int) $pk);
				$db->setQuery($query);

				$subscription = $db->loadObject();

				if (!isset($loaded[$subscription->profileId]))
				{
					// Load RO Payments
					$jdideal = new Gateway;

					// Load the profile, if it is not the default
					if ($jdideal->getProfileId() !== (int) $subscription->profileId)
					{
						$profileAlias = $jdideal->getProfileAlias($subscription->profileId);
						$jdideal->loadConfiguration($profileAlias);
					}

					// Load the Mollie class
					$mollie = new Mollie;
					$mollie->setApiKey($jdideal->get('profile_key'));

					$loaded[$subscription->profileId] = $mollie;
				}

				/** @var Mollie $mollie */
				$mollie = $loaded[$subscription->profileId];

				// Cancel the subscription
				$mollie->cancelSubscription($subscription->email, $subscription->subscriptionId);
			}

			return true;
		}
		catch (Exception $exception)
		{
			Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}
}
