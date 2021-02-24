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
 * EMS payment class.
 *
 * @package  JDiDEAL
 * @since    4.2.0
 */
class Ems
{
	/**
	 * The store ID
	 *
	 * @var    string
	 * @since  4.2.0
	 */
	private $storeName;

	/**
	 * The shared secret
	 *
	 * @var    string
	 * @since  4.2.0
	 */
	private $sharedSecret;

	/**
	 * The constructor.
	 *
	 * @param   string  $storeName     The store ID.
	 * @param   string  $sharedSecret  The shared secret.
	 *
	 * @since  4.2.0
	 */
	public function __construct($storeName, $sharedSecret)
	{
		$this->storeName    = $storeName;
		$this->sharedSecret = $sharedSecret;
	}

	/**
	 * Create the hash for the transaction.
	 *
	 * @param   string  $dateTime  The date time stamp of the transaction.
	 * @param   string  $amount    The amount to pay in the format of 12.34.
	 * @param   string  $currency  The currency of the amount.
	 *
	 * @return  string An SHA256 hash of the payment details.
	 *
	 * @since   4.2.0
	 */
	public function createHash($dateTime, $amount, $currency): string
	{
		$stringToHash = $this->storeName . $dateTime . $amount . $currency . $this->sharedSecret;
		$ascii        = bin2hex($stringToHash);

		return hash('sha256', $ascii);
	}

	/**
	 * Create the hash for the transaction.
	 *
	 * @param   string  $approvalCode  The returned approval code.
	 * @param   string  $amount        The amount to pay in the format of 12.34.
	 * @param   string  $currency      The currency of the amount.
	 * @param   string  $dateTime      The date time stamp of the transaction.
	 *
	 * @return  string An SHA256 hash of the payment details.
	 *
	 * @since   4.2.0
	 */
	public function getResponseHash($approvalCode, $amount, $currency, $dateTime): string
	{
		$stringToHash = $this->sharedSecret . $approvalCode . $amount . $currency . $dateTime . $this->storeName;
		$ascii        = bin2hex($stringToHash);

		return hash('sha256', $ascii);
	}

	/**
	 * Create the hash for the transaction.
	 *
	 * @param   string  $approvalCode  The returned approval code.
	 * @param   string  $amount        The amount to pay in the format of 12.34.
	 * @param   string  $currency      The currency of the amount.
	 * @param   string  $dateTime      The date time stamp of the transaction.
	 *
	 * @return  string An SHA256 hash of the payment details.
	 *
	 * @since   4.2.0
	 */
	public function getNotificationHash($approvalCode, $amount, $currency, $dateTime): string
	{
		$stringToHash = $amount . $this->sharedSecret . $currency . $dateTime . $this->storeName . $approvalCode;
		$ascii        = bin2hex($stringToHash);

		return hash('sha256', $ascii);
	}
}
