<?php
/**
 * @package     JDiDEAL
 * @subpackage  Buckaroo
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

use Jdideal\Gateway;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Buckaroo processor.
 *
 * @package     JDiDEAL
 * @subpackage  Buckaroo
 * @since       2.12
 */
class Buckaroo
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
	 * @var    \JInput
	 * @since  4.0
	 */
	private $jinput;

	/**
	 * Array with return data from Buckaroo
	 *
	 * @var    array
	 * @since  4.0
	 */
	private $data = array();

	/**
	 * Live URL
	 *
	 * @var    string
	 * @since  2.13
	 */
	private $liveUrl = 'https://checkout.buckaroo.nl/nvp/';

	/**
	 * Test URL
	 *
	 * @var    string
	 * @since  2.13
	 */
	private $testUrl = 'https://testcheckout.buckaroo.nl/nvp/';

	/**
	 * The response from the send query.
	 *
	 * @var    \JHttpResponse
	 * @since  2.13
	 */
	private $response = '';

	/**
	 * Construct the payment reference.
	 *
	 * @param   \Jinput  $jinput  The input object.
	 *
	 * @since   4.0
	 */
	public function __construct(\JInput $jinput)
	{
		// Set the input
		$this->jinput = $jinput;

		// Set the database
		$this->db = \JFactory::getDbo();
	}

	/**
	 * Returns a list of available payment methods.
	 *
	 * @return  array  List of available payment methods.
	 *
	 * @since   3.0
	 */
	public function getAvailablePaymentMethods()
	{
		return array(
			'ideal'              => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'bancontactmrcash'   => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'),
			'mastercard'         => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'),
			'visa'               => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'),
			'amex'               => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_AMEX'),
			'sofortueberweisung' => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT'),
			'paypal'             => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'),
			'paysafecard'        => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYSAFECARD'),
			'giropay'            => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_GIROPAY'),
			'sepadirectdebit'    => Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SEPADIRECTDEBIT'),
		);
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
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public function getForm(Gateway $jdideal, $data): array
	{
		// Load the form options
		$options = [];
		$banks = '';

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$selected = [];
			$selected[] = strtolower($data->payment_method);
		}
		else
		{
			$selected = $jdideal->get('payment', array('ideal'));
		}

		// Process the selected payment methods
		foreach ($selected as $name)
		{
			switch ($name)
			{
				case 'sofortueberweisung':
					$options[] = HTMLHelper::_('select.option', 'sofortueberweisung', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SOFORT'));
					break;
				case 'bancontactmrcash':
					$options[] = HTMLHelper::_('select.option', 'bancontactmrcash', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_BANCONTACT'));
					break;
				case 'mastercard':
					$options[] = HTMLHelper::_('select.option', 'mastercard', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'));
					break;
				case 'visa':
					$options[] = HTMLHelper::_('select.option', 'visa', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'));
					break;
				case 'amex':
					$options[] = HTMLHelper::_('select.option', 'amex', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_AMEX'));
					break;
				case 'paypal':
					$options[] = HTMLHelper::_('select.option', 'paypal', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_PAYPAL'));
					break;
				case 'giropay':
					$options[] = HTMLHelper::_('select.option', 'giropay', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_GIROPAY'));
					break;
				case 'sepadirectdebit':
					$options[] = HTMLHelper::_('select.option', 'sepadirectdebit', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_SEPADIRECTDEBIT'));
					break;
				default:
				case 'ideal':
					$options[] = HTMLHelper::_('select.option', 'ideal', Text::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'));
					$banks = $this->getBanks();
					break;
			}
		}

		$output = [];
		$output['payments'] = $options;
		$output['banks'] = $banks;

		return $output;
	}

	/**
	 * Get the log ID.
	 *
	 * @return  int  The ID of the log.
	 *
	 * @since   4.0
	 *
	 * @throws  \RuntimeException
	 */
	public function getLogId()
	{
		$logId = $this->jinput->get('add_logid', $this->jinput->get('logid', false, 'int'));

		if (!$logId)
		{
			throw new \RuntimeException(Text::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
		}

		return $logId;
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return  int  The ID of the transaction.
	 *
	 * @since   4.0
	 *
	 * @throws  \RuntimeException
	 */
	public function getTransactionId()
	{
		if (!array_key_exists('transactionId', $this->data))
		{
			$logId = $this->getLogId();

			if ($logId)
			{
				$query = $this->db->getQuery(true)
					->select($this->db->quoteName('trans'))
					->from($this->db->quoteName('#__jdidealgateway_logs'))
					->where($this->db->quoteName('id') . ' = ' . (int) $logId);
				$this->db->setQuery($query);

				$this->data['transactionId'] = $this->db->loadResult();
			}
		}

		$transactionId = $this->data['transactionId'];

		if (!$transactionId)
		{
			throw new \RuntimeException(Text::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND'));
		}

		return $transactionId;
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
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public function transactionStatus(Gateway $jdideal, $logId)
	{
		// Load the data received from Buckaroo
		$returnData = $this->jinput->post->getArray();

		// Check if we are doing a status request only
		$statusRequest = false;

		if (!$returnData)
		{
			$returnData = $this->statusRequest($jdideal, $this->getTransactionId());
			$statusRequest = true;
		}

		// Log the returned variables
		foreach ($returnData as $key => $value)
		{
			$jdideal->log($key . ': ' . $value, $logId);
		}

		// Load the transaction ID
		$transactionID = $jdideal->getTrans($logId);
		$status = array();
		$status['transactionID'] = $transactionID;

		// Calculate the signature
		$signature = $this->calculateSignature($jdideal, $returnData);

		$jdideal->log('Hashed signature: ' . $signature, $logId);

		// Check if the signature is correct
		if ($statusRequest || $returnData['brq_signature'] === $signature)
		{
			// Log the status message
			$jdideal->log(urldecode($returnData['brq_statusmessage']), $logId);

			// Process the brand
			$brand = $returnData['brq_payment_method'];

			if (!array_key_exists('brq_payment_method', $returnData))
			{
				$brand = '';
			}

			$status['isOK'] = true;
			$status['card'] = $brand;

			// Verify status code
			switch ($returnData['brq_statuscode'])
			{
				case '190':
					$status['suggestedAction'] = 'SUCCESS';
					break;
				case '490':
					$status['suggestedAction'] = 'FAILURE';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_490');
					break;
				case '491':
					$status['suggestedAction'] = 'FAILURE';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_491');
					break;
				case '492':
					$status['suggestedAction'] = 'ERROR';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_492');
					break;
				case '791':
					$status['suggestedAction'] = 'OPEN';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_791');
					break;
				case '690':
					$status['suggestedAction'] = 'FAILURE';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_690');
					break;
				case '890':
					$status['suggestedAction'] = 'CANCELLED';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_890');
					break;
				default:
					$status['suggestedAction'] = 'FAILURE';
					$status['error_message'] = Text::_('COM_ROPAYMENTS_BUCKAROO_RESULT_' . $returnData['brq_statuscode']);
					break;
			}

			switch ($brand)
			{
				case 'ideal':
					// Get the customer details
					$status['consumer']['consumerAccount'] = '';
					$status['consumer']['consumerIban'] = '';
					$status['consumer']['consumerBic'] = '';
					$status['consumer']['consumerName'] = '';
					$status['consumer']['consumerCity'] = '';

					if (array_key_exists('brq_service_ideal_consumeriban', $returnData))
					{
						$status['consumer']['consumerIban'] = urldecode($returnData['brq_service_ideal_consumeriban']);
					}

					if (array_key_exists('brq_service_ideal_consumerbic', $returnData))
					{
						$status['consumer']['consumerBic'] = urldecode($returnData['brq_service_ideal_consumerbic']);
					}

					if (array_key_exists('brq_service_ideal_consumername', $returnData))
					{
						$status['consumer']['consumerName'] = urldecode($returnData['brq_service_ideal_consumername']);
					}

					break;
				default:
					$status['consumer'] = array();
					break;
			}

			$jdideal->setTransactionDetails($brand, 1, $logId);
		}
		else
		{
			$jdideal->log(Text::_('COM_JDIDEAGATEWAY_SHA_VERIFY_FAILED'), $logId);
			$status['isOK'] = false;
			$status['error_message'] = Text::_('COM_JDIDEAGATEWAY_SHA_VERIFY_FAILED');
			$status['suggestedAction'] = 'CANCELLED';
		}

		return $status;
	}

	/**
	 * Send a request to Buckaroo.
	 *
	 * @param   Gateway  $jdideal    An instance of JdidealGateway.
	 * @param   array    $keyvalue   Array with values.
	 * @param   string   $operation  Name of a non-default operation.
	 *
	 * @return  mixed  List of feedback variables.
	 *
	 * @since   2.13
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	private function send(Gateway $jdideal, array $keyvalue = null, $operation = null)
	{
		$options = new Registry;
		$http    = HttpFactory::getHttp($options, ['curl', 'stream']);

		// Construct the URL
		$url = $jdideal->get('testmode', 0) === '1' ? $this->testUrl : $this->liveUrl;

		if ($operation)
		{
			$url .= '?op=' . $operation;
		}

		if (is_array($keyvalue))
		{
			foreach ($keyvalue as $index => $item)
			{
				$jdideal->log($index . ': ' . $item, $this->getLogId());
			}
		}

		$jdideal->log('Sending request to: ' . $url, $this->getLogId());

		$strResponse    = $http->post($url, $keyvalue);
		$this->response = $strResponse->body;

		$jdideal->log('Received response: ' . $this->response, $this->getLogId());

		if (!$this->response)
		{
			return false;
		}

		// Read the feedback
		$answer = $this->parseString($this->response);

		$jdideal->log('Received answer: ' . $this->response, $this->getLogId());

		// Validate the feedback
		$return = array('BRQ_STATUSMESSAGE' => Text::_('COM_ROPAYMENTS_BUCKAROO_HASH_FAILED'));

		if ($operation === 'transactionstatus')
		{
			$return = array_change_key_case($answer);
		}
		else
		{
			$hash = $this->calculateSignature($jdideal, $answer);

			if ($hash === $answer['BRQ_SIGNATURE'])
			{
				$return = $answer;
			}
		}

		return $return;
	}

	/**
	 * Request the list of banks.
	 *
	 * @return  array  List of available banks.
	 *
	 * @since   2.13
	 *
	 * @throws  \InvalidArgumentException
	 */
	private function getBanks()
	{
		$options = array();
		$options['_']['id'] = 'NL';
		$options['_']['text'] = 'Nederland';
		$options['_']['items'] = array();
		$options['_']['items'][] = HTMLHelper::_('select.option', 'ABNANL2A', 'ABN Amro Bank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'INGBNL2A', 'ING');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'KNABNL2H', 'Knab');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'RABONL2U', 'Rabobank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'SNSBNL2A', 'SNS Bank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'ASNBNL21', 'ASN Bank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'RBRBNL21', 'Regio Bank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'TRIONL2U', 'Triodos Bank');
		$options['_']['items'][] = HTMLHelper::_('select.option', 'FVLBNL22', 'Van Lanschot Bankiers');

		return $options;
	}

	/**
	 * Make a transaction request.
	 *
	 * @param   Gateway  $jdideal  An instance of Gateway.
	 *
	 * @return  array  An array with result data.
	 *
	 * @since   2.13
	 *
	 * @throws  \Exception
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public function sendPayment(Gateway $jdideal)
	{
		$app = \JFactory::getApplication();
		$logId = $this->getLogId();

		// Array of fields to send out
		$fields = array();

		// Payment fields
		$payment = $this->jinput->get('payment', 'ideal');

		// Load the stored data
		$details = $jdideal->getDetails($logId);

		$jdideal->setTransactionDetails($payment, 0, $logId);

		switch ($payment)
		{
			case 'bancontactmrcash':
			case 'visa':
			case 'mastercard':
			case 'amex':
			case 'paypal':
				$fields['brq_payment_method'] = $payment;
				$fields['brq_service_' . $payment . '_action'] = 'pay';
				break;
			case 'sepadirectdebit':
				$fields['brq_payment_method'] = $payment;
				$fields['brq_service_' . $payment . '_action'] = 'pay';

				// Load the addon
				$addon = $jdideal->getAddon($details->origin);

				// Load the customer details
				$customer = $addon->getCustomerInformation($details->order_id);

				$fields['brq_customeraccountname'] = $customer->firstname . ' ' . $customer->lastname;

				/** @todo We need someone to tell us if they use it which field they use */
				$fields['brq_customeriban'] = '';
				break;
			case 'ideal':
			default:
				$fields['brq_payment_method'] = 'ideal';
				$fields['brq_service_ideal_action'] = 'pay';
				$fields['brq_service_ideal_issuer'] = $this->jinput->get('banks', '');
				$fields['brq_service_ideal_version'] = '2';
				break;
		}

		// Basic fields
		$invoiceNumber               = $jdideal->get('invoiceNumber', 'order_number');
		$orderNumber                 = $jdideal->get('orderNumber', 'order_number');
		$fields['brq_websitekey']    = $jdideal->get('merchant_key');
		$fields['brq_culture']       = 'nl-NL';
		$fields['brq_currency']      = $details->currency ?: 'EUR';
		$fields['brq_amount']        = $details->amount;
		$fields['brq_invoicenumber'] = $details->$invoiceNumber;
		$fields['brq_ordernumber']   = $details->$orderNumber;

		// Store the currency
		$jdideal->setCurrency($fields['brq_currency'], $logId);
		$jdideal->log('Currency: ' . $fields['brq_currency'], $logId);

		// Setup the description
		$description = $jdideal->replacePlaceholders($logId, $jdideal->get('description'));
		$fields['brq_description'] = substr($description, 0, 32);

		$fields['brq_clientip'] = $_SERVER['REMOTE_ADDR'];
		$fields['brq_channel'] = 'Web';

		// Set the return URLs
		$root = $jdideal->getUrl();
		$fields['brq_return'] = $root . 'cli/notify.php';
		$fields['brq_returncancel'] = $root . 'cli/notify.php';
		$fields['brq_returnerror'] = $root . 'cli/notify.php';
		$fields['brq_returnreject'] = $root . 'cli/notify.php';

		// List of available methods to show to the customer
		$fields['brq_requestedservices'] = 'ideal';

		// Add the log ID
		$fields['add_logid'] = $details->id;

		// Calculate the signature
		$jdideal->log('Using hash ' . $jdideal->get('hash'), $logId);
		$fields['brq_signature'] = $this->calculateSignature($jdideal, $fields);

		// Call Buckaroo
		$request = $this->send($jdideal, $fields);

		if (!$request)
		{
			throw new \RuntimeException(Text::_('COM_ROPAYMENTS_BUCKAROO_CALL_FAILED'));
		}

		if (array_key_exists('BRQ_ACTIONREQUIRED', $request))
		{
			// Set the transaction ID
			$jdideal->setTrans($request['BRQ_TRANSACTIONS'], $logId);

			switch (strtolower($request['BRQ_ACTIONREQUIRED']))
			{
				case 'redirect':
					$jdideal->log('Send customer to URL: ' . $request['BRQ_REDIRECTURL'], $logId);
					$app->redirect($request['BRQ_REDIRECTURL']);
					break;
			}
		}
		// We don't need to redirect for SEPA Direct Debit
		elseif ($payment === 'sepadirectdebit')
		{
			\JFactory::getApplication()->enqueueMessage(
				urldecode($request['BRQ_CONSUMERMESSAGE_TITLE']) . '<br />' . urldecode($request['BRQ_CONSUMERMESSAGE_HTMLTEXT'])
			);

			// Update the transaction status
			$jdideal->setTrans($request['BRQ_TRANSACTIONS'], $logId);
			$jdideal->status('TRANSFER', $logId);

			$jdideal->log('Send customer to URL: ' . $details->return_url, $logId);
			$app->redirect($details->return_url);
		}
		else
		{
			if (array_key_exists('BRQ_APIERRORMESSAGE', $request))
			{
				$app->enqueueMessage($request['BRQ_APIERRORMESSAGE'], 'error');
			}

			if ($payment !== 'sepadirectdebit' && array_key_exists('BRQ_STATUSMESSAGE', $request))
			{
				$app->enqueueMessage($request['BRQ_STATUSMESSAGE'], 'error');
			}

			$redirect = $details->cancel_url ?: $details->return_url;
			$jdideal->log('Send customer to URL: ' . $redirect, $logId);
			$app->redirect($redirect);
		}
	}

	/**
	 * Process feedback string into a readable array.
	 *
	 * @param   string  $string  The feedback string to parse.
	 *
	 * @return  array  The array with the feedback data.
	 *
	 * @since   2.13
	 */
	private function parseString($string)
	{
		$responseValues = explode('&', $string);
		$answer = array();

		foreach ($responseValues as $responseValue)
		{
			list ($name, $value) = explode('=', $responseValue);
			$answer[$name] = urldecode($value);
		}

		return $answer;
	}

	/**
	 * Calculate the signature.
	 *
	 * @param   Gateway  $jdideal  An instance of Gateway.
	 * @param   array    $fields   The fields to hash.
	 *
	 * @return  string  The calculated signature.
	 *
	 * @since   2.13
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	private function calculateSignature(Gateway $jdideal, $fields)
	{
		// Let's check if we have an invalid response
		if (array_key_exists('BRQ_APIRESULT', $fields) && $fields['BRQ_APIRESULT'] === 'Fail')
		{
			throw new \InvalidArgumentException($fields['BRQ_APIERRORMESSAGE']);
		}

		// Sort the fields
		ksort($fields);

		// Make sure the signature field is not in there
		if (array_key_exists('brq_signature', $fields))
		{
			unset($fields['brq_signature']);
		}
		elseif (array_key_exists('BRQ_SIGNATURE', $fields))
		{
			unset($fields['BRQ_SIGNATURE']);
		}

		// Get the array for fields to hash
		$dataFields = array();
		$logId = $this->getLogId();

		// Assign the fields to hash
		foreach ($fields as $name => $value)
		{
			$jdideal->log($name . ': ' . $value, $logId);
			$dataFields[] = $name . '=' . urldecode($value);
		}

		// Create the string
		$dataString = implode('', $dataFields);
		$dataString .= $jdideal->get('secret_key');

		$jdideal->log('Hashing datastring: ' . $dataString, $logId);

		// Return the hashed data
		return hash($jdideal->get('hash'), $dataString);
	}

	/**
	 * Status request.
	 *
	 * @param   Gateway  $jdideal        An instance of Gateway.
	 * @param   string   $transactionId  The transaction key.
	 *
	 * @return  int  The ID of the status.
	 *
	 * @since   2.13
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public function statusRequest(Gateway $jdideal, $transactionId)
	{
		$fields = array();
		$fields['brq_websitekey'] = $jdideal->get('merchant_key');
		$fields['brq_transaction'] = $transactionId;
		$fields['brq_signature'] = $this->calculateSignature($jdideal, $fields);

		return $this->send($jdideal, $fields, 'transactionstatus');
	}

	/**
	 * Check who is knocking at the door.
	 *
	 * @return  bool  True if it is the customer | False if it is the PSP.
	 *
	 * @since   4.0
	 */
	public function isCustomer()
	{
		return true;
	}

	/**
	 * Tell RO Payments if status can be checked based on customer.
	 *
	 * @return  boolean  True if user status can be used | False otherwise.
	 *
	 * @since   4.13.0
	 */
	public function canUseCustomerStatus()
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
	public function callHome()
	{
		return false;
	}
}
