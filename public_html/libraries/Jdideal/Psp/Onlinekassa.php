<?php
/**
 * @package     JDiDEAL
 * @subpackage  Omnikassa
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2017 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

use Exception;
use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint\Endpoint;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Environment;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandForce;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\PaymentCompletedResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use RuntimeException;

// Load the OnlineKassa class
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Onlinekassa/vendor/autoload.php';

defined('_JEXEC') or die;

/**
 * Omnikassa processor.
 *
 * @package     JDiDEAL
 * @subpackage  Onlinekassa
 * @since       4.8.0
 * @link        https://github.com/opensdks/omnikassa2-sdk
 */
class Onlinekassa
{
	/**
	 * Database driver
	 *
	 * @var    \JDatabaseDriver
	 * @since  4.8.0
	 */
	private $db;

	/**
	 * Input processor
	 *
	 * @var    Input
	 * @since  4.8.0
	 */
	private $jinput;

	/**
	 * Array with return data from the Rabobank
	 *
	 * @var    array
	 * @since  4.8.0
	 */
	private $data = [];

	/**
	 * Set if the customer or PSP is calling
	 *
	 * @var    boolean
	 * @since  4.8.0
	 */
	private $isCustomer;

	/**
	 * Set the event that was triggered
	 *
	 * @var    string
	 * @since  4.14.2
	 */
	private $eventName;

	/**
	 * List of orders to process
	 *
	 * @var    array
	 * @since  4.14.2
	 */
	private $orders = [];

	/**
	 * The order being processed.
	 *
	 * @var     array
	 * @since   4.14.2
	 */
	private $order = [];

	/**
	 * Contruct the payment reference.
	 *
	 * @param   Input  $jinput  The input object.
	 *
	 * @since   4.8.0
	 */
	public function __construct(Input $jinput)
	{
		// Set the input
		$this->jinput = $jinput;

		// Set the database
		$this->db = Factory::getDbo();

		// Put the return data in an array, data is constructed as name=value
		$this->data['transaction_id'] = $jinput->get('transaction_id', false);

		// Set who is calling
		$this->isCustomer = $this->data['transaction_id'] !== false;

		// Get the POST body
		$data = json_decode(file_get_contents('php://input'), true);

		// Get the event name if set
		$this->eventName = $data['eventName'] ?? '';
	}

	/**
	 * Returns a list of available payment methods.
	 *
	 * @return  array  List of available payment methods.
	 *
	 * @since   4.8.0
	 */
	public function getAvailablePaymentMethods(): array
	{
		return [
			'IDEAL'      => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'VISA'       => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'),
			'MASTERCARD' => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'
			),
			'MAESTRO'    => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTRO'),
			'BANCONTACT' => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'
			),
			'VPAY'       => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VPAY'),
			'PAYPAL'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'),
		];
	}

	/**
	 * Prepare data for the form.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   object   $data     An object with transaction information.
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   4.8.0
	 *
	 * @throws  RuntimeException
	 */
	public function getForm(Gateway $jdideal, $data): array
	{
		// Load the form options
		$options = [];

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$selected   = [];
			$selected[] = strtolower($data->payment_method);
		}
		else
		{
			$selected = $jdideal->get('paymentMeanBrandList', array('ideal'));

			// If there is no choice made, set the value empty
			if ($selected[0] === 'all')
			{
				$selected[0] = '';
			}
		}

		// Process the selected payment methods
		foreach ($selected as $name)
		{
			switch (strtolower($name))
			{
				case 'ideal':
					$options[] = HTMLHelper::_(
						'select.option',
						'ideal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL')
					);
					break;
				case 'paypal':
					$options[] = HTMLHelper::_(
						'select.option',
						'paypal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL')
					);
					break;
				case 'mastercard':
					$options[] = HTMLHelper::_(
						'select.option',
						'mastercard',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD')
					);
					break;
				case 'visa':
					$options[] = HTMLHelper::_(
						'select.option',
						'visa',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA')
					);
					break;
				case 'bancontact':
					$options[] = HTMLHelper::_(
						'select.option',
						'bancontact',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT')
					);
					break;
				case 'maestro':
					$options[] = HTMLHelper::_(
						'select.option',
						'maestro',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTRO')
					);
					break;
				case 'v_pay':
					$options[] = HTMLHelper::_(
						'select.option',
						'v_pay',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VPAY')
					);
					break;
			}
		}

		$output             = array();
		$output['payments'] = $options;
		$output['redirect'] = $jdideal->get('redirect', 'wait');

		$jdideal->log(
			Text::sprintf('COM_JDIDEAL_SELECTED_CARD', $selected[0]),
			$data->logid
		);

		return $output;
	}

	/**
	 * Get the log ID.
	 *
	 * @return  integer  The ID of the log.
	 *
	 * @since   4.8.0
	 *
	 * @throws  RuntimeException
	 */
	public function getLogId(): int
	{
		$logId = 0;

		if ($this->data['transaction_id'])
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__jdidealgateway_logs'));

			$field = 'trans';

			if ( ! $this->isCustomer)
			{
				$field = 'order_id';
			}

			$query->where(
				$this->db->quoteName($field) . ' = ' . $this->db->quote(
					$this->data['transaction_id']
				)
			);
			Log::add($query->__toString(), Log::ERROR, 'com_jdidealgateway');
			$this->db->setQuery($query);

			$logId = $this->db->loadResult();
		}

		if ( ! $logId)
		{
			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND')
			);
		}

		return $logId;
	}

	/**
	 * Send payment to Rabobank.
	 *
	 * @param   Gateway  $jdideal  An instance of \Jdideal\Gateway.
	 *
	 * @return  void
	 *
	 * @since   4.8.0
	 * @throws  Exception
	 */
	public function sendPayment(Gateway $jdideal): void
	{
		$app   = Factory::getApplication();
		$logId = $this->jinput->get('logid', 0, 'int');

		// Load the stored data
		$details = $jdideal->getDetails($logId);

		if ( ! is_object($details))
		{
			throw new RuntimeException(
				Text::sprintf(
					'COM_ROPAYMENTS_NO_TRANSACTION_DETAILS', 'Onlinekassa',
					$logId
				)
			);
		}

		$trans = time();
		$jdideal->setTrans($trans, $logId);
		$notify_url = Uri::root() . 'cli/notify.php?transaction_id=' . $trans;

		try
		{
			// Replace some predefined values
			$description = $jdideal->replacePlaceholders(
				$logId, $jdideal->get('description')
			);
			$description = substr($description, 0, 100);

			// Get the amount
			$currency = $details->currency ?: $jdideal->get('currency');
			$jdideal->setCurrency($currency, $logId);
			$jdideal->log('Currency: ' . $currency, $logId);
			$cents  = $details->amount * 100;
			$amount = Money::fromCents($currency, $cents);

			// Get the language
			$language = $jdideal->get('customerLanguage');

			// Get the chosen payment method
			$paymentMethod = strtoupper($this->jinput->get('payment'));

			// Store the chosen payment method
			$jdideal->setTransactionDetails($paymentMethod, 0, $logId);

			// Set if payment method should be forced
			$paymentBrandForce = PaymentBrandForce::FORCE_ONCE;

			/**
			 * If we have no payment brand, we must set the force to null otherwise you get the error
			 * Client error response [url] https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/order [status code] 422 [reason phrase] Undefined
			 *
			 * This cannot be tested with other payment methods than iDEAL because sandbox only has iDEAL
			 */
			if (empty($paymentMethod))
			{
				$paymentMethod     = null;
				$paymentBrandForce = null;
			}

			// Get the order ID to send
			$orderNumber = $jdideal->get('orderNumber', 'order_number');

			$order = MerchantOrder::createFrom(
				[
					'merchantOrderId'   => $details->$orderNumber,
					'description'       => $description,
					'orderItems'        => null,
					'amount'            => $amount,
					'shippingDetail'    => null,
					'billingDetail'     => null,
					'language'          => $language,
					'merchantReturnURL' => $notify_url,
					'paymentBrand'      => $paymentMethod,
					'paymentBrandForce' => $paymentBrandForce
				]
			);

			// Prepare for sending
			$signingKey            = new SigningKey(
				base64_decode($jdideal->get('signingKey'))
			);
			$inMemoryTokenProvider = new InMemoryTokenProvider(
				$jdideal->get('apiKey')
			);

			$endpoint = Endpoint::createInstance(
				$this->getUrl($jdideal),
				$signingKey,
				$inMemoryTokenProvider
			);

			$redirectUrl = $endpoint->announceMerchantOrder($order);


			// Add some info to the log
			$jdideal->log('Send customer to URL: ' . $redirectUrl, $logId);

			// Send the customer to the bank
			$app->redirect($redirectUrl);
		}
		catch (Exception $exception)
		{
			$message  = $exception->getMessage();
			$response = false;

			if (method_exists($exception, 'getResponse'))
			{
				$response = $exception->getResponse();
			}

			if ($response)
			{
				$message = json_decode($response->getBody());
			}

			$jdideal->log('The payment could not be created.', $logId);
			$jdideal->log('Notify URL: ' . $notify_url, $logId);

			if (is_object($message))
			{
				$jdideal->log(json_encode($message), $logId);

				if (isset($message->errorCode))
				{
					$jdideal->log('Error Code: ' . $message->errorCode, $logId);
				}

				if (isset($message->errorMessage))
				{
					$jdideal->log(
						'Error Message: ' . $message->errorMessage, $logId
					);
				}

				if (isset($message->consumerMessage))
				{
					$jdideal->log(
						'Error Message: ' . $message->consumerMessage, $logId
					);
					throw new RuntimeException($message->consumerMessage);
				}
			}
			elseif (is_string($message))
			{
				$jdideal->log('Error Message: ' . $message, $logId);
			}
			else
			{
				echo $message;
			}
		}
	}

	/**
	 * Get the URL to send the request to.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @return  string  The URL to call.
	 *
	 * @since   4.8.0
	 */
	private function getUrl(Gateway $jdideal): string
	{
		if ($jdideal->get('testmode', 1))
		{
			return Environment::SANDBOX;
		}

		return Environment::PRODUCTION;
	}

	/**
	 * Check the transaction status.
	 *
	 * isOK            = Set if the validation is OK
	 * card            = The payment method used by the customer
	 * suggestedAction = The result of the transaction
	 * error_message   = An error message in case there is an error with the transaction
	 * consumer        = Array with info about the customer
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   integer  $logId    The ID of the transaction log.
	 *
	 * @return  array  Array of transaction details.
	 *
	 * @since   4.8.0
	 *
	 * @throws  RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, int $logId): array
	{
		// Get the status information
		$status         = [];
		$status['isOK'] = true;
		$status['card'] = '';

		// Get the customer info, not available
		$status['consumer'] = array();

		if ($this->isCustomer)
		{
			// Log the received data
			$orderId       = $this->jinput->get('order_id');
			$paymentStatus = $this->jinput->get('status');
			$signature     = $this->jinput->get('signature');

			// Log the received data
			$jdideal->log('Order ID:' . $orderId, $logId);
			$jdideal->log('Status:' . $paymentStatus, $logId);
			$jdideal->log('Signature:' . $signature, $logId);

			// Store the payment ID, needed for retrieving order status at a later time
			if ($signature)
			{
				$jdideal->setPaymentId(
					'transaction_id=' . $this->getTransactionId() . '&order_id='
					. $orderId . '&status=' . $paymentStatus . '&signature='
					. $signature,
					$logId
				);
			}

			// Verify the payment
			$signingKey = new SigningKey(
				base64_decode($jdideal->get('signingKey'))
			);
			$paymentCompletedResponse
			            = PaymentCompletedResponse::createInstance(
				$orderId,
				$paymentStatus,
				$signature,
				$signingKey
			);

			// Check if payment completed
			if ( ! $paymentCompletedResponse)
			{
				throw new RuntimeException(
					'The payment completed response was invalid.'
				);
			}

			// Get the payment result
			$validatedStatus = $paymentCompletedResponse->getStatus();
		}
		else
		{
			$validatedStatus = $this->order['orderStatus'];
		}

		// Store the details
		$details = $jdideal->getDetails($logId);
		$jdideal->setTransactionDetails($details->card, 1, $logId);

		switch ($validatedStatus)
		{
			case 'COMPLETED':
				$status['suggestedAction'] = 'SUCCESS';
				break;
			case 'CANCELLED':
				$status['suggestedAction'] = 'CANCELLED';
				break;
			case 'EXPIRED':
				$status['suggestedAction'] = 'EXPIRED';
				break;
			case 'IN_PROGRESS':
			default:
				$status['suggestedAction'] = 'OPEN';
				break;
		}

		return $status;
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return  integer  The ID of the transaction.
	 *
	 * @since   4.8.0
	 *
	 * @throws  RuntimeException
	 */
	public function getTransactionId()
	{
		if ($this->isCustomer()
			&& ! array_key_exists(
				'transaction_id', $this->data
			))
		{
			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND')
			);
		}

		// Payment provider is calling, we need to get the trans value from the database
		if ( ! $this->isCustomer)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('trans'))
				->from($this->db->quoteName('#__jdidealgateway_logs'));
			$query->where(
				$this->db->quoteName('order_id') . ' = ' . $this->db->quote(
					$this->data['transaction_id']
				)
			);
			$this->db->setQuery($query);

			return $this->db->loadResult();
		}

		// Get the transaction ID
		return $this->data['transaction_id'];
	}

	/**
	 * Check who is knocking at the door.
	 *
	 * @return  boolean  True if it is the customer | False if it is the PSP.
	 *
	 * @since   4.8.0
	 */
	public function isCustomer(): bool
	{
		return $this->isCustomer;
	}

	/**
	 * Do a status pull for all open orders.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @return  void
	 *
	 * @since   4.14.2
	 */
	public function getOrders(Gateway $jdideal): void
	{
		Log::addLogger(
			[
				'text_file' => 'com_jdidealgateway.errors.php'
			],
			Log::ERROR,
			['com_jdidealgateway']
		);

		// Get the POST body
		$data = json_decode(file_get_contents('php://input'), true);

		// Validate the request
		$signatureFields = [
			$data['authentication'],
			$data['expiry'],
			$data['eventName'],
			$data['poiId']
		];

		$signingKey = new SigningKey(
			base64_decode($jdideal->get('signingKey'))
		);

		// Calculate the hash
		$signature = hash_hmac(
			'sha512', implode(',', $signatureFields),
			$signingKey->getSigningData()
		);

		if ($signature !== $data['signature'])
		{
			Log::add(
				'Calculated: ' . $signature, Log::ERROR, 'com_jdidealgateway'
			);
			Log::add(
				'Supplied: ' . $data['signature'], Log::ERROR,
				'com_jdidealgateway'
			);

			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_ONLINEKASSA_SIGNATURE_MISMATCH')
			);
		}

		// Get the order to check the status for
		$http = HttpFactory::getHttp(null, ['curl', 'stream']);

		$headers                  = [];
		$headers['Authorization'] = 'Bearer ' . $data['authentication'];

		// Call the bank
		$result = $http->get(
			$this->getUrl($jdideal)
			. 'order/server/api/events/results/merchant.order.status.changed',
			$headers
		);
		$data   = json_decode($result->body, true);

		if ( ! empty($data['errorCode']))
		{
			Log::add($data['errorMessage'], Log::ERROR, 'com_jdidealgateway');

			throw new RuntimeException($data['errorMessage']);
		}

		// Verify the data integrity
		$signatureFields = [
			$data['moreOrderResultsAvailable'] ? 'true' : 'false',
		];

		array_walk(
			$data['orderResults'],
			static function ($result) use (&$signatureFields) {
				$signatureFields[] = $result['merchantOrderId'];
				$signatureFields[] = $result['omnikassaOrderId'];
				$signatureFields[] = $result['poiId'];
				$signatureFields[] = $result['orderStatus'];
				$signatureFields[] = $result['orderStatusDateTime'];
				$signatureFields[] = $result['errorCode'];
				$signatureFields[] = $result['paidAmount']['currency'];
				$signatureFields[] = $result['paidAmount']['amount'];
				$signatureFields[] = $result['totalAmount']['currency'];
				$signatureFields[] = $result['totalAmount']['amount'];
			}
		);

		$signature = hash_hmac(
			'sha512', implode(',', $signatureFields),
			$signingKey->getSigningData()
		);

		if ($signature !== $data['signature'])
		{
			Log::add(
				'Calculated: ' . $signature, Log::ERROR, 'com_jdidealgateway'
			);
			Log::add(
				'Supplied: ' . $data['signature'], Log::ERROR,
				'com_jdidealgateway'
			);

			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_ONLINEKASSA_SIGNATURE_MISMATCH')
			);
		}

		// Process order statuses
		$this->orders = $data['orderResults'];
	}

	/**
	 * Process transactions provided by the bank.
	 *
	 * @return  boolean  True to process | False when done
	 *
	 * @since   4.14.2
	 */
	public function processTransaction(): bool
	{
		// Get the order
		$order = array_shift($this->orders);

		// If there is no order, stop processing
		if ( ! $order)
		{
			return false;
		}

		// Set the data for further processing
		$this->order      = $order;
		$this->isCustomer = false;

		// If this is not the customer, the merchant Order ID is actually the order number
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('order_id'))
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where(
				$db->quoteName('order_number') . ' = ' . $db->quote(
					$order['merchantOrderId']
				)
			);
		$db->setQuery($query);

		$this->data['transaction_id'] = $db->loadResult();

		return true;
	}

	/**
	 * Tell RO Payments if status can be checked based on customer.
	 *
	 * @return  boolean  True if user status can be used | False otherwise.
	 *
	 * @since   4.13.0
	 */
	public function canUseCustomerStatus(): bool
	{
		return true;
	}

	/**
	 * Tell RO Payments the bank must be called instead of the bank calling us
	 *
	 * @return  boolean  True if the bank must be called | False if the bank calls us.
	 *
	 * @since   4.14.2
	 */
	public function callHome(): bool
	{
		if ($this->eventName === 'merchant.order.status.changed')
		{
			return true;
		}

		return false;
	}
}

/**
 * In memory token provider.
 *
 * @package  JDiDEAL
 * @since    4.8.0
 */
class InMemoryTokenProvider extends TokenProvider
{
	private $map = [];

	/**
	 * Construct the in memory token provider with the given refresh token.
	 *
	 * @param   string  $refreshToken  The refresh token used to retrieve the
	 *                                 access tokens with.
	 *
	 * @since   4.8.0
	 */
	public function __construct($refreshToken)
	{
		$this->setValue(static::REFRESH_TOKEN, $refreshToken);
	}

	/**
	 * Store the value by the given key.
	 *
	 * @param   string  $key    They key to store
	 * @param   string  $value  The value to store
	 *
	 * @return  void
	 *
	 * @since   4.8.0
	 */
	protected function setValue($key, $value)
	{
		$this->map[$key] = $value;
	}

	/**
	 * Retrieve the value for the given key.
	 *
	 * @param   string  $key  The key to get the value for
	 *
	 * @return string Value of the given key or null if it does not exists.
	 *
	 * @since   4.8.0
	 */
	protected function getValue($key)
	{
		return array_key_exists($key, $this->map)
			? $this->map[$key]
			:
			null;
	}

	/**
	 * Optional functionality to flush your systems.
	 * It is called after storing all the values of the access token and
	 * can be used for example to clean caches or reload changes from the
	 * database.
	 *
	 * @return  void
	 *
	 * @since   4.8.0
	 */
	protected function flush(): void
	{
	}
}
