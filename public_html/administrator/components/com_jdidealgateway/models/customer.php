<?php
/**
 * @package    RO Payments
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Recurring\Mollie;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
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
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jdidealgateway.customer',
			'customer',
			array('control' => 'jform', 'load_data' => $loadData)
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
	 *
	 * @return  MandateCollection List of mandates.
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function getMandates(string $customerEmail): MandateCollection
	{
		// Load RO Payments
		$jdideal = new Gateway('mollie');

		// Load the Mollie class
		$mollie = new Mollie;
		$mollie->setApiKey($jdideal->get('profile_key'))
			->setProfileId($jdideal->getProfileId());

		return $mollie->listMandates($customerEmail);
	}

	/**
	 * Retrieve the subscriptions for the given customer.
	 *
	 * @param   string  $customerEmail  The customer ID to get the subscriptions for
	 *
	 * @return  SubscriptionCollection List of subscriptions.
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function getSubscriptions(string $customerEmail): SubscriptionCollection
	{
		// Load RO Payments
		$jdideal = new Gateway('mollie');

		// Load the Mollie class
		$mollie = new Mollie;
		$mollie->setApiKey($jdideal->get('profile_key'))
			->setProfileId($jdideal->profileId);

		return $mollie->listSubscriptions($customerEmail);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form.
	 *
	 * @throws  Exception
	 * @since   5.0.0
	 *
	 */
	protected function loadFormData()
	{
		/** @var CMSApplication $app */
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_jdidealgateway.edit.customer.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
}
