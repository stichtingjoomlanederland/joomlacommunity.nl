<?php
/**
 * @package     RO Payments
 * @subpackage  Internetkassa
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

use Jdideal\Gateway;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Internetkassa processor.
 *
 * @package     JDiDEAL
 * @subpackage  Internetkassa
 * @since       2.12
 */
class Internetkassa
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
	 * Array with return data from Ingenico.
	 *
	 * @var    array
	 * @since  4.0
	 */
	private $data = array();

	/**
	 * Array with list of supported cards.
	 *
	 * @var    array
	 * @since  4.0
	 */
	private $brands = array();

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
	 * Contruct the payment reference.
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

		// Put the return data in an array, data is constructed as name=value
		$this->data['transaction_id'] = $jinput->post->get('PAYID');

		// Set if this is the customer
		$this->data['isCustomer'] = $this->data['transaction_id'] === null;

		// Cards general
		$this->brands['ogone']['3XCB']             = '3XCB';
		$this->brands['ogone']['AIRPLUS']          = 'CreditCard';
		$this->brands['ogone']['American Express'] = 'CreditCard';
		$this->brands['ogone']['Aurora']           = 'CreditCard';
		$this->brands['ogone']['Aurore']           = 'CreditCard';
		$this->brands['ogone']['Billy']            = 'CreditCard';
		$this->brands['ogone']['CB']               = 'CreditCard';
		$this->brands['ogone']['Cofinoga']         = 'CreditCard';
		$this->brands['ogone']['Dankort']          = 'CreditCard';
		$this->brands['ogone']['Diners Club']      = 'CreditCard';
		$this->brands['ogone']['JCB']              = 'CreditCard';
		$this->brands['ogone']['Laser']            = 'CreditCard';
		$this->brands['ogone']['MaestroUK']        = 'CreditCard';
		$this->brands['ogone']['MasterCard']       = 'CreditCard';
		$this->brands['ogone']['Solo']             = 'CreditCard';
		$this->brands['ogone']['UATP']             = 'CreditCard';
		$this->brands['ogone']['VISA']             = 'CreditCard';

		// Cards exceptions
		$this->brands['ogone']['BCMC']             = 'CreditCard';
		$this->brands['ogone']['Maestro']          = 'CreditCard';
		$this->brands['ogone']['PostFinance Card'] = 'PostFinance Card';

		// Cards Online Credit
		$this->brands['ogone']['NetReserve'] = 'CreditCard';
		$this->brands['ogone']['PRIVILEGE']  = 'CreditCard';
		$this->brands['ogone']['UNEUROCOM']  = 'UNEUROCOM';

		// WebBanking
		$this->brands['ogone']['Amazon Checkout']       = 'Amazon Checkout';
		$this->brands['ogone']['Belfius Direct Net']    = 'Belfius Direct Net';
		$this->brands['ogone']['cashticket']            = 'cashticket';
		$this->brands['ogone']['CBC Online']            = 'CBC Online';
		$this->brands['ogone']['CENTEA Online']         = 'CENTEA Online';
		$this->brands['ogone']['Sofort Uberweisung']    = 'DirectEbanking';
		$this->brands['ogone']['DirectEbankingAT']      = 'DirectEbankingAT ';
		$this->brands['ogone']['DirectEbankingBE']      = 'DirectEbankingBE';
		$this->brands['ogone']['DirectEbankingCH']      = 'DirectEbankingCH';
		$this->brands['ogone']['DirectEbankingDE']      = 'DirectEbankingDE';
		$this->brands['ogone']['DirectEbankingFR']      = 'DirectEbankingFR';
		$this->brands['ogone']['DirectEbankingGB']      = 'DirectEbankingGB';
		$this->brands['ogone']['DirectEbankingIT']      = 'DirectEbankingIT';
		$this->brands['ogone']['DirectEbankingNL']      = 'DirectEbankingNL';
		$this->brands['ogone']['EBS_AXIS']              = 'EBS_AXIS';
		$this->brands['ogone']['EBS_BC']                = 'EBS_BC';
		$this->brands['ogone']['EBS_CB']                = 'EBS_CB';
		$this->brands['ogone']['EBS_CORP']              = 'EBS_CORP';
		$this->brands['ogone']['EBS_DC']                = 'EBS_DC';
		$this->brands['ogone']['EBS_FED']               = 'EBS_FED';
		$this->brands['ogone']['EBS_HDFC']              = 'EBS_HDFC';
		$this->brands['ogone']['EBS_HYDERABAD']         = 'EBS_HYDERABAD';
		$this->brands['ogone']['EBS_IB']                = 'EBS_IB';
		$this->brands['ogone']['EBS_ICASH']             = 'EBS_ICASH';
		$this->brands['ogone']['EBS_ICICI']             = 'EBS_ICICI';
		$this->brands['ogone']['EBS_INDIA']             = 'EBS_INDIA';
		$this->brands['ogone']['EBS_ITZ']               = 'EBS_ITZ';
		$this->brands['ogone']['EBS_JK']                = 'EBS_JK';
		$this->brands['ogone']['EBS_KARNATAKA']         = 'EBS_KARNATAKA';
		$this->brands['ogone']['EBS_KOTAK']             = 'EBS_KOTAK';
		$this->brands['ogone']['EBS_MYSORE']            = 'EBS_MYSORE';
		$this->brands['ogone']['EBS_TRAVANCORE']        = 'EBS_TRAVANCORE';
		$this->brands['ogone']['eDankort']              = 'eDankort';
		$this->brands['ogone']['EPS']                   = 'EPS';
		$this->brands['ogone']['FidorPay']              = 'FidorPay';
		$this->brands['ogone']['Fortis Pay Button']     = 'Fortis Pay Button';
		$this->brands['ogone']['giropay']               = 'giropay';
		$this->brands['ogone']['iDEAL']                 = 'iDEAL';
		$this->brands['ogone']['ING HomePay']           = 'ING HomePay';
		$this->brands['ogone']['KBC Online']            = 'KBC Online';
		$this->brands['ogone']['MPASS']                 = 'MPASS';
		$this->brands['ogone']['paysafecard']           = 'paysafecard';
		$this->brands['ogone']['PostFinance e-finance'] = 'PostFinance e-finance';

		// Direct Debits
		$this->brands['ogone']['Direct Debits AT'] = 'Direct Debits AT';
		$this->brands['ogone']['Direct Debits DE'] = 'Direct Debits DE';
		$this->brands['ogone']['Direct Debits NL'] = 'Direct Debits NL';

		// Offline Payment
		$this->brands['ogone']['Acceptgiro']          = 'Acceptgiro';
		$this->brands['ogone']['Bank transfer']       = 'Bank transfer';
		$this->brands['ogone']['Bank transfer BE']    = 'Bank transfer BE';
		$this->brands['ogone']['Bank transfer DE']    = 'Bank transfer DE';
		$this->brands['ogone']['Bank transfer FR']    = 'Bank transfer FR';
		$this->brands['ogone']['Bank transfer NL']    = 'Bank transfer NL';
		$this->brands['ogone']['Installments DE']     = 'Installments DE';
		$this->brands['ogone']['Installments DK']     = 'Installments DK';
		$this->brands['ogone']['Installments FI']     = 'Installments FI';
		$this->brands['ogone']['Installments NL']     = 'Installments NL';
		$this->brands['ogone']['Installments NO']     = 'Installments NO';
		$this->brands['ogone']['Installments SE']     = 'Installments SE';
		$this->brands['ogone']['Open Invoice DE']     = 'Open Invoice DE';
		$this->brands['ogone']['Open Invoice DK']     = 'Open Invoice DK';
		$this->brands['ogone']['Open Invoice FI']     = 'Open Invoice FI';
		$this->brands['ogone']['Open Invoice NL']     = 'Open Invoice NL';
		$this->brands['ogone']['Open Invoice NO']     = 'Open Invoice NO';
		$this->brands['ogone']['Open Invoice SE']     = 'Open Invoice SE';
		$this->brands['ogone']['Payment on Delivery'] = 'Payment on Delivery';

		// Gift cards
		$this->brands['ogone']['InterSolve'] = 'InterSolve';

		// Mobile
		$this->brands['ogone']['PingPing'] = 'PingPing';
		$this->brands['ogone']['TUNZ']     = 'TUNZ';

		// Others
		$this->brands['ogone']['cashEX']       = 'cashEX';
		$this->brands['ogone']['cashU']        = 'cashU';
		$this->brands['ogone']['cashU Direct'] = 'cashU Direct';
		$this->brands['ogone']['Payble']       = 'Payble';
		$this->brands['ogone']['PAYPAL']       = 'PAYPAL';
		$this->brands['ogone']['Wallie']       = 'Wallie';

		// Set the URLs
		$this->liveUrl['ogone'] = 'https://secure.ogone.com/ncol/prod/orderstandard.asp';
		$this->testUrl['ogone'] = 'https://secure.ogone.com/ncol/test/orderstandard.asp';

		// ABN-AMRO Internetkassa
		$this->liveUrl['abn-internetkassa'] = 'https://internetkassa.abnamro.nl/ncol/prod/orderstandard.asp';
		$this->testUrl['abn-internetkassa'] = 'https://internetkassa.abnamro.nl/ncol/test/orderstandard.asp';

	}

	/**
	 * Get details for a certain brand.
	 *
	 * @param   string  $psp    The name of the PSP.
	 * @param   string  $brand  The name of the brand.
	 *
	 * @return  string  The type of the card.
	 *
	 * @since   3.0
	 */
	private function get($psp, $brand)
	{
		return $this->brands[$psp][$brand];
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
			'iDEAL'            => \JText::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_IDEAL'),
			'Mastercard'       => \JText::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_MASTERCARD'),
			'VISA'             => \JText::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_VISA'),
			'American Express' => \JText::_('COM_JDIDEALGATWAY_PAYMENT_METHOD_AMEX'),
		);
	}

	/**
	 * Return the live URL.
	 *
	 * @param   string  $psp  The name of the payment provider to get the URL for.
	 *
	 * @return  string  The live URL.
	 *
	 * @since   4.0
	 */
	public function getLiveUrl($psp)
	{
		return $this->liveUrl[$psp];
	}

	/**
	 * Return the test URL.
	 *
	 * @param   string  $psp  The name of the payment provider to get the URL for.
	 *
	 * @return  string  The test URL.
	 *
	 * @since   4.0
	 */
	public function getTestUrl($psp)
	{
		return $this->testUrl[$psp];
	}

	/**
	 * Prepare data for the form.
	 *
	 * @param   Gateway  $jdideal  An instance of JdIdealgateway.
	 * @param   object   $data     An object with transaction information.
	 *
	 * @return  object  The data for the form.
	 *
	 * @since   2.13
	 *
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 */
	public function getForm(Gateway $jdideal, $data)
	{
		// Set the root URL
		$root = $jdideal->getUrl();

		// Get the language
		$language = $jdideal->get('language', 'nl_NL');

		// Check the user language
		if ($language === '*')
		{
			$user            = Factory::getUser();
			$languageDefault = str_replace('-', '_', $user->getParam('language', 'nl_NL'));
			$language        = $languageDefault;
		}

		// Store the information in the log table
		$orderNumber               = $jdideal->get('orderNumber', 'order_id');
		$data->amount              = sprintf('%.2f', $data->amount) * 100;
		$data->orderNumber         = $data->$orderNumber;
		$data->hashinkey           = $jdideal->get('shainkey');
		$data->hashoutkey          = $jdideal->get('shaoutkey');
		$data->merchantID          = $jdideal->get('merchant_id');
		$data->currency            = $data->currency ?: $jdideal->get('currency');
		$data->language            = $language;
		$data->backurl             = $root;
		$data->notifyurl           = $root . 'cli/notify.php';
		$data->look_bgcolor        = $jdideal->get('look_bgcolor');
		$data->look_buttonbgcolor  = $jdideal->get('look_buttonbgcolor');
		$data->look_buttontxtcolor = $jdideal->get('look_buttontxtcolor');
		$data->look_fonttype       = $jdideal->get('look_fonttype');
		$data->look_logo           = $jdideal->get('look_logo');
		$data->look_tblbgcolor     = $jdideal->get('look_tblbgcolor');
		$data->look_tbltxtcolor    = $jdideal->get('look_tbltxtcolor');
		$data->look_title          = $jdideal->get('look_title');
		$data->look_txtcolor       = $jdideal->get('look_txtcolor');

		// Store the currency
		$jdideal->setCurrency($data->currency, $data->logid);
		$jdideal->log('Currency: ' . $data->currency, $data->logid);

		// Check if there is an email address
		if (!$data->email)
		{
			$data->email = '';
		}

		// Replace some predefined values
		$description = $jdideal->replacePlaceholders($data->logid, $jdideal->get('description'));
		$data->com   = substr($description, 0, 100);

		// Get the payment method, plugin overrides component
		if (isset($data->payment_method) && $data->payment_method)
		{
			$data->pm = $this->get('ogone', $data->payment_method);
			$data->brand = $data->payment_method;
			$data->pmlist = '';
		}
		else
		{
			$payment      = $jdideal->get('payment');
			$data->pmlist = '';
			$data->brand  = '';
			$data->pm     = '';

			if (is_array($payment) && $payment[0] !== 'all' && count($payment) >= 1)
			{
				if (count($payment) === 1)
				{
					$data->pm = implode(';', $payment);
				}
				else
				{
					$data->pmlist = implode(';', $payment);
				}
			}
		}

		if ($data->hashinkey)
		{
			// Fields to be included in the hash
			$hashFields             = array();
			$hashFields['PSPID']    = $data->merchantID;
			$hashFields['ORDERID']  = $data->orderNumber;
			$hashFields['AMOUNT']   = $data->amount;
			$hashFields['CURRENCY'] = $data->currency;
			$hashFields['LANGUAGE'] = $data->language;
			$hashFields['EMAIL']    = $data->email;

			$hashFields['COM'] = $data->com;

			$hashFields['BACKURL']      = $data->backurl;
			$hashFields['ACCEPTURL']    = $data->notifyurl;
			$hashFields['DECLINEURL']   = $data->notifyurl;
			$hashFields['EXCEPTIONURL'] = $data->notifyurl;
			$hashFields['CANCELURL']    = $data->notifyurl;

			$hashFields['PM']    = $data->pm;
			$hashFields['BRAND'] = $data->brand;

			$hashFields['PMLIST']  = $data->pmlist;
			$hashFields['COMPLUS'] = $data->logid;

			$hashFields['TITLE']          = $data->look_title;
			$hashFields['BGCOLOR']        = $data->look_bgcolor;
			$hashFields['TXTCOLOR']       = $data->look_txtcolor;
			$hashFields['TBLBGCOLOR']     = $data->look_tblbgcolor;
			$hashFields['TBLTXTCOLOR']    = $data->look_tbltxtcolor;
			$hashFields['BUTTONBGCOLOR']  = $data->look_buttonbgcolor;
			$hashFields['BUTTONTXTCOLOR'] = $data->look_buttontxtcolor;
			$hashFields['LOGO']           = $data->look_logo;
			$hashFields['FONTTYPE']       = $data->look_fonttype;

			// Sort the fields alphabetically
			ksort($hashFields);

			// Construct the string to hash
			$shaString = '';

			foreach ($hashFields as $paramname => $paramvalue)
			{
				if ($paramvalue)
				{
					$shaString .= $paramname . '=' . $paramvalue . $data->hashinkey;
				}
			}

			// SHA calculation
			$data->shasign = strtoupper(hash($jdideal->get('hash', 'sha1'), $shaString));
		}

		return $data;
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
		$logId = $this->jinput->get('COMPLUS');

		if (!$logId)
		{
			throw new \RuntimeException(\JText::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
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
		// Check if we are the customer
		if ($this->isCustomer())
		{
			$this->data['transaction_id'] = $this->jinput->get('PAYID');
		}

		if (!array_key_exists('transaction_id', $this->data))
		{
			throw new \RuntimeException(\JText::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND'));
		}

		// Get the transaction ID
		return $this->data['transaction_id'];
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
	 * @throws  \Exception
	 * @throws  \RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, $logId)
	{
		// Store the transaction reference
		if ($this->data['transaction_id'])
		{
			$jdideal->setTrans($this->data['transaction_id'], $logId);
		}

		// Calculate the seal
		$shaoutkey = $jdideal->get('shaoutkey');

		// Get the parameters
		$sha_out = array_flip($jdideal->get('dynamic_parameters'));
		$data = $this->jinput->post->getArray($sha_out);

		if (array_key_exists('AMOUNT', $sha_out))
		{
			$data['AMOUNT'] = $this->jinput->get('amount');
		}

		if (array_key_exists('CURRENCY', $sha_out))
		{
			$data['CURRENCY'] = $this->jinput->get('currency');
		}

		if (array_key_exists('ORDERID', $sha_out))
		{
			$data['ORDERID'] = $this->jinput->get('orderID');
		}

		ksort($data);

		// Validate data
		$shastring = '';

		foreach ($data as $field => $value)
		{
			// Log the received data
			$jdideal->log($field . ': ' . $value, $logId);

			if (strlen($value) > 0)
			{
				$shastring .= strtoupper($field) . '=' . $value . $shaoutkey;
			}
		}

		// Check the SHA string
		if (strtoupper(hash($jdideal->get('hash', 'sha1'), $shastring)) === $this->jinput->get('SHASIGN'))
		{
			$status['isOK'] = true;
			$status['card'] = $this->jinput->get('BRAND');

			$jdideal->setTransactionDetails($status['card'], 1, $logId);

			switch ($this->jinput->get('STATUS'))
			{
				case '5':
				case '9':
					$status['suggestedAction'] = 'SUCCESS';
					break;
				case '0':
				case '1':
					$status['suggestedAction'] = 'CANCELLED';
					$status['error_message'] = \JText::_('COM_ROPAYMENTS_PAYMENT_CANCELLED');
					break;
				case '2':
					$status['suggestedAction'] = 'OPEN';
					$status['error_message'] = \JText::_('COM_ROPAYMENTS_PAYMENT_NOT_AUTHORIZED');
					break;
				default:
					$status['suggestedAction'] = 'FAILURE';
					$status['error_message'] = \JText::_('COM_ROPAYMENTS_ABN_INTERNETKASSA_RESULT_' . $this->jinput->get('STATUS'));
					break;
			}
		}
		else
		{
			$jdideal->log('Concatenated string: ' . $shastring, $logId);
			$jdideal->log('Calculated string: ' . strtoupper(sha1($shastring)) . ' and received string:' . $this->jinput->get('SHASIGN'), $logId);

			// Store the result
			$status['isOK'] = false;
			$status['suggestedAction'] = 'FAILURE';
			$status['error_message'] = \JText::_('COM_ROPAYMENTS_DATA_NOT_VALIDATED');
			$status['card'] = '';
		}

		// Get the customer info, not available
		$status['consumer'] = array();
		$status['consumer']['consumerAccount'] = '';
		$status['consumer']['consumerName'] = $this->jinput->get('CN');
		$status['consumer']['consumerCity'] = '';

		return $status;
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
		return $this->data['isCustomer'];
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
