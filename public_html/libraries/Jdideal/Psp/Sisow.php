<?php
/**
 * @package     JDiDEAL
 * @subpackage  Sisow
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

defined('_JEXEC') or die;

use Exception;
use InvalidArgumentException;
use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Input\Input;
use RuntimeException;
use Sisow\Exceptions\SisowException;
use Sisow\SisowClient;

/**
 * Sisow processor.
 *
 * @package     JDiDEAL
 * @subpackage  Sisow
 * @since       2.12
 */
class Sisow
{
	/**
	 * Database driver
	 *
	 * @var    \JDatabaseDriver
	 * @since  4.0
	 */
	private $db;

	/**
	 * Input processor
	 *
	 * @var    Input
	 * @since  4.0
	 */
	private $input;

	/**
	 * Array with return data from Sisow
	 *
	 * @var    array
	 * @since  4.0
	 */
	private $data;

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
		$this->data['transaction_id'] = $input->get('trxid');
		$this->data['logId']          = $input->get('ec');

		// Set if this is the customer
		$notify                   = $input->get('notify', false) ? false : true;
		$callback                 = $input->get('callback', false) ? false : true;
		$this->data['isCustomer'] = $notify && $callback;

		// Load the Mollie classes
		require_once JPATH_LIBRARIES . '/Jdideal/Psp/Sisow/vendor/autoload.php';
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
			'ideal'       => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'overboeking' => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANKTRANSFER'),
			'sofort'      => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT'),
			'mistercash'  => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'),
			'paypalec'    => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'),
			'visa'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'),
			'mastercard'  => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'),
			'maestro'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTRO'),
			'vpay'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VPAY'),
			'webshop'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_WEBSHOP'),
			'podium'      => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PODIUM'),
			'bunq'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BUNQ'),
			'kbc'         => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_KBC'),
			'cbc'         => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CBC'),
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
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  SisowException
	 */
	public function getForm(Gateway $jdideal, $data): array
	{
		$sisow = new SisowClient;
		$sisow->setApiKey($jdideal->get('merchant_id'), $jdideal->get('merchant_key'), $jdideal->get('shop_id', 0));

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$selected[0] = strtolower($data->payment_method);
		}
		else
		{
			$selected = $jdideal->get('payment');
		}

		// Create the list of possible payment methods
		$options = [];
		$banks   = '';

		foreach ($selected as $key => $name)
		{
			switch ($name)
			{
				case 'idealqr':
					$options[] = HTMLHelper::_(
						'select.option',
						'idealqr',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEALQR')
					);
					break;
				case 'overboeking':
					$options[] = HTMLHelper::_(
						'select.option',
						'overboeking',
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
				case 'mistercash':
					$options[] = HTMLHelper::_(
						'select.option',
						'mistercash',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT')
					);
					break;
				case 'paypalec':
					$options[] = HTMLHelper::_(
						'select.option',
						'paypalec',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL')
					);
					break;
				case 'visa':
					$options[] = HTMLHelper::_(
						'select.option',
						'visa',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA')
					);
					break;
				case 'mastercard':
					$options[] = HTMLHelper::_(
						'select.option',
						'mastercard',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD')
					);
					break;
				case 'maestro':
					$options[] = HTMLHelper::_(
						'select.option',
						'maestro',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTRO')
					);
					break;
				case 'vpay':
					$options[] = HTMLHelper::_(
						'select.option',
						'vpay',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VPAY')
					);
					break;
				case 'webshop':
					$options[] = HTMLHelper::_(
						'select.option',
						'webshop',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_WEBSHOP')
					);
					break;
				case 'podium':
					$options[] = HTMLHelper::_(
						'select.option',
						'podium',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PODIUM')
					);
					break;
				case 'bunq':
					$options[] = HTMLHelper::_(
						'select.option',
						'bunq',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BUNQ')
					);
					break;
				case 'kbc':
					$options[] = HTMLHelper::_('select.option', 'kbc', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_KBC'));
					break;
				case 'cbc':
					$options[] = HTMLHelper::_('select.option', 'cbc', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_CBC'));
					break;
				default:
				case 'ideal':
					$options[] = HTMLHelper::_(
						'select.option',
						'ideal',
						Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL')
					);

					// Check if testmode is enabled
					$testmode = $jdideal->get('testmode') ? true : false;

					// Load the banks
					$banks = $sisow->issuers->get($testmode);
					break;
			}
		}

		$output             = [];
		$output['payments'] = $options;
		$output['banks']    = $banks;
		$output['redirect'] = $jdideal->get('redirect', 'wait');

		return $output;
	}

	/**
	 * Get the log ID.
	 *
	 * @return  integer  The ID of the log.
	 *
	 * @since   4.0
	 *
	 * @throws  RuntimeException
	 */
	public function getLogId(): int
	{
		$logId = false;

		if ($this->data['logId'])
		{
			return $this->data['logId'];
		}

		if ($this->data['transaction_id'])
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__jdidealgateway_logs'))
				->where($this->db->quoteName('trans') . ' = ' . $this->db->quote($this->data['transaction_id']));
			$this->db->setQuery($query);

			$logId = $this->db->loadResult();
		}

		if ( ! $logId)
		{
			throw new RuntimeException(Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
		}

		return $logId;
	}

	/**
	 * Send payment to Sisow.
	 *
	 * @param   Gateway  $jdideal  An instance of \Jdideal\Gateway.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  Exception
	 */
	public function sendPayment(Gateway $jdideal): void
	{
		$app      = Factory::getApplication();
		$logId    = $this->input->get('logid', 0, 'int');
		$customer = false;

		$sisow = new SisowClient;
		$sisow->setApiKey($jdideal->get('merchant_id'), $jdideal->get('merchant_key'), $jdideal->get('shop_id', 0));

		// Load the stored data
		$details = $jdideal->getDetails($logId);

		if ( ! is_object($details))
		{
			throw new RuntimeException(Text::sprintf('COM_ROPAYMENTS_NO_TRANSACTION_DETAILS', 'Sisow', $logId));
		}

		// Load the addon
		$addon = $jdideal->getAddon($details->origin);

		// Replace some predefined values
		$description = $jdideal->replacePlaceholders($logId, $jdideal->get('description'));

		// Get the purchase ID
		$orderNumber = $jdideal->get('orderNumber', 'order_number');

		$data = [
			'payment'      => $this->input->get('payment', 'ideal'),
			'shopid'       => $jdideal->get('shop_id', 0),
			'issuerid'     => $this->input->get('banks'),
			'purchaseid'   => substr($details->$orderNumber, 0, 16),
			'amount'       => round($details->amount * 100),
			'currency'     => $details->currency ?: 'EUR',
			'description'  => substr($description, 0, 32),
			'entrancecode' => $details->id,
			'returnurl'    => $jdideal->getUrl() . 'cli/notify.php',
			'cancelurl'    => $jdideal->getUrl() . 'cli/notify.php',
			'callbackurl'  => $jdideal->getUrl() . 'cli/notify.php',
			'notifyurl'    => $jdideal->getUrl() . 'cli/notify.php',
			'testmode'     => $jdideal->get('testmode') ? 'true' : 'false'
		];

		// Store the currency
		$jdideal->setCurrency($data['currency'], $logId);
		$jdideal->log('Currency: ' . $data['currency'], $logId);

		$jdideal->log('Callback URL: ' . $data['callbackurl'], $logId);

		if ($data['payment'] === 'overboeking')
		{
			// Set the status to transfer as it works different than other payment options
			$jdideal->status('TRANSFER', $logId);

			// Load the customer details
			$customer = $addon->getCustomerInformation($details->order_id);

			if ($customer)
			{
				// Billing details
				$data['billing_firstname']   = $customer['billing']->firstname ?: '';
				$data['billing_lastname']    = $customer['billing']->lastname ?: '';
				$data['billing_mail']        = $customer['billing']->email ?: '';
				$data['billing_company']     = $customer['billing']->company ?: '';
				$data['billing_address1']    = $customer['billing']->address1 ?: '';
				$data['billing_address2']    = $customer['billing']->address2 ?: '';
				$data['billing_zip']         = $customer['billing']->zip ?: '';
				$data['billing_city']        = $customer['billing']->city ?: '';
				$data['billing_country']     = $customer['billing']->country ?: '';
				$data['billing_countrycode'] = $customer['billing']->countrycode ?: '';
				$data['billing_phone']       = $customer['billing']->phone1 ?: '';

				// Shipping details
				$data['shipping_firstname']   = $customer['shipping']->firstname ?: '';
				$data['shipping_lastname']    = $customer['shipping']->lastname ?: '';
				$data['shipping_mail']        = $customer['shipping']->email ?: '';
				$data['shipping_company']     = $customer['shipping']->company ?: '';
				$data['shipping_address1']    = $customer['shipping']->address1 ?: '';
				$data['shipping_address2']    = $customer['shipping']->address2 ?: '';
				$data['shipping_zip']         = $customer['shipping']->zip ?: '';
				$data['shipping_city']        = $customer['shipping']->city ?: '';
				$data['shipping_country']     = $customer['shipping']->country ?: '';
				$data['shipping_countrycode'] = $customer['shipping']->countrycode ?: '';
				$data['shipping_phone']       = $customer['shipping']->phone ?: '';

				// Other details
				$data['days']      = $jdideal->get('days', 14);
				$data['including'] = $jdideal->get('including', 0) ? 'true' : 'false';
			}
		}

		try
		{
			$transaction = $sisow->transactions->create($data);
			$jdideal->log('Transaction: ' . json_encode($transaction), $logId);

			// Set the transaction ID
			$jdideal->setTrans($transaction->transactionId, $logId);

			// Store the payment type
			$jdideal->setTransactionDetails($data['payment'], 0, $logId);

			// Check where to send the customer
			switch ($data['payment'])
			{
				case 'overboeking':
					if ($customer === false)
					{
						// No customer details, can't continue
						$jdideal->log(
							'No customer details found, bank transfer is not possible. Redirect to cancel URL',
							$logId
						);
						$jdideal->log(
							'Return URL: ' . $data['cancelurl'] . '?ec=' . $logId . '&trxid=' . $transaction->transactionId,
							$logId
						);
						$app->redirect($data['cancelurl'] . '?ec=' . $logId . '&trxid=' . $transaction->transactionId);
					}
					else
					{
						// Send the customer to the success page
						$jdideal->log('Customer details found, redirect bank transfer to notify URL', $logId);
						$jdideal->log(
							'Return URL: ' . $data['notifyurl'] . '?ec=' . $logId . '&trxid=' . $transaction->transactionId,
							$logId
						);
						$app->redirect($data['notifyurl'] . '?ec=' . $logId . '&trxid=' . $transaction->transactionId);
					}
					break;
				default:
					// Send the customer to the bank
					$jdideal->log('Sending customer to URL: ' . $transaction->issuerUrl, $logId);
					$app->redirect($transaction->issuerUrl);
					break;
			}
		}
		catch (Exception $exception)
		{
			$message = $exception->getMessage();
			$jdideal->log($message, $logId);
			$jdideal->log('Payment: ' . $data['payment'], $logId);
			$jdideal->log('Issuer ID: ' . $data['issuerid'] ?? 'No issuer ID', $logId);

			$redirect = $details->cancel_url === '' ? $details->return_url : $details->cancel_url;
			$jdideal->log('Redirect to: ' . $redirect, $logId);
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirect);
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
	 * @param   Gateway  $jdideal  An instance of JdIdealGateway.
	 * @param   int      $logId    The ID of the transaction log.
	 *
	 * @return  array  Array of transaction details.
	 *
	 * @since   2.13
	 * @throws  RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, $logId): array
	{
		$details       = $jdideal->getDetails($logId);
		$transactionID = $details->trans;

		if (empty($transactionID))
		{
			$transactionID = $this->getTransactionId();

			if (empty($transactionID))
			{
				$status['isOK']            = true;
				$status['error_message']   = '';
				$status['suggestedAction'] = 'CANCELLED';
				$status['consumer']        = [];

				return $status;
			}

			$jdideal->setTrans($transactionID, $logId);
		}

		$status['transactionID'] = $transactionID;

		// Check the transaction status
		$sisow = new SisowClient;
		$sisow->setApiKey($jdideal->get('merchant_id'), $jdideal->get('merchant_key'), $jdideal->get('shop_id', 0));

		try
		{
			$transaction = $sisow->transactions->get($transactionID);

			$status['card'] = $details->card;
			$jdideal->log('Received status: ' . $transaction->status, $logId);

			if ($status['card'] === 'overboeking' && strtolower($transaction->status) === 'pending')
			{
				$status['isOK']            = true;
				$status['error_message']   = '';
				$status['suggestedAction'] = 'TRANSFER';
				$status['consumer']        = [];
			}
			else
			{
				$jdideal->log('Status: ' . $transaction->status, $logId);
				$jdideal->log('Timestamp: ' . $transaction->timestamp, $logId);
				$jdideal->log('Amount: ' . $transaction->amount, $logId);
				$jdideal->log('Consumer account: ' . $transaction->consumerAccount, $logId);
				$jdideal->log('Consumer name: ' . $transaction->consumerName, $logId);
				$jdideal->log('Consumer city: ' . $transaction->consumerCity, $logId);
				$jdideal->log('Purchase ID: ' . $transaction->purchaseId, $logId);
				$jdideal->log('Description: ' . $transaction->description, $logId);
				$jdideal->log('Entrance code: ' . $transaction->entranceCode, $logId);
				$jdideal->log('Document ID: ' . $transaction->documentId, $logId);
				$jdideal->log('Document URL: ' . $transaction->documentURL, $logId);

				$jdideal->setTransactionDetails($status['card'], 1, $logId);

				$status['isOK']          = true;
				$status['error_message'] = '';

				switch (strtoupper($transaction->status))
				{
					case 'REVERSED':
						$status['suggestedAction'] = 'REFUNDED';
						break;
					default:
						$status['suggestedAction'] = $transaction->status;
						break;
				}

				// Consumer data
				$status['consumer']                    = [];
				$status['consumer']['consumerAccount'] = $transaction->consumerAccount;
				$status['consumer']['consumerName']    = $transaction->consumerName;
				$status['consumer']['consumerCity']    = $transaction->consumerCity;
			}
		}
		catch (Exception $exception)
		{
			$jdideal->log('Error code: ' . $exception->getCode(), $logId);
			$jdideal->log('Error message: ' . $exception->getMessage(), $logId);

			$status['isOK']            = false;
			$status['error_message']   = $exception->getMessage();
			$status['suggestedAction'] = 'CANCELLED';
			$status['consumer']        = [];
		}

		return $status;
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return  string  The ID of the transaction.
	 *
	 * @since   4.0
	 *
	 * @throws  RuntimeException
	 */
	public function getTransactionId(): string
	{
		if ( ! array_key_exists('transaction_id', $this->data))
		{
			throw new RuntimeException(Text::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND'));
		}

		// Get the transaction ID
		return (string) $this->data['transaction_id'];
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
		return (bool) $this->data['isCustomer'];
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
	 * Create the options to show on a checkout page.
	 *
	 * @param   Gateway  $jdideal        An instance of JdidealGateway.
	 * @param   string   $paymentMethod  The name of the chosen payment method.
	 *
	 * @return  array  List of select options.
	 *
	 * @since   4.1.0
	 *
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  SisowException
	 */
	public function getCheckoutOptions(Gateway $jdideal, $paymentMethod): array
	{
		$sisow = new SisowClient;
		$sisow->setApiKey($jdideal->get('merchant_id'), $jdideal->get('merchant_key'), $jdideal->get('shop_id', 0));

		$testMode = $jdideal->get('testmode') ? true : false;
		$banks    = [];

		if (strtolower($paymentMethod) === 'ideal')
		{
			$banks = $sisow->issuers->get($testMode);

			array_walk(
				$banks,
				static function ($item)
				{
					$item->text = $item->name;
					$item->value = $item->id;
				}
			);
		}

		return $banks;
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
