<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Recurring\Mollie;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Object\CMSObject;

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
		$form = $this->loadForm(
			'com_jdidealgateway.subscription',
			'subscription',
			['control' => 'jform', 'load_data' => $loadData]
		);

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Sync the subscriptions.
	 *
	 * @return  void
	 *
	 * @since   6.3.0
	 * @throws  Exception
	 */
	public function sync(): void
	{
		/** @var JdidealgatewayModelProfiles $profilesModel */
		$profilesModel = BaseDatabaseModel::getInstance('Profiles', 'JdidealgatewayModel', ['ignore_request' => true]);
		$profiles      = $profilesModel->getItems();

		foreach ($profiles as $profile)
		{
			$jdideal = new Gateway($profile->alias);

			if ((bool) $jdideal->get('recurring', false) === false)
			{
				continue;
			}

			$mollie = new Mollie;
			$mollie->setProfileId($jdideal->getProfileId());
			$mollie->setApiKey($jdideal->get('profile_key'));
			$mollie->syncAllSubscriptions();
		}
	}

	/**
	 * Cancel the user subscription.
	 *
	 * @param   string  $subscriptionId  The subscription ID to cancel
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	public function cancelSubscription(string $subscriptionId): void
	{
		BaseDatabaseModel::addIncludePath(
			JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/models'
		);

		/** @var JdidealgatewayModelSubscriptions $subscriptionsModel */
		$subscriptionsModel = BaseDatabaseModel::getInstance(
			'Subscriptions', 'JdidealgatewayModel', ['ignore_request' => true]
		);
		$subscriptionsModel->setState(
			'filter.subscriptionId', $subscriptionId
		);
		$subscriptions = $subscriptionsModel->getItems();

		foreach ($subscriptions as $subscription)
		{
			$pks = [$subscription->id];
			$this->delete($pks);
		}
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   5.0.0
	 * @throws  Exception
	 */
	public function delete(&$pks)
	{
		try
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->select(
					$db->quoteName(
						[
							'subscriptions.subscriptionId',
							'subscriptions.profileId',
							'customers.email',
						]
					)
				)
				->from($db->quoteName('#__jdidealgateway_subscriptions', 'subscriptions'))
				->leftJoin(
					$db->quoteName('#__jdidealgateway_customers', 'customers')
					. ' ON ' . $db->quoteName('customers.customerId') . ' = ' . $db->quoteName(
						'subscriptions.customerId'
					)
				);

			// Keep track of loded profiles
			$loaded = [];

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

				if (JVERSION < 4)
				{
					$dispatcher = JEventDispatcher::getInstance();
					$dispatcher->trigger('onCancelSubscriptionComplete', [$subscription]);
				}
				else
				{
					Factory::getApplication()->triggerEvent('onCancelSubscriptionComplete', [$subscription]);
				}
			}

			return true;
		}
		catch (Exception $exception)
		{
			Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   6.4.0
	 */
	public function save($data)
	{
		if (parent::save($data) === false)
		{
			return false;
		}

		$table = $this->getTable();
		$table->load($this->getState($this->getName() . '.id'));
		$mollie                         = $this->getMollieClient($table->get('profileId'));
		$subscription                   = $mollie->getSubscription(
			$table->get('customerId'), $table->get('subscriptionId')
		);
		$subscription->description      = $data['description'];
		$subscription->amount->value    = $data['amount'];
		$subscription->amount->currency = $data['currency'];
		$subscription->interval         = $data['interval'];
		$subscription->times            = $data['times'];
		$subscription->metadata         = $data['metadata'];
		$subscription->webhookUrl       = $data['webhookUrl'];
		$subscription->startDate        = (new Date($data['startDate']))->format('Y-m-d');

		$subscription->update();

		return true;
	}

	/**
	 * Load Mollie client.
	 *
	 * @param   int  $profileId  The profile ID
	 *
	 * @return  Mollie  The Mollie client.
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	private function getMollieClient(int $profileId): Mollie
	{
		$profileTable = $this->getTable('Profile', 'Table');
		$profileTable->load($profileId);
		$jdideal = new Gateway($profileTable->get('alias'));
		$mollie  = new Mollie;
		$mollie->setProfileId($jdideal->getProfileId());
		$mollie->setApiKey($jdideal->get('profile_key'));

		return $mollie;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_jdidealgateway.edit.subscription.data', []);

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  CMSObject|boolean  Object on success, false on failure.
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	public function getItem($pk = null)
	{
		$item         = parent::getItem($pk);
		$mollie       = $this->getMollieClient($item->get('profileId'));
		$subscription = $mollie->getSubscription($item->get('customerId'), $item->get('subscriptionId'));

		$item->set('webhookUrl', $subscription->webhookUrl);
		$item->set('createdAt', (new Date($subscription->createdAt))->format(Text::_('DATE_FORMAT_LC6')));
		$item->set('method', $subscription->method);
		$item->set('status', $subscription->status);
		$item->set('amount', $subscription->amount->value);
		$item->set('currency', $subscription->amount->currency);
		$item->set('description', $subscription->description);
		$item->set('interval', $subscription->interval);
		$item->set('mandateId', $subscription->mandateId);
		$item->set('metadata', $subscription->metadata);
		$item->set('times', $subscription->times);
		$item->set('startDate', (new Date($subscription->startDate))->format('Y-m-d'));
		$item->set('timesRemaining', $subscription->timesRemaining);
		$item->set('nextPaymentDate', (new Date($subscription->nextPaymentDate))->format(Text::_('DATE_FORMAT_LC4')));

		return $item;
	}
}
