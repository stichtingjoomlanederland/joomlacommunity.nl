<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

namespace Jdideal\Psp;

defined('_JEXEC') or die;

use Exception;
use Jdideal\Gateway;
use Jdideal\Recurring\Mollie as RecurringMollie;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use RuntimeException;

/**
 * Mollie processor.
 *
 * @package     JDiDEAL
 * @subpackage  Mollie
 * @since       2.12
 */
class Mollie
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
	 * @var    \JInput
	 * @since  4.0.0
	 */
	private $jinput;

	/**
	 * Array with return data from Mollie
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	private $data = [];

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
	 * @param   \Jinput  $jinput  The input object.
	 *
	 * @since   4.0.0
	 */
	public function __construct(\JInput $jinput)
	{
		// Set the input
		$this->jinput = $jinput;

		// Set the database
		$this->db = Factory::getDbo();

		// Put the return data in an array, data is constructed as name=value
		$this->data['id']             = $jinput->post->get('id', false);
		$this->data['transaction_id'] = $jinput->get('transaction_id');

		// Set who is calling
		$this->isCustomer = $jinput->get('output', '') === 'customer';

		// Load the Mollie classes
		require_once JPATH_LIBRARIES
			. '/Jdideal/Psp/Mollie/vendor/autoload.php';
	}

	/**
	 * Returns a list of available payment methods.
	 *
	 * @return  array  List of available payment methods.
	 *
	 * @since   3.0
	 */
	public function getAvailablePaymentMethods(): array
	{
		return [
			'ideal'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'creditcard'   => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_CREDITCARD'
			),
			'bancontact'   => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'
			),
			'sofort'       => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT'
			),
			'paypal'       => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'
			),
			'paysafecard'  => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYSAFECARD'
			),
			'banktransfer' => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_BANKTRANSFER'
			),
			'bitcoin'      => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_BITCOIN'
			),
			'kbc'          => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_KBC'),
			'belfius'      => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_BELFIUS'
			),
			'giftcard'     => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_GIFTCARD'
			),
			'inghomepay'   => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_INGHOMEPAY'
			),
			'eps'          => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_EPS'),
			'giropay'      => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_GIROPAY'
			),
			'przelewy24'   => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_PRZELEWY24'
			),
			'applepay'     => Text::_(
				'COM_JDIDEALGATWAY_PAYMENT_METHOD_APPLEPAY'
			),
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
	 * @since   2.13.0
	 *
	 * @throws   RuntimeException
	 * @throws   \InvalidArgumentException
	 * @throws   Exception
	 */
	public function getForm(Gateway $jdideal, $data): array
	{
		// Load the form options
		$options = [];
		$cards   = false;
		$banks   = false;

		$selected = $jdideal->get('payment', ['ideal']);

		// If there is no choice made, set the value empty
		if ($selected[0] === 'all')
		{
			$selected[0] = '';
		}

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$selected   = [];
			$selected[] = strtolower($data->payment_method);
		}

		// Process the selected payment methods
		foreach ($selected as $name)
		{
			switch ($name)
			{
				case 'creditcard':
					$options[] = HTMLHelper::_(
						'select.option',
						'creditcard',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CREDITCARD')
					);
					break;
				case 'bancontact':
					$options[] = HTMLHelper::_(
						'select.option',
						'bancontact',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT')
					);
					break;
				case 'paypal':
					$options[] = HTMLHelper::_(
						'select.option',
						'paypal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL')
					);
					break;
				case 'paysafecard':
					$options[] = HTMLHelper::_(
						'select.option',
						'paysafecard',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYSAFECARD')
					);
					break;
				case 'banktransfer':
					$options[] = HTMLHelper::_(
						'select.option',
						'banktransfer',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANKTRANSFER')
					);
					break;
				case 'sofort':
					$options[] = HTMLHelper::_(
						'select.option',
						'sofort',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT')
					);
					break;
				case 'bitcoin':
					$options[] = HTMLHelper::_(
						'select.option',
						'bitcoin',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BITCOIN')
					);
					break;
				case 'belfius':
					$options[] = HTMLHelper::_(
						'select.option',
						'belfius',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BELFIUS')
					);
					break;
				case 'kbc':
					$options[] = HTMLHelper::_(
						'select.option',
						'kbc',
						Text::_(
							'COM_JDIDEALGATWAY_PAYMENT_METHOD_KBC'
						)
					);
					break;
				case 'inghomepay':
					$options[] = HTMLHelper::_(
						'select.option',
						'inghomepay',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_INGHOMEPAY')
					);
					break;
				case 'eps':
					$options[] = HTMLHelper::_(
						'select.option',
						'eps',
						Text::_(
							'COM_JDIDEALGATWAY_PAYMENT_METHOD_EPS'
						)
					);
					break;
				case 'giropay':
					$options[] = HTMLHelper::_(
						'select.option',
						'giropay',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_GIROPAY')
					);
					break;
				case 'przelewy24':
					$options[] = HTMLHelper::_(
						'select.option',
						'przelewy24',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PRZELEWY24')
					);
					break;
				case 'applepay':
					$options[] = HTMLHelper::_(
						'select.option',
						'applepay',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_APPLEPAY')
					);
					break;
				case 'giftcard':
					$options[] = HTMLHelper::_(
						'select.option',
						'giftcard',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_GIFTCARD')
					);
					$cards     = [];
					$cards[]   = HTMLHelper::_(
						'select.option',
						'',
						Text::_(
							'COM_ROPAYMENTS_SELECT_GIFTCARD'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'nationalebioscoopbon',
						Text::_(
							'COM_ROPAYMENTS_PAYMENT_GIFTCARD_NATIONALEBIOSCOOPBON'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'nationaleentertainmentcard',
						Text::_(
							'COM_ROPAYMENTS_PAYMENT_GIFTCARD_NATIONALEENTERTAINMENTCARD'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'kunstencultuurcadeaukaart',
						Text::_(
							'COM_ROPAYMENTS_PAYMENT_GIFTCARD_KUNSTENCULTUURCADEAUKAART'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'podiumcadeaukaart',
						Text::_(
							'COM_ROPAYMENTS_PAYMENT_GIFTCARD_PODIUMCADEAUKAART'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'vvvgiftcard',
						Text::_('COM_ROPAYMENTS_PAYMENT_GIFTCARD_VVVGIFTCARD')
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'webshopgiftcard',
						Text::_(
							'COM_ROPAYMENTS_PAYMENT_GIFTCARD_WEBSHOPGIFTCARD'
						)
					);
					$cards[]   = HTMLHelper::_(
						'select.option',
						'yourgift',
						Text::_('COM_ROPAYMENTS_PAYMENT_GIFTCARD_YOURGIFT')
					);
					break;
				case 'ideal':
					$options[] = HTMLHelper::_(
						'select.option',
						'ideal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL')
					);

					// Load the Mollie class
					$mollie = new MollieApiClient;
					$mollie->setApiKey($jdideal->get('profile_key'));
					$methods = $mollie->methods->get(
						'ideal',
						['include' => 'issuers']
					);

					foreach ($methods->issuers as $issuer)
					{
						$banks['Nederland']['items'][] = HTMLHelper::_(
							'select.option',
							$issuer->id,
							$issuer->name
						);
					}
					break;
				default:
					$options[] = '';
					break;
			}
		}

		$output             = [];
		$output['payments'] = $options;
		$output['redirect'] = $jdideal->get('redirect', 'wait');

		if ($cards)
		{
			$output['cards'] = $cards;
		}

		if ($banks)
		{
			$output['banks'] = $banks;
		}

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
	 * @since   4.0.0
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
				->from($this->db->quoteName('#__jdidealgateway_logs'))
				->where(
					$this->db->quoteName('trans') . ' = '
					. $this->db->quote(
						$this->data['transaction_id']
					)
				);
			$this->db->setQuery($query);

			$logId = (int) $this->db->loadResult();
		}

		if (!$logId)
		{
			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND')
			);
		}

		return $logId;
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
		if (!array_key_exists('transaction_id', $this->data))
		{
			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND')
			);
		}

		return $this->data['transaction_id'];
	}

	/**
	 * Get the payment ID.
	 *
	 * @return  string  The ID of the transaction.
	 *
	 * @since   6.4.0
	 * @throws  RuntimeException
	 */
	public function getPaymentId(): string
	{
		if (!array_key_exists('id', $this->data))
		{
			throw new RuntimeException(
				Text::_('COM_ROPAYMENTS_NO_PAYMENTID_FOUND')
			);
		}

		return $this->data['id'];
	}

	/**
	 * Send payment to Mollie.
	 *
	 * @param   Gateway  $jdideal  An instance of \Jdideal\Gateway.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 * @throws  ApiException
	 * @throws  Exception
	 */
	public function sendPayment(Gateway $jdideal): void
	{
		$app   = Factory::getApplication();
		$logId = $this->jinput->get('logid', 0, 'int');

		// Load the Mollie class
		$mollie = new MollieApiClient;
		$mollie->setApiKey($jdideal->get('profile_key'));

		// Load the stored data
		$details = $jdideal->getDetails($logId);

		if (!is_object($details))
		{
			throw new RuntimeException(
				Text::sprintf(
					'COM_ROPAYMENTS_NO_TRANSACTION_DETAILS',
					'Mollie',
					$logId
				)
			);
		}

		$trans = time();
		$jdideal->setTrans($trans, $logId);
		$notifyUrl = Uri::root() . 'cli/notify.php?transaction_id=' . $trans;

		// Load the addon
		$addon = $jdideal->getAddon($details->origin);

		// Replace some predefined values
		$description = $jdideal->replacePlaceholders(
			$logId,
			$jdideal->get(
				'description'
			)
		);
		$description = substr($description, 0, 32);

		// Load the chosen payment method
		$paymentMethod = $this->jinput->get('payment', '');

		switch ($paymentMethod)
		{
			case 'ideal':
				$issuerID = $this->jinput->get('banks', '');
				break;
			case 'giftcard':
				$issuerID = $this->jinput->get('cards', '');
				break;
			default:
				$issuerID = '';
				break;
		}

		// Store the chosen payment method
		$jdideal->setTransactionDetails($paymentMethod, 0, $logId);

		// Store the currency
		$currency = $details->currency ?: 'EUR';
		$jdideal->setCurrency($currency, $logId);
		$jdideal->log('Currency: ' . $currency, $logId);

		// Build the amount
		$amount = number_format($details->amount, 2, '.', '');

		// Determine the language
		$language     = Factory::getLanguage();
		$languageTag  = $language->getTag();
		$languageCode = '';
		$languageMap  = $jdideal->get('languageMap', []);

		array_walk(
			$languageMap,
			static function ($map) use ($languageTag, &$languageCode) {
				if ($map->joomlaLanguage === $languageTag)
				{
					$languageCode = $map->mollieLanguage;
				}
			}
		);

		try
		{
			// Check if we need to setup recurring
			if ($jdideal->get('recurring', false))
			{
				$customer = $addon->getCustomerInformation($details->order_id);

				// Get the email
				$customerEmail = $customer['billing']->email;
				$customerName  = $customer['billing']->name;

				// Get the name
				if (isset($customer['billing']->lastname))
				{
					$customerName = $customer['billing']->firstname ?? '';

					$customerName .= $customer['billing']->lastname;
				}

				// Load the recurring handler
				$recurring = new RecurringMollie;

				// Set the API key
				$recurring->setApiKey($jdideal->get('profile_key'));

				$recurring->createCustomer(
					$customerName, $customerEmail, $jdideal->getProfileId()
				);

				// Create a first payment
				$payment = $recurring->createFirstPayment(
					$trans,
					$customerEmail,
					$currency,
					$amount,
					$description,
					$paymentMethod
				);

				// Store the payment ID
				$jdideal->setPaymentId($payment->id, $logId);
			}
			else
			{
				// Build the metadata to send to Mollie
				$orderNumber = $jdideal->get('orderNumber', 'order_number');

				$metadata = ['order_id' => $details->$orderNumber];

				// Set the payment parameters
				$paymentParameters = [
					'amount'      => [
						'currency' => $currency,
						'value'    => $amount,
					],
					'description' => $description,
					'redirectUrl' => $notifyUrl . '&output=customer',
					'webhookUrl'  => $notifyUrl,
					'metadata'    => $metadata,
					'locale'      => $languageCode,
				];

				// Need customer information for the banktransfer
				if ($paymentMethod === 'banktransfer')
				{
					// Set the status to transfer as it works different than other payment options
					$jdideal->status('TRANSFER', $logId);

					// Load the customer details
					$customer = $addon->getCustomerInformation(
						$details->order_id
					);

					if ($customer)
					{
						$paymentParameters['billingEmail']
							= $customer['billing']->email;

						$jdideal->log(
							'Email: ' . $customer['billing']->email,
							$logId
						);
					}
				}

				if ($paymentMethod)
				{
					$paymentParameters['method'] = $paymentMethod;
					$paymentParameters['issuer'] = $issuerID;
				}

				$payment = $mollie->payments->create($paymentParameters);

				$jdideal->setPaymentId($payment->id, $logId);

				// Add some info to the log
				$jdideal->log(
					'Send customer to URL: ' . $payment->getCheckoutUrl(),
					$logId
				);
			}

			// Send the customer to the bank
			$app->redirect($payment->getCheckoutUrl());
		}
		catch (ApiException $exception)
		{
			$jdideal->log('The payment could not be created.', $logId);
			$jdideal->log('Error: ' . $exception->getMessage(), $logId);
			$jdideal->log('Notify URL: ' . $notifyUrl, $logId);

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
	 * @since   2.13.0
	 *
	 * @throws  ApiException
	 * @throws  RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, int $logId): array
	{
		// Log the received data
		foreach ($this->data as $name => $value)
		{
			$jdideal->log($name . ':' . $value, $logId);
		}

		$status         = [];
		$status['isOK'] = false;

		// Check if we have a banktransfer, in that case status is OK
		$details = $jdideal->getDetails($logId);

		if ($details->card === 'banktransfer')
		{
			$status['isOK']            = true;
			$status['suggestedAction'] = 'TRANSFER';
		}

		if (array_key_exists('id', $this->data) && $this->data['id'])
		{
			// Store the payment ID, needed for retrieving order status at a later time
			$jdideal->setPaymentId($this->data['id'], $logId);

			// Load the Mollie class
			$mollie = new MollieApiClient;
			$mollie->setApiKey($jdideal->get('profile_key'));

			$payment = $mollie->payments->get($this->data['id']);

			$status['isOK'] = true;
			$status['card'] = $payment->method;

			$jdideal->log(
				'Received payment status: ' . $payment->status,
				$logId
			);
			$jdideal->log('Has chargebacks: ' . $payment->hasChargebacks() ? 'Yes' : 'No', $logId);
			$jdideal->log('Received card: ' . $payment->method, $logId);

			if ($status['card'] === 'banktransfer'
				&& !in_array(
					$payment->status,
					['paid', 'cancelled'],
					true
				))
			{
				$status['isOK']            = true;
				$status['error_message']   = '';
				$status['suggestedAction'] = 'TRANSFER';
				$status['consumer']        = [];

				$jdideal->setTransactionDetails($status['card'], 0, $logId);
			}
			else
			{
				switch ($payment->status)
				{
					case 'open':
					case 'pending':
						$status['suggestedAction'] = 'OPEN';
						break;
					case 'canceled':
					case 'cancelled':
						$status['suggestedAction'] = 'CANCELLED';
						break;
					case 'refunded':
						$status['suggestedAction'] = 'REFUNDED';
						break;
					case 'charged_back':
						$status['suggestedAction'] = 'CHARGEBACK';
						break;
					case 'fail':
					case 'failed':
						$status['suggestedAction'] = 'FAILURE';
						break;
					case 'expired':
						$status['suggestedAction'] = 'EXPIRED';
						break;
					case 'paid':
						$status['suggestedAction'] = 'SUCCESS';
						break;
				}

				if ($payment->hasChargebacks())
				{
					$status['suggestedAction'] = 'CHARGEBACK';
				}

				if ($payment->hasRefunds())
				{
					$status['suggestedAction'] = 'REFUNDED';
				}

				$jdideal->setTransactionDetails($status['card'], 1, $logId);

				$status['consumer'] = (array) $payment->details;

				if (empty($status['consumer']))
				{
					$status['consumer']['consumerAccount'] = '';
					$status['consumer']['consumerName']    = '';
					$status['consumer']['consumerBic']     = '';
				}
			}

			// See if we need to do any post-processing for recurring
			$jdideal->log('Payment sequence: ' . $payment->sequenceType, $logId);

			if ($jdideal->get('recurring', false)
				&& $payment->hasSequenceTypeFirst() === true
				&& strtoupper($status['suggestedAction']) === 'SUCCESS'
			)
			{
				$jdideal->log('Create subscription', $logId);

				try
				{
					$this->createSubscription($jdideal, $logId);
				}
				catch (Exception $exception)
				{
					$jdideal->log($exception->getMessage(), $logId);
				}
			}
		}
		else
		{
			$jdideal->log('ID key is not found????', $logId);
		}

		return $status;
	}

	/**
	 * Create a periodic payment subscription.
	 *
	 * @param   Gateway  $jdideal  Gateway class
	 * @param   int      $logId    The log ID
	 *
	 * @return  void
	 *
	 * @since   5.0.0
	 */
	private function createSubscription(Gateway $jdideal, int $logId): void
	{
		$jdideal->log('Start creating subscription', $logId);

		try
		{
			// Load the profile
			$details = $jdideal->getDetails($logId);

			// Load the addon
			$addon    = $jdideal->getAddon($details->origin);
			$customer = $addon->getCustomerInformation($details->order_id);

			// Get the details
			$customerEmail = $customer['billing']->email;
			$description   = $jdideal->get(
				'recurringDescription',
				'Subscription'
			);
			$amount        = number_format($details->amount, 2, '.', '');
			$times         = $jdideal->get('times', 0);
			$interval      = $jdideal->get('interval', '1 months');
			$startDate     = date('Y-m-d', strtotime('+' . $interval));

			// Replace some placeholders on the description
			$find = [
				'{ORDERNR}',
				'{ORDERID}',
			];

			$replace = [
				$details->order_number,
				$details->order_id,
			];

			$description = $addon->replacePlaceholders($details, $description);
			$description = str_ireplace($find, $replace, $description);

			// Load the recurring handler
			$recurring = new RecurringMollie;

			// Set the API key
			$recurring->setApiKey($jdideal->get('profile_key'))
				->setProfileId($jdideal->getProfileId());

			$recurring->createSubscription(
				$amount,
				$details->currency,
				$startDate,
				$description,
				$customerEmail,
				$details->trans,
				$times,
				$interval
			);

			$jdideal->log('Subscription created', $logId);
		}
		catch (Exception $exception)
		{
			$jdideal->log($exception->getMessage(), $logId);
		}
	}

	/**
	 * Check who is knocking at the door.
	 *
	 * @return  boolean  True if it is the customer | False if it is the PSP.
	 *
	 * @since   4.0.0
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
		return false;
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
