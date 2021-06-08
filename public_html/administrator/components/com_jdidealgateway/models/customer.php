<?php
/**
 * @package    RO Payments
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Recurring\Mollie;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Resources\SubscriptionCollection;

defined('_JEXEC') or die;

/**
 * Customer model.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayModelCustomer extends AdminModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   5.0.0
	 * @throws  Exception
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jdidealgateway.customer',
			'customer',
			['control' => 'jform', 'load_data' => $loadData]
		);

		if (!$form)
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   5.0.0
	 */
	public function save($data)
	{
		if (empty($data['id']))
		{
			$data['created'] = (new Date)->toSql();
		}

		return parent::save($data);
	}

	/**
	 * Retrieve the mandates for the given customer.
	 *
	 * @param   string  $customerEmail  The customer ID to get the mandates for
	 * @param   int     $profileId      The profile ID the customer belongs to
	 *
	 * @return  MandateCollection List of mandates.
	 *
	 * @since   5.0.0
	 * @throws \Mollie\Api\Exceptions\ApiException
	 */
	public function getMandates(string $customerEmail, int $profileId): MandateCollection
	{
		$profileTable = $this->getTable('Profile');
		$profileTable->load($profileId);
		$jdideal = new Gateway($profileTable->get('alias'));
		$mollie  = new Mollie;
		$mollie->setApiKey($jdideal->get('profile_key'))
			->setProfileId($jdideal->getProfileId());

		return $mollie->listMandates($customerEmail);
	}

	/**
	 * Retrieve the subscriptions for the given customer.
	 *
	 * @param   string  $customerEmail  The customer ID to get the subscriptions for
	 * @param   int     $profileId      The profile ID the customer belongs to
	 *
	 * @return  SubscriptionCollection List of subscriptions.
	 *
	 * @since   5.0.0
	 * @throws  Exception
	 *
	 */
	public function getSubscriptions(
		string $customerEmail,
		int $profileId
	): SubscriptionCollection {
		$profileTable = $this->getTable('Profile');
		$profileTable->load($profileId);
		$jdideal = new Gateway($profileTable->get('alias'));
		$mollie  = new Mollie;
		$mollie->setApiKey($jdideal->get('profile_key'))
			->setProfileId($jdideal->profileId);

		return $mollie->listSubscriptions($customerEmail);
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   6.1.0
	 * @throws  Exception
	 */
	public function delete(&$pks)
	{
		$jdideal = new Gateway;
		$mollie  = new Mollie;
		$mollie->setApiKey($jdideal->get('profile_key'))
			->setProfileId($jdideal->profileId);

		foreach ($pks as $pk)
		{
			$customer = $this->getItem($pk);

			try
			{
				$mollie->deleteCustomer($customer->get('customerId'));
			}
			catch (Exception $exception)
			{
				$this->setError($exception->getMessage());

				return false;
			}
		}

		return parent::delete($pks);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   5.0.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		/** @var CMSApplication $app */
		$app = Factory::getApplication();

		$data = $app->getUserState('com_jdidealgateway.edit.customer.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
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
			$mollie->syncAllCustomers();
		}
	}
}
