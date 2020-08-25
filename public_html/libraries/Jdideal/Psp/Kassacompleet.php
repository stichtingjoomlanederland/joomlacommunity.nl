<?php
/**
 * @package     JDiDEAL
 * @subpackage  Kassacompleet
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

defined('_JEXEC') or die;

use Exception;
use Ginger\Ginger;
use InvalidArgumentException;
use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use RuntimeException;

/**
 * Kassa Compleet processor.
 *
 * @package     JDiDEAL
 * @subpackage  Kassacompleet
 *
 * @since       4.0.0
 * @link        https://s3-eu-west-1.amazonaws.com/wl1-apidocs/api.kassacompleet.nl/index.html
 *
 * @link        https://github.com/gingerpayments/ginger-php
 */
class Kassacompleet
{
	/**
	 * Database driver
	 *
	 * @var    \JDatabaseDriver
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * Input processor
	 *
	 * @var    Input
	 * @since  4.0.0
	 */
	private $input;

	/**
	 * API URL
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	private $apiUrl = 'https://api.kassacompleet.nl';

	/**
	 * Array with return data from Kassacompleet
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	private $data;

	/**
	 * Set if the customer or PSP is calling
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	private $isCustomer;

	/**
	 * Construct the payment reference.
	 *
	 * @param   Input  $input  The input object.
	 *
	 * @since   4.0.0
	 */
	public function __construct(Input $input)
	{
		// Set the input
		$this->input = $input;

		// Set the database
		$this->db = Factory::getDbo();

		// Put the return data in an array, data is constructed as name=value
		$this->data['transactionId'] = $input->get('order_id');

		// Set who is calling
		$this->isCustomer = $input->get('output', '') === 'customer';

		// Load the KassaCompleet classes
		require_once JPATH_LIBRARIES . '/Jdideal/Psp/Kassacompleet/vendor/autoload.php';
	}

	/**
	 * Prepare data for the form.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   object   $data     An object with transaction information.
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   2.13
	 *
	 * @throws   RuntimeException
	 * @throws   InvalidArgumentException
	 */
	public function getForm(Gateway $jdideal, $data)
	{
		// Load the form options
		$options = [];
		$banks   = false;

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$selected   = [];
			$selected[] = strtolower($data->payment_method);
		}
		else
		{
			$selected = $jdideal->get('payment', array('all'));

			// If there is no choice made, set the value empty
			if ($selected[0] === 'all')
			{
				$selected = array_flip($this->getAvailablePaymentMethods());
			}
		}

		$output             = [];
		$output['redirect'] = $jdideal->get('redirect', 'wait');

		// Process the selected payment methods
		foreach ($selected as $name)
		{
			switch ($name)
			{
				case 'credit-card':
					$options[] = HTMLHelper::_(
						'select.option',
						'credit-card',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CREDITCARD')
					);
					break;
				case 'paypal':
					$options[] = HTMLHelper::_(
						'select.option',
						'paypal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL')
					);
					break;
				case 'bank-transfer':
					$options[] = HTMLHelper::_(
						'select.option',
						'bank-transfer',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANKTRANSFER')
					);
					break;
				case 'rembours':
					$options[] = HTMLHelper::_(
						'select.option',
						'rembours',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CASHONDELIVERY')
					);
					break;
				case 'ideal':
					$options[] = HTMLHelper::_(
						'select.option',
						'ideal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL')
					);

					// Instantiate Kassa Compleet
					$kassacompleet = Ginger::createClient($this->apiUrl, $jdideal->get('apiKey'));

					// Load the banks
					$banks = $kassacompleet->getIdealIssuers();

					// Set the timer to wait
					$output['redirect'] = 'wait';
					break;
				default:
					$options[] = '';
					break;
			}
		}

		// Set the payment options
		$output['payments'] = $options;

		// Add any banks if there are
		if ($banks)
		{
			$output['banks'] = $banks;
		}

		return $output;
	}

	/**
	 * Returns a list of available payment methods.
	 *
	 * @return  array  List of available payment methods.
	 *
	 * @since   3.0.0
	 */
	public function getAvailablePaymentMethods(): array
	{
		return [
			'ideal'         => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'credit-card'   => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CREDITCARD'),
			'paypal'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'),
			'bank-transfer' => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANKTRANSFER'),
			'rembours'      => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CASHONDELIVERY'),
		];
	}

	/**
	 * Get the log ID.
	 *
	 * @return  integer  The ID of the log.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getLogId(): int
	{
		$logId = false;

		if ($this->data['transactionId'])
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__jdidealgateway_logs'))
				->where($this->db->quoteName('trans') . ' = ' . $this->db->quote($this->data['transactionId']));
			$this->db->setQuery($query);

			$logId = $this->db->loadResult();
		}

		if (!$logId)
		{
			throw new RuntimeException(Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
		}

		return $logId;
	}

	/**
	 * Send payment to Kassacompleet.
	 *
	 * @param   Gateway  $jdideal  An instance of \Jdideal\Gateway.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function sendPayment(Gateway $jdideal): void
	{
		$app   = Factory::getApplication();
		$logId = $this->input->get('logid', 0, 'int');

		// Load the Kassacompleet class
		$kassacompleet = Ginger::createClient($this->apiUrl, $jdideal->get('apiKey'));

		// Load the stored data
		$details = $jdideal->getDetails($logId);

		if (!is_object($details))
		{
			throw new RuntimeException(
				Text::sprintf('COM_ROPAYMENTS_NO_TRANSACTION_DETAILS', 'Kassacompleet', $logId)
			);
		}

		$notify_url = Uri::root() . 'cli/notify.php?output=customer';

		try
		{
			// Replace some predefined values
			$description = $jdideal->replacePlaceholders($logId, $jdideal->get('description'));
			$description = substr($description, 0, 32);

			// Load the chosen payment method
			$paymentMethod = $this->input->get('payment');
			$jdideal->log(Text::sprintf('COM_JDIDEAL_SELECTED_CARD', $paymentMethod), $logId);

			// Create the payload
			$orderNumber = $jdideal->get('orderNumber', 'order_number');
			$payload     =
				[
					'currency'          => $details->currency ?: 'EUR',
					'amount'            => $details->amount * 100,
					'merchant_order_id' => $details->$orderNumber,
					'description'       => $description,
					'return_url'        => $notify_url,
					'transactions'      => [
						'payment_method'         => $paymentMethod,
						'payment_method_details' => [
							'issuer_id' => $this->input->get('banks')
						]
					],
				];

			// Store the currency
			$jdideal->setCurrency($payload['currency'], $logId);
			$jdideal->log('Currency: ' . $payload['currency'], $logId);

			// Create the order at ING
			$order = $kassacompleet->createOrder($payload);

			if (!$order)
            {
                throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_TRANSACTION_NOT_CREATED'));
            }

			// Store the transaction ID
			$jdideal->setTrans($order['id'], $logId);

			// Get the payment URL
			$paymentUrl = $order['order_url'];

			// Store the response in the log
			foreach ($order as $name => $value)
			{
				if (is_string($value))
				{
					$jdideal->log($name . ': ' . $value, $logId);
				}
			}

			// Check if we need to send the customer to the bank
			if ($order['status'] === 'new' && $paymentUrl)
			{
				// Send the customer to the bank if needed
				$jdideal->log('Send customer to URL: ' . $paymentUrl, $logId);
				$app->redirect($paymentUrl);
			}

			// No need for redirect e.g. bank transfer, go straight to the notify URL
			$app->redirect($notify_url . '&transactionId=' . $order['id']);
		}
		catch (RuntimeException $exception)
		{
			$jdideal->log('The payment could not be created.', $logId);
			$jdideal->log('Error: ' . $exception->getMessage(), $logId);
			$jdideal->log('Notify URL: ' . $notify_url, $logId);

			throw new RuntimeException($exception->getMessage());
		}
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
	 * @param   int      $logId    The ID of the transaction log.
	 *
	 * @return  array  Array of transaction details.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, $logId): array
	{
		$status         = [];
		$status['isOK'] = false;

		// Load the Kassa Compleet class
		$kassacompleet = Ginger::createClient($this->apiUrl, $jdideal->get('apiKey'));

		// Get the transaction ID
		$transactionId = $this->getTransactionId();

		// Get the order status
		$payment = $kassacompleet->getOrder($transactionId);

		$jdideal->log(json_encode($payment), $logId);

		$status['isOK'] = true;
		$status['card'] = $payment['transactions'][0]['payment_method'];

		$jdideal->log('Received payment status: ' . $payment['status'], $logId);

		if ($payment['status'] !== 'Success' && $status['card'] === 'bank-transfer')
		{
			$status['isOK']            = true;
			$status['error_message']   = '';
			$status['suggestedAction'] = 'TRANSFER';
			$status['reference']       = $payment['transactions'][0]['payment_method_details']['reference'];
			$status['consumer']        = [];

			$jdideal->setTransactionDetails(
				$status['card'],
				0,
				$logId,
				$payment['transactions'][0]['payment_method_details']['reference']
			);

			$jdideal->log('Payment reference: ' . $payment['transactions'][0]['payment_method_details']['reference'], $logId);
		}
		else
		{
			switch (strtolower($payment['status']))
			{
				case 'cancelled':
				case 'refunded':
				case 'charged_back':
					$status['suggestedAction'] = 'CANCELLED';
					break;
				case 'fail':
				case 'expired':
					$status['suggestedAction'] = 'FAILURE';
					break;
				case 'success':
				case 'completed':
					$status['suggestedAction'] = 'SUCCESS';
					break;
				case 'processing':
				default:
					$status['suggestedAction'] = 'OPEN';
					break;
			}

			$jdideal->setTransactionDetails($status['card'], 0, $logId);

			// Get the customer info
			$status['consumer'] = (array) $payment['transactions'][0]['payment_method_details'];

			if (empty($status['consumer']))
			{
				$status['consumer']['consumerAccount'] = '';
				$status['consumer']['consumerName']    = '';
				$status['consumer']['consumerCity']    = '';
			}
		}

		return $status;
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return  string  The ID of the transaction.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getTransactionId(): string
	{
		if (!array_key_exists('transactionId', $this->data))
		{
			throw new RuntimeException(Text::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND'));
		}

		// Get the transaction ID
		return $this->data['transactionId'];
	}

	/**
	 * Check who is knocking at the door.
	 *
	 * @return  boolean  True if it is the customer | False if it is the PSP.
	 *
	 * @since   4.0
	 */
	public function isCustomer(): bool
	{
		return $this->isCustomer;
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
		return false;
	}
}
