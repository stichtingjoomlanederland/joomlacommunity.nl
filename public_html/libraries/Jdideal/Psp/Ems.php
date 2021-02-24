<?php
/**
 * @package     RO Payments
 * @subpackage  Omnikassa
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

use Jdideal\Gateway;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Input\Input;
use RuntimeException;
use stdClass;

defined('_JEXEC') or die;

/**
 * EMS processor.
 *
 * @package     JDiDEAL
 * @subpackage  EMS
 * @since       4.2.0
 */
class Ems
{
	/**
	 * Input
	 *
	 * @var    Input
	 * @since  4.0.0
	 */
	private $input;

	/**
	 * Array with return data from EMS
	 *
	 * @var    array
	 * @since  4.0
	 */
	private $data = [];

	/**
	 * The live URL
	 *
	 * @var    string
	 * @since  4.0
	 */
	private $liveUrl;

	/**
	 * The test URL
	 *
	 * @var    string
	 * @since  4.0
	 */
	private $testUrl;

	/**
	 * Set if the customer or PSP is calling
	 *
	 * @var    boolean
	 * @since  4.0
	 */
	private $isCustomer;

	/**
	 * List of return fields
	 *
	 * @var    array
	 * @since  4.2.0
	 */
	private $returnFields = [
		'approval_code',
		'oid',
		'refnumber',
		'status',
		'txndate_processed',
		'tdate',
		'fail_reason',
		'response_hash',
		'processor_response_code',
		'fail_rc',
		'terminal_id',
		'ccbin',
		'cccountry',
		'ccbrand',
		'response_code_3dsecure',
		'redirectURL',
		'fail_reason_details',
		'invalid_cardholder_data',
	];

	/**
	 * Contruct the payment reference.
	 *
	 * @param   input  $input  The input object.
	 *
	 * @since   4.0.0
	 */
	public function __construct(Input $input)
	{
		// Set the input
		$this->input = $input;

		// Put the return data in an array, data is constructed as name=value
		$this->data['transactionId'] = $input->get('oid');
		$this->data['logId']         = $input->getInt('logId');

		// Set the URLs
		$this->liveUrl = 'https://www.ipg-online.com/connect/gateway/processing';
		$this->testUrl = 'https://test.ipg-online.com/connect/gateway/processing';

		// Set who is calling
		$this->isCustomer = $input->get('customer', 1);
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
			'M'          => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'),
			'V'          => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'),
			'C'          => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_DINERSCLUB'),
			'ideal'      => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'klarna'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_KLARNA'),
			'MA'         => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTRO'),
			'maestroUK'  => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MAESTROUK'),
			'masterpass' => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERPASS'),
			'paypal'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'),
			'sofort'     => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT'),
			'BCMC'       => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'),
		];
	}

	/**
	 * Return the live URL.
	 *
	 * @return  string  The live URL.
	 *
	 * @since   4.0.0
	 */
	public function getLiveUrl(): string
	{
		return $this->liveUrl;
	}

	/**
	 * Return the test URL.
	 *
	 * @return  string  The test URL.
	 *
	 * @since   4.0.0
	 */
	public function getTestUrl(): string
	{
		return $this->testUrl;
	}

	/**
	 * Prepare data for the form.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   object   $data     An object with transaction information.
	 *
	 * @return  stdClass  The data for the form.
	 *
	 * @since   2.13
	 *
	 * @throws  RuntimeException
	 */
	public function getForm(Gateway $jdideal, $data): stdClass
	{
		// Get the store name
		$data->storeName = $jdideal->get('storeName');

		// Load the EMS class to get the values
		require_once JPATH_LIBRARIES . '/Jdideal/Psp/Ems/Ems.php';

		// Get the details so we can get the date to use for signing requests, e.g. EMS
		$details = $jdideal->getDetails($data->logid);

		// Instantiate EMS
		/** @var \Ems $ems */
		$ems = new \Ems($data->storeName, $jdideal->get('sharedSecret'));

		// Get the amount
		$data->amount = number_format($data->amount, 2, '.', '');

		// Get the timezone
		$data->timezone = $jdideal->get('timezone', 'Europe/Amsterdam');

		// Get the date and time of transaction
		$data->transactionDateTime = HTMLHelper::_('date', $details->date_added, 'Y:m:d-H:i:s', $data->timezone);
		$jdideal->log('Set transaction date and time' . $data->transactionDateTime, $data->logid);

		// Store the currency
		$currency = $data->currency ?: $jdideal->get('currency', 'EUR');
		$jdideal->setCurrency($currency, $data->logid);
		$jdideal->log('Currency: ' . $currency, $data->logid);

		// Get the currency
		$data->currency = $this->getCurrencyCode($currency);

		// Create the payment hash storename + txndatetime + chargetotal + currency + sharedsecret
		$data->hash = $ems->createHash($data->transactionDateTime, $data->amount, $data->currency);

		// Set the notify URL
		$root            = $jdideal->getUrl();
		$data->notifyurl = $root . 'cli/notify.php';

		// Set the order ID to send
		$invoiceNumber       = $jdideal->get('invoiceNumber', 'order_number');
		$orderNumber         = $jdideal->get('orderNumber', 'order_id');
		$data->invoiceNumber = $data->$invoiceNumber;
		$data->orderNumber   = $data->$orderNumber;

		// Get the payment method, plugin overrides component
		$data->paymentMethod = $jdideal->get('payment');

		$jdideal->log(Text::sprintf('COM_JDIDEAL_SELECTED_CARD', $data->paymentMethod), $data->logid);

		return $data;
	}

	/**
	 * Get the currency code.
	 *
	 * @param   string  $currency  The currency to convert
	 *
	 * @return  string The currency code.
	 *
	 * @since   4.11.0
	 */
	private function getCurrencyCode($currency): string
	{
		switch ($currency)
		{
			case 'AUD':
				return '036';
				break;
			case 'BRL':
				return '986';
				break;
			case 'INR':
				return '356';
				break;
			case 'GBP':
				return '826';
				break;
			case 'USD':
				return '840';
				break;
			case 'ZAR':
				return '710';
				break;
			case 'CHF':
				return '756';
				break;
			case 'AWG':
				return '533';
				break;
			case 'KYD':
				return '136';
				break;
			case 'DOP':
				return '214';
				break;
			case 'BSD':
				return '044';
				break;
			case 'BHD':
				return '048';
				break;
			case 'BBD':
				return '052';
				break;
			case 'BZD':
				return '084';
				break;
			case 'CAD':
				return '124';
				break;
			case 'CNY':
				return '156';
				break;
			case 'HRK':
				return '191';
				break;
			case 'CZK':
				return '203';
				break;
			case 'DKK':
				return '208';
				break;
			case 'XCD':
				return '951';
				break;
			case 'GYD':
				return '328';
				break;
			case 'HKD':
				return '344';
				break;
			case 'HUF':
				return '348';
				break;
			case 'ISL':
				return '376';
				break;
			case 'JMD':
				return '388';
				break;
			case 'JPY':
				return '392';
				break;
			case 'KWD':
				return '414';
				break;
			case 'LTL':
				return '440';
				break;
			case 'MXN':
				return '484';
				break;
			case 'NZD':
				return '554';
				break;
			case 'ANG':
				return '532';
				break;
			case 'NOK':
				return '578';
				break;
			case 'OMR':
				return '512';
				break;
			case 'PLN':
				return '985';
				break;
			case 'RON':
				return '946';
				break;
			case 'SAR':
				return '682';
				break;
			case 'SGD':
				return '702';
				break;
			case 'KRW':
				return '410';
				break;
			case 'SRD':
				return '968';
				break;
			case 'SEK':
				return '752';
				break;
			case 'TTD':
				return '780';
				break;
			case 'TRY':
				return '949';
				break;
			case 'AED':
				return '784';
				break;
			case 'EUR':
			default:
				return '978';
				break;
		}
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
		if (!array_key_exists('logId', $this->data))
		{
			throw new RuntimeException(Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
		}

		// Get the transaction ID
		return $this->data['logId'];
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
	 * @since   2.13
	 *
	 * @throws  RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, $logId): array
	{
		// Store the transaction reference
		if ($this->data['transactionId'])
		{
			$jdideal->setTrans($this->data['transactionId'], $logId);
		}

		// Log the received data
		foreach ($_POST as $name => $value)
		{
			$jdideal->log($name . ':' . $value, $logId);
		}

		$status         = [];
		$status['isOK'] = true;

		// Get the transaction details
		$details = $jdideal->getDetails($logId);

		// Add the card and processed status to the log
		$brand = $this->input->getString('paymentMethod', false);

		if ($brand)
		{
			$status['card'] = $brand;
		}

		// Get the customer info, not available
		$status['consumer'] = [];

		// Load the EMS class to get the values
		require_once JPATH_LIBRARIES . '/Jdideal/Psp/Ems/Ems.php';

		// Instantiate EMS
		$ems = new \Ems($jdideal->get('storeName'), $jdideal->get('sharedSecret'));

		// Create the seal
		$approvalCode = $this->input->getString('approval_code');
		$amount       = $this->input->getString('chargetotal');
		$currency     = $this->getCurrencyCode($details->currency ?: $jdideal->get('currency', 978));
		$dateAdded    = HTMLHelper::_(
			'date',
			$details->date_added,
			'Y:m:d-H:i:s',
			$jdideal->get('timezone', 'Europe/Amsterdam')
		);

		// Check which hashes to check
		$receivedResponseHash     = $this->input->get('response_hash');
		$receivedNotificationHash = $this->input->get('notification_hash');

		// If there is a response hash, check it
		if ($receivedResponseHash)
		{
			$responseHash = $ems->getResponseHash($approvalCode, $amount, $currency, $dateAdded);

			if ($responseHash !== $receivedResponseHash)
			{
				$jdideal->log(Text::_('COM_JDIDEAGATEWAY_RESPONSE_VERIFY_FAILED'), $logId);
				$jdideal->log('Received hash: ' . $this->input->get('notification_hash'), $logId);
				$jdideal->log('Calculated hash: ' . $responseHash, $logId);
				$jdideal->log('Currency: ' . $currency, $logId);
				$jdideal->log('dateAdded: ' . $dateAdded, $logId);
				$status['isOK']            = false;
				$status['error_message']   = Text::_('COM_JDIDEAGATEWAY_SHA_VERIFY_FAILED');
				$status['suggestedAction'] = 'CANCELLED';

				return $status;
			}
		}

		// If there is a notification hash, check it
		if ($receivedNotificationHash)
		{
			$notificationHash = $ems->getNotificationHash($approvalCode, $amount, $currency, $dateAdded);

			// Check the seal
			if ($notificationHash !== $receivedNotificationHash)
			{
				$jdideal->log(Text::_('COM_JDIDEAGATEWAY_SHA_VERIFY_FAILED'), $logId);
				$jdideal->log('Received hash: ' . $this->input->get('notification_hash'), $logId);
				$jdideal->log('Calculated hash: ' . $notificationHash, $logId);
				$jdideal->log('Currency: ' . $currency, $logId);
				$jdideal->log('dateAdded: ' . $dateAdded, $logId);
				$status['isOK']            = false;
				$status['error_message']   = Text::_('COM_JDIDEAGATEWAY_SHA_VERIFY_FAILED');
				$status['suggestedAction'] = 'CANCELLED';

				return $status;
			}
		}

		$jdideal->setTransactionDetails($brand, 1, $logId);

		// Check the payment status
		$approvalCode = $this->input->get('approval_code');

		switch ($approvalCode[0])
		{
			case 'Y':
				$status['suggestedAction'] = 'SUCCESS';
				break;
			case '?':
				$status['suggestedAction'] = 'OPEN';
				break;
			default:
				$status['suggestedAction'] = 'FAILURE';
				$status['error_message']   = $this->input->getString('fail_reason');
				break;
		}

		return $status;
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
