<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

namespace Jdideal\Recurring;

defined('_JEXEC') or die;

use InvalidArgumentException;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use Mollie\Api\Types\MandateMethod;
use stdClass;

/**
 * Mollie Recurring helper.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class Mollie
{
	/**
	 * The Mollie client
	 *
	 * @var    MollieApiClient
	 * @since  5.0.0
	 */
	private $mollie;

	/**
	 * The profile the subscription belongs to
	 *
	 * @var    integer
	 * @since  5.0.0
	 */
	private $profileId;

	/**
	 * Construct the class.
	 *
	 * @since   5.0.0
	 * @throws  \Exception
	 *
	 */
	public function __construct()
	{
		// Load Mollie
		require_once JPATH_LIBRARIES
			. '/Jdideal/Psp/Mollie/vendor/autoload.php';
		$this->mollie = new MollieApiClient;
	}

	/**
	 * Set the API key.
	 *
	 * @param   string  $apiKey  The API key to use for communicating with Mollie
	 *
	 * @return  Mollie Returns itself for chaining
	 *
	 * @since   5.0.0
	 * @throws  \Exception
	 *
	 */
	public function setApiKey($apiKey): Mollie
	{
		$this->mollie->setApiKey($apiKey);

		return $this;
	}

	/**
	 * Delete a customer.
	 *
	 * @param   string  $customerId  The customer ID to delete
	 *
	 * @return  void
	 *
	 * @since   6.1.0
	 * @throws  ApiException
	 */
	public function deleteCustomer(string $customerId): void
	{
		$this->mollie->customers->delete($customerId);
	}

	/**
	 * Create a customer.
	 *
	 * @param   string  $name       The customer name
	 * @param   string  $email      The customer email address
	 * @param   int     $profileId  The profile ID the customer belongs to
	 *
	 * @return  string  The customer ID
	 *
	 * @since   5.0.0
	 *
	 * @throws ApiException
	 */
	public function createCustomer(string $name, string $email, int $profileId
	): string {
		// Check if the customer already exists
		/** @var $customer object */
		if ($customer = $this->getCustomerByEmail($email))
		{
			return $customer->customerId;
		}

		// Create the Mollie customer
		$customer = $this->mollie->customers->create(
			[
				'name'  => $name,
				'email' => $email,
			]
		);

		// Get a Date object so we can format it to our needs
		$created = new Date($customer->createdAt);

		// Store the customer details in the database
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__jdidealgateway_customers'))
			->columns(
				$db->quoteName(
					array(
						'name',
						'email',
						'customerId',
						'profileId',
						'created'
					)
				)
			)
			->values(
				$db->quote($name) . ',' . $db->quote($email) . ',' . $db->quote(
					$customer->id
				) . ',' . $db->quote(
					$profileId
				) . ',' . $db->quote(
					$created->toSql()
				)
			);
		$db->setQuery($query)
			->execute();

		return $customer->id;
	}

	/**
	 * Check if a customer already exists with the given email address.
	 *
	 * @param   string  $email  The email address to check
	 *
	 * @return  stdClass|null  The customer details or null if not found.
	 *
	 * @since   5.0.0
	 */
	private function getCustomerByEmail(string $email): ?stdClass
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'id',
						'customerId'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_customers'))
			->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Create the first payment.
	 *
	 * @param   string  $transactionId  The RO Payments transaction ID
	 * @param   string  $email          The customer email address
	 * @param   string  $currency       The currency of the payment
	 * @param   string  $amount         The amount for the first payment
	 * @param   string  $description    The description for the first payment
	 * @param   string  $paymentMethod  The payment method to use for the first payment
	 *
	 * @return  object  The payment details
	 *
	 * @since   5.0.0
	 *
	 * @throws  \Exception
	 */
	public function createFirstPayment(
		string $transactionId,
		string $email,
		string $currency,
		string $amount,
		string $description,
		string $paymentMethod = ''
	) {
		// Check if the customer already exists
		/** @var $customer object */
		if (!$customer = $this->getCustomerByEmail($email))
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_CUSTOMER_DOES_NOT_EXIST')
			);
		}

		$payment = $this->mollie->payments->create(
			[
				'amount'       => [
					'currency' => $currency,
					'value'    => $amount
				],
				'customerId'   => $customer->customerId,
				'sequenceType' => 'first',
				'description'  => $description,
				'method'       => $paymentMethod,
				'redirectUrl'  => Uri::root() . 'cli/notify.php?transaction_id='
					. $transactionId . '&output=customer',
				'webhookUrl'   => Uri::root() . 'cli/notify.php?transaction_id='
					. $transactionId,
			]
		);

		// Redirect the payment object
		return $payment;
	}

	/**
	 * Set the profile ID to use for the subscriptions
	 *
	 * @param   int  $profileId  The profile ID of the subscription
	 *
	 * @return Mollie
	 *
	 * @since  5.0.0
	 */
	public function setProfileId(int $profileId): Mollie
	{
		$this->profileId = $profileId;

		return $this;
	}

	/**
	 * Get the mandate for the user.
	 *
	 * @param   string  $customerId  The customer ID to check the mandate for
	 *
	 * @return  boolean  True if there is a valid mandate | False otherwise
	 *
	 * @since   5.0.0
	 *
	 * @throws  ApiException
	 */
	public function hasValidMandate($customerId)
	{
		/** @var MandateCollection $mandates */
		$mandates = $this->mollie->customers->get($customerId)->mandates();

		/** @var Mandate $mandate */
		foreach ($mandates as $index => $mandate)
		{
			if ($mandate->isValid() || $mandate->isPending())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Create a periodic payment subscription.
	 *
	 * @param   string   $amount         The amount to charge periodically
	 * @param   string   $currency       The currency for the amount
	 * @param   string   $startDate      The starting date of the subscription
	 * @param   string   $description    A unique description for the subscription
	 * @param   string   $customerEmail  The customer email the subscription belongs to
	 * @param   string   $transactionId  The RO Payments transaction ID
	 * @param   integer  $times          The duration of the subscription
	 * @param   string   $interval       The interval in which the subscription should take place
	 *
	 * @return  void
	 *
	 * @since   5.0.0
	 * @throws  ApiException
	 * @throws  InvalidArgumentException
	 */
	public function createSubscription(
		string $amount,
		string $currency,
		string $startDate,
		string $description,
		string $customerEmail,
		string $transactionId,
		int $times = 0,
		string $interval = '1 months'
	): void {
		/** @var $customer object */
		if (!$customer = $this->getCustomerByEmail($customerEmail))
		{
			throw new InvalidArgumentException(
				Text::sprintf(
					'COM_ROPAYMENTS_CUSTOMER_NOT_FOUND', $customerEmail
				)
			);
		}

		if (!$this->profileId)
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_PROFILEID_NOT_SET')
			);
		}

		$params = ComponentHelper::getParams('com_jdidealgateway');
		$domain = $params->get('domain');

		if (substr($domain, -1) === '/')
		{
			$domain = substr($domain, 0, -1);
		}

		$data   = [
			'amount'      => [
				'currency' => $currency,
				'value'    => $amount
			],
			'interval'    => $interval,
			'startDate'   => $startDate,
			'description' => $description,
			'webhookUrl'  => $domain . '/cli/notify.php?transaction_id='
				. $transactionId,
		];

		if ($times)
		{
			$data['times'] = $times;
		}

		/** @var Subscription $subscription */
		$subscription = $this->mollie->customers->get($customer->customerId)
			->createSubscription(
				$data
			);

		$this->storeSubscription($subscription);
	}

	/**
	 * Store a subscription.
	 *
	 * @param   Subscription  $subscription  The subscription to store
	 *
	 * @return  void
	 *
	 * @since   5.0.0
	 *
	 * @throws  InvalidArgumentException
	 */
	private function storeSubscription(Subscription $subscription): void
	{
		if (!$this->profileId)
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_PROFILEID_NOT_SET')
			);
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Check if the subscription exists
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__jdidealgateway_subscriptions'))
			->where(
				$db->quoteName('subscriptionId') . ' = ' . $db->quote(
					$subscription->id
				)
			);
		$db->setQuery($query);

		// If the subscription ID is set, no need to store
		if ($subscriptionId = $db->loadResult())
		{
			return;
		}

		$createDate = (new Date($subscription->createdAt))->toSql();
		$startDate  = (new Date($subscription->startDate))->toSql();
		$cancelDate = $db->getNullDate();

		if ($subscription->canceledAt)
		{
			$cancelDate = new Date($subscription->canceledAt);
		}

		$query->clear()
			->insert($db->quoteName('#__jdidealgateway_subscriptions'))
			->columns(
				$db->quoteName(
					[
						'customerId',
						'subscriptionId',
						'profileId',
						'status',
						'currency',
						'amount',
						'times',
						'interval',
						'description',
						'start',
						'cancelled',
						'created'
					]
				)
			)
			->values(
				$db->quote($subscription->customerId) . ',' .
				$db->quote($subscription->id) . ',' .
				$db->quote($this->profileId) . ',' .
				$db->quote($subscription->status) . ',' .
				$db->quote($subscription->amount->currency) . ',' .
				$db->quote($subscription->amount->value) . ',' .
				$db->quote($subscription->times) . ',' .
				$db->quote($subscription->interval) . ',' .
				$db->quote($subscription->description) . ',' .
				$db->quote($startDate) . ',' .
				$db->quote($cancelDate) . ',' .
				$db->quote($createDate)
			);
		$db->setQuery($query)
			->execute();
	}

	/**
	 * Get a list of subscriptions.
	 *
	 * @param   string  $customerEmail  The customer email address
	 *
	 * @return  SubscriptionCollection  List of subscriptions.
	 *
	 * @since   5.0.0
	 *
	 * @throws  ApiException
	 */
	public function listSubscriptions($customerEmail): SubscriptionCollection
	{
		/** @var $customer object */
		if (!$customer = $this->getCustomerByEmail($customerEmail))
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_CUSTOMER_NOT_FOUND')
			);
		}

		$subscriptions = $this->mollie->customers->get($customer->customerId)
			->subscriptions();

		// Store all subscriptions if needed
		foreach ($subscriptions as $index => $subscription)
		{
			$this->storeSubscription($subscription);
		}

		return $subscriptions;
	}

	/**
	 * Get a single subscriptions.
	 *
	 * @param   string  $customerId The customer ID
	 * @param   string  $subscriptionId  The subscription ID
	 *
	 * @return  Subscription  Return the requested subscription
	 *
	 * @since   5.0.0
	 *
	 * @throws ApiException
	 */
	public function getSubscription(string $customerId, string $subscriptionId): Subscription
	{
		if (!$customer = $this->mollie->customers->get($customerId))
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_CUSTOMER_NOT_FOUND')
			);
		}

		return $customer->getSubscription($subscriptionId);
	}

	/**
	 * Load all the subscriptions for a given account.
	 *
	 * @param   string  $from        The first payment ID you want to include in your list.
	 * @param   int     $limit       The number of items to retrieve
	 * @param   array   $parameters  Optional parameters to pass in the request
	 *
	 * @return  void
	 *
	 * @since   6.3.0
	 *
	 * @throws  ApiException
	 */
	public function syncAllSubscriptions($from = null, $limit = null,
		array $parameters = []
	): void {
		if (!$this->profileId)
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_PROFILEID_NOT_SET')
			);
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__jdidealgateway_subscriptions'))
			->where(
				$db->quoteName('profileId') . ' = ' . (int) $this->profileId
			);
		$db->setQuery($query)
			->execute();

		$subscriptions = $this->mollie->subscriptions->page(
			$from, $limit, $parameters
		);
		$this->storeSubscriptions($subscriptions);

		while ($subscriptions = $subscriptions->next())
		{
			$this->storeSubscriptions($subscriptions);
		}
	}

	/**
	 * Store a subscription.
	 *
	 * @param   SubscriptionCollection  $subscriptions  List of subscriptions to store
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 */
	private function storeSubscriptions(SubscriptionCollection $subscriptions
	): void {
		/** @var Subscription $subscription */
		foreach ($subscriptions->getIterator() as $subscription)
		{
			$this->storeSubscription($subscription);
		}
	}

	/**
	 * Synchronize all the customers.
	 *
	 * @param   string  $from        The first payment ID you want to include in your list.
	 * @param   int     $limit       The number of items to retrieve
	 * @param   array   $parameters  Optional parameters to pass in the request
	 *
	 * @return  void
	 *
	 * @since   6.3.0
	 *
	 * @throws  ApiException
	 */
	public function syncAllCustomers($from = null, $limit = null,
		array $parameters = []
	): void {
		if (!$this->profileId)
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_PROFILEID_NOT_SET')
			);
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__jdidealgateway_customers'))
			->where(
				$db->quoteName('profileId') . ' = ' . (int) $this->profileId
			);
		$db->setQuery($query)
			->execute();

		$customers = $this->mollie->customers->page($from, $limit, $parameters);
		$this->storeCustomers($customers);

		while ($customers = $customers->next())
		{
			$this->storeCustomers($customers);
		}
	}

	/**
	 * Store a customer.
	 *
	 * @param   CustomerCollection  $customers  List of customers to store
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 */
	private function storeCustomers(CustomerCollection $customers): void
	{
		/** @var Customer $customer */
		foreach ($customers->getIterator() as $customer)
		{
			$this->storeCustomer($customer);
		}
	}

	/**
	 * Store a customer.
	 *
	 * @param   Customer  $customer  The customer to store
	 *
	 * @return  void
	 *
	 * @since   6.2.0
	 */
	private function storeCustomer(Customer $customer): void
	{
		$db      = Factory::getDbo();
		$created = new Date($customer->createdAt);
		$query   = $db->getQuery(true)
			->insert($db->quoteName('#__jdidealgateway_customers'))
			->columns(
				$db->quoteName(
					[
						'name',
						'email',
						'customerId',
						'created',
						'profileId'
					]
				)
			)
			->values(
				$db->quote($customer->name) . ', ' .
				$db->quote($customer->email) . ', ' .
				$db->quote($customer->id) . ', ' .
				$db->quote($created->toSql()) . ', ' .
				$db->quote($this->profileId)
			);
		$db->setQuery($query)
			->execute();
	}

	/**
	 * Cancel a subscription.
	 *
	 * @param   string  $customerEmail   The customer email address
	 * @param   string  $subscriptionId  The subscription ID to cancel
	 *
	 * @return  void
	 *
	 * @since   5.0.0
	 *
	 * @throws  ApiException
	 */
	public function cancelSubscription($customerEmail, $subscriptionId): void
	{
		/** @var $customer object */
		$customer = $this->getCustomerByEmail($customerEmail);

		/** @var Subscription $subscription */
		$subscription = $this->mollie->customers->get($customer->customerId)
			->cancelSubscription($subscriptionId);

		// Cancel the subscription in the database
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__jdidealgateway_subscriptions'))
			->set(
				$db->quoteName('status') . ' = ' . $db->quote(
					$subscription->status
				)
			)
			->set(
				$db->quoteName('cancelled') . ' = ' . $db->quote(
					(new Date)->toSql()
				)
			)
			->where(
				$db->quoteName('subscriptionId') . ' = ' . $db->quote(
					$subscriptionId
				)
			);
		$db->setQuery($query)
			->execute();
	}

	/**
	 * Get the mandates for a given customer.
	 *
	 * @param   string  $customerEmail  The customer email to get the mandates for
	 *
	 * @return  MandateCollection List of mandates.
	 *
	 * @since   5.0.0
	 *
	 * @throws  ApiException
	 */
	public function listMandates(string $customerEmail): MandateCollection
	{
		/** @var $customer Customer */
		if (!$customer = $this->getCustomerByEmail($customerEmail))
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_CUSTOMER_NOT_FOUND')
			);
		}

		$customer = $this->mollie->customers->get($customer->customerId);

		return $customer->mandates();
	}

	/**
	 * Revoke a mandate.
	 *
	 * @param   string  $customerEmail  The customer email address
	 * @param   string  $mandateId      The mandate ID to revoke
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 * @throws  ApiException
	 */
	public function revokeMandate(string $customerEmail, string $mandateId
	): void {
		/** @var $customer object */
		$customer = $this->getCustomerByEmail($customerEmail);

		/** @var Subscription $subscription */
		$this->mollie->customers->get($customer->customerId)
			->revokeMandate($mandateId);
	}

	/**
	 * Create a mandate.
	 *
	 * @param   string  $customerEmail    The customer email address
	 * @param   string  $consumerName     The consumer name
	 * @param   string  $consumerAccount  The bank account number
	 * @param   string  $consumerBic      The bank BIC code
	 *
	 * @return  Mandate The mandate response
	 *
	 * @since   5.0.0
	 * @throws ApiException
	 */
	public function createMandate(string $customerEmail, string $consumerName,
		string $consumerAccount, string $consumerBic = ''
	): Mandate {
		/** @var $customer object */
		$customer = $this->getCustomerByEmail($customerEmail);

		/** @var Subscription $subscription */
		return $this->mollie->customers->get($customer->customerId)
			->createMandate(
				[
					'method'          => MandateMethod::DIRECTDEBIT,
					'consumerName'    => $consumerName,
					'consumerAccount' => $consumerAccount,
					'consumerBic'     => $consumerBic,
				]
			);
	}

	/**
	 * Check if a customer already exists with the given email address.
	 *
	 * @param   string  $customerId  The email address to check
	 *
	 * @return  stdClass|null  The customer details or null if not found.
	 *
	 * @since   6.3.0
	 */
	private function getCustomerByCustomerId(string $customerId): ?stdClass
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'id',
						'customerId'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_customers'))
			->where(
				$db->quoteName('customerId') . ' = ' . $db->quote($customerId)
			);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Create the first payment.
	 *
	 * @param   integer  $transactionId  The RO Payments transaction ID
	 * @param   string   $email          The customer email address
	 * @param   string   $amount         The amount for the first payment
	 * @param   string   $description    The description for the first payment
	 * @param   string   $paymentMethod  The payment method to use for the first payment
	 *
	 * @return  object  The payment details
	 *
	 * @since   5.0.0
	 *
	 * @throws  \Exception
	 */
	private function createRecurringPayment(
		int $transactionId,
		string $email,
		string $amount,
		string $description,
		string $paymentMethod = ''
	) {
		/** @var $customer object */
		if (!$customer = $this->getCustomerByEmail($email))
		{
			throw new InvalidArgumentException(
				Text::_('COM_ROPAYMENTS_CUSTOMER_DOES_NOT_EXIST')
			);
		}

		return $this->mollie->payments->create(
			[
				'amount'       => [
					'currency' => 'EUR',
					'value'    => $amount
				],
				'customerId'   => $customer->customerId,
				'sequenceType' => 'recurring',
				'description'  => $description,
				'method'       => $paymentMethod,
				'webhookUrl'   => Uri::root() . 'cli/notify.php?transaction_id='
					. $transactionId,
			]
		);
	}

	/**
	 * Load the payment details.
	 *
	 * @param   string  $paymentId  The payment ID to get the details for
	 *
	 * @return  Payment  The payment details.
	 *
	 * @since   6.4.0
	 * @throws  ApiException
	 */
	public function getPayment(string $paymentId): Payment
	{
		return $this->mollie->payments->get($paymentId);
	}
}
