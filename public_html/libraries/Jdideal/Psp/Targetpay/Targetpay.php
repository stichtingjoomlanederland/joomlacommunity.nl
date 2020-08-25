<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;

/**
 * TargetPay payment class.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class TargetPay
{
	/**
	 * The ID of the bank
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $bank;

	/**
	 * The description to go with the payment. Maximum 32 alphanumeric characters.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $description;

	/**
	 * The amount to be charged.
	 *
	 * @var    integer
	 * @since  4.0.0
	 */
	public $amount;

	/**
	 * The URL to send the payment status to.
	 *
	 * @var    string
	 * @since  4.0
	 */
	public $reporturl;

	/**
	 * The URL to send the customer to.
	 *
	 * @var    string
	 * @since  4.0
	 */
	public $returnurl;

	/**
	 * Set the testmode.
	 *
	 * @var    integer
	 * @since  4.0.0
	 */
	public $testmode;

	/**
	 * The ISO country code.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $country;

	/**
	 * The ID of the type of service.
	 *
	 * 1: Webshop, adult & non-adult
	 * 2: Digital, paid access, non-adult
	 * 3: Digital, paid access, adult
	 *
	 * @var    integer
	 * @since  4.0.0
	 */
	public $type;

	/**
	 * The language the payment should be in.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $language;

	/**
	 * The transaction ID.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $trxId;

	/**
	 * The URL to send the customer to.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $issuerUrl;

	/**
	 * An error message in case of an error
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	public $errorMessage;

	/**
	 * The merchant layout code
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	private $rtlo;

	/**
	 * The constructor.
	 *
	 * @param   string  $rtlo  The merchant layout code.
	 *
	 * @since  4.0.0
	 */
	public function __construct($rtlo)
	{
		$this->rtlo = $rtlo;
	}

	/**
	 * Retrieve a list of banks that support iDEAL.
	 *
	 * @return  string  An HTML select list.
	 *
	 * @since   4.0.0
	 *
	 * @throws  InvalidArgumentException
	 */
	public function directoryRequest(): string
	{
		$options = [];
		$xml     = simplexml_load_file('https://www.targetpay.com/ideal/getissuers?ver=3&format=xml');

		foreach ($xml->children() as $child)
		{
			foreach ($child->attributes() as $attr)
			{
				$options[] = HTMLHelper::_('select.option', $attr, $child);
			}
		}

		return HTMLHelper::_('select.genericlist', $options, 'bank');
	}

	/**
	 * Start the payment transaction.
	 *
	 *  0 : All is good
	 * -1 : No layout code specified
	 * -3 : Amount is less than 84 cents
	 * -4 : Description missing
	 * -5 : Return URL missing
	 * -9 : Bad response
	 *
	 * @param   string  $payment  The type of payment to be used.
	 *
	 * @return  integer  A number indicating if the payment has started.
	 *
	 * @since   4.0.0
	 */
	public function transactionRequest($payment): int
	{
		$payment = strtolower($payment);

		try
		{
			if (!$this->rtlo)
			{
				$this->errorMessage = 'No Layout code';

				return -1;
			}

			if ($this->amount < 0.84)
			{
				$this->errorMessage = 'Amount less than 0.84 cent';

				return -3;
			}

			if (!$this->description)
			{
				$this->errorMessage = 'No description';

				return -4;
			}

			if (!$this->returnurl)
			{
				$this->errorMessage = 'No returnurl';

				return -5;
			}

			// Build parameter string
			$aParameters                = [];
			$aParameters['rtlo']        = $this->rtlo;
			$aParameters['description'] = substr($this->description, 0, 32);
			$aParameters['amount']      = sprintf('%.2f', $this->amount) * 100;
			$aParameters['returnurl']   = $this->returnurl;
			$aParameters['reporturl']   = $this->reporturl;
			$aParameters['test']        = $this->testmode;

			switch ($payment)
			{
				case 'mistercash':
					// Specific settings
					$aParameters['lang']   = 'NL';
					$aParameters['userip'] = $_SERVER['REMOTE_ADDR'];

					// Sent request
					$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/mrcash/start?');
					break;
				case 'sofort':
					$aParameters['country'] = $this->country;
					$aParameters['type']    = $this->type;
					$aParameters['userip']  = $_SERVER['REMOTE_ADDR'];
					$aParameters['lang']    = $this->language;

					// Sent request
					$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/directebanking/start?');
					break;
				case 'paysafecard':
					$aParameters['currency'] = 'EUR';
					$aParameters['country']  = '31';
					$aParameters['language'] = 'NL';
					$aParameters['userip']   = $_SERVER['REMOTE_ADDR'];

					// Sent request
					$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/wallie/start?');
					break;
				case 'ideal':
				default:
					// Specific settings
					$aParameters['ver']  = 3;
					$aParameters['bank'] = $this->bank;

					// Sent request
					$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/ideal/start');
					break;
			}

			$aResponse = explode('|', $strResponse);

			// Bad response
			if (!array_key_exists(1, $aResponse))
			{
				$this->errorMessage = $aResponse[0];

				return -9;
			}

			$iTrxID = explode(' ', $aResponse[0]);

			// We return TRXid and url to redirect
			$this->trxId     = $iTrxID[1];
			$this->issuerUrl = $aResponse[1];

			return 0;
		}
		catch (Exception $exception)
		{
			// Error, could not proceed
			$this->errorMessage = $exception->getMessage();

			return -9;
		}
	}

	/**
	 * Get a response for a TargetPay request.
	 *
	 * @param   array   $aParams   An array with parameters to send to TargetPay.
	 * @param   string  $sRequest  The URL to send the request to.
	 *
	 * @return  string  The response received.
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	protected function getResponse($aParams, $sRequest = 'https://www.targetpay.com/api/plugandpay'): string
	{
		// Store some log information
		$logId = Factory::getApplication()->input->get('logid', 0, 'int');

		if ($logId)
		{
			$jdideal = new Gateway;
			$jdideal->log('Host: ' . $sRequest, $logId);

			foreach ($aParams as $name => $value)
			{
				$jdideal->log($name . ': ' . $value, $logId);
			}
		}

		// Get the transporter
		$http = HttpFactory::getHttp(null, ['curl', 'stream']);

		// Send request
		return $http->post($sRequest, $aParams)->body;
	}

	/**
	 * Check the status of a payment.
	 *
	 * @param   string  $payment        The type of payment used.
	 * @param   string  $transactionId  The transaction ID.
	 *
	 * @return  boolean  True if payment is successful | False if an error has occured.
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function checkPayment($payment, $transactionId): bool
	{
		// Build parameter string
		$aParameters          = [];
		$aParameters['rtlo']  = $this->rtlo;
		$aParameters['trxid'] = $transactionId;
		$aParameters['once']  = $this->testmode ? 0 : 1;

		// Sent the request
		switch ($payment)
		{
			case 'mistercash':
				$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/mrcash/check');
				break;
			case 'sofort':
				$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/directebanking/check');
				break;
			case 'paysafecard':
				$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/wallie/check');
				break;
			case 'ideal':
			default:
				$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/ideal/check');
				break;
		}

		$aResponse = explode('|', $strResponse);

		// Bad response
		if ($aResponse[0] !== '000000 OK')
		{
			$this->errorMessage = $strResponse;

			return false;
		}

		return true;
	}

	/**
	 * Get the amount of the transaction.
	 *
	 * @return  integer  The amount of the transaction.
	 *
	 * @since   4.0.0
	 */
	public function getAmount(): int
	{
		return $this->amount;
	}

	/**
	 * Set the amount of the transaction to be used.
	 *
	 * @param   int  $value  The amount to be charged.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function setAmount($value): void
	{
		$this->amount = $value;
	}

	/**
	 * Get the 2-letter country code.
	 *
	 * @return  string  The 2-letter country code.
	 *
	 * @since   4.0.0
	 */
	public function getCountry(): string
	{
		return $this->country;
	}

	/**
	 * Set the country.
	 *
	 * @param   string  $country  The 2-letter country code.
	 *
	 * @return  void.
	 *
	 * @since   4.0
	 */
	public function setCountry(string $country): void
	{
		$this->country = $country;
	}

	/**
	 * Set the base parameters and send them back.
	 *
	 * @return  array  List of base parameters.
	 *
	 * @since   4.0.0
	 */
	protected function getBaseRequest(): array
	{
		// Return array with base parameters
		$aParams           = [];
		$aParams['action'] = 'start';
		$aParams['ip']     = $_SERVER['REMOTE_ADDR'];
		$aParams['domain'] = JUri::root();
		$aParams['rtlo']   = $this->rtlo;

		return $aParams;
	}

	/**
	 * Get the RTLO.
	 *
	 * @return  string  The RTLO value.
	 *
	 * @since   4.0.0
	 */
	protected function getRtlo(): string
	{
		return $this->rtlo;
	}

	/**
	 * Returns the testmode status.
	 *
	 * @return  integer 1 if enabled | 0 if disabled.
	 *
	 * @since   4.0.0
	 */
	protected function getTestmode(): int
	{
		return $this->testmode;
	}

	/**
	 * Set the testmode on the requests.
	 *
	 * @param   bool  $enable  Set the mode of the test status.
	 *
	 * @return  boolean  The value the test mode is set to.
	 *
	 * @since   4.0.0
	 */
	public function setTestmode($enable = true): bool
	{
		return ($this->testmode = $enable);
	}
}
