<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * TargetPay SMS processor.
 *
 * @package  JDiDEAL
 * @since    4.0
 */
class TargetPaySms extends TargetPay
{
	/**
	 * Start the payment.
	 *
	 * @return  array  An array with payline, paycode and price.
	 *
	 * @since   4.0
	 */
	public function startPayment ()
	{
		try
		{
			// Build parameter string
			$aParameters = $this->getBaseRequest();
			$aParameters['method'] = 'SMS';
			$aParameters['country'] = $this->getCountry();

			if ($this->amount > 0)
			{
				$aParameters['smsamount'] = ($this->amount * 100);
			}

			// Do request
			$strResponse = $this->getResponse($aParameters);
			$aResponse = explode('|', $strResponse);

			// Bad response
			if (!isset($aResponse[3]))
			{
				throw new Exception('Error' . $aResponse[0]);
			}

			// We return Payline, code, and price
			return array ($aResponse[2], $aResponse[3], $aResponse[4]);
		}
		catch (Exception $e)
		{
			// Error, could not proceed
			$this->errorMessage = $e->getMessage();
		}

		return array();
	}

	/**
	 * Validate the payment.
	 *
	 * @param   string  $strKeyword    The keyword.
	 * @param   string  $strPayCode    The pay code.
	 * @param   string  $strShortcode  The short code.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   4.0
	 */
	public function validatePayment($strKeyword, $strPayCode, $strShortcode)
	{
		try
		{
			// Build parameter string
			$aParameters = array();
			$aParameters['rtlo'] = $this->getRtlo();
			$aParameters['code'] = $strPayCode;
			$aParameters['keyword'] = strtolower($strKeyword);
			$aParameters['shortcode'] = $strShortcode;
			$aParameters['country'] = $this->getCountry();
			$aParameters['test'] = $this->getTestmode();

			// do request
			$strResponse = $this->getResponse($aParameters, 'https://www.targetpay.com/api/sms-pincode?');
			$aResponse = explode('|', $strResponse);

			// Bad response
			if ($aResponse[0] != '000 OK')
			{
				throw new Exception($aResponse[0]);
			}

			return true;

		}
		catch (Exception $e)
		{

			// Error, could not proceed
			$this->errorMessage = $e->getMessage();

			return false;

		}
	}
}
