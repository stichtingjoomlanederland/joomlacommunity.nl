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

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Plugin to do post processing after payment
 *
 * @package  JDiDEAL
 * @since    6.4.0
 */
class PlgJdidealRoPayments extends CMSPlugin
{
	/**
	 * Handle the onPaymentComplete trigger.
	 *
	 * @param   array  $data  The payment details
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 */
	public function onPaymentComplete(array $data): void
	{
	}

	/**
	 * Handle the onCreateRenewal trigger.
	 *
	 * @param   int  $paymentId      The payment ID
	 * @param   int  $transactionId  The transaction ID
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 */
	public function onCreateRenewal(int $paymentId, int $transactionId): void
	{
	}

	/**
	 * Handle the onCreateSuccess trigger.
	 *
	 * @param   int  $paymentId      The payment ID
	 * @param   int  $transactionId  The transaction ID
	 * @param   int  $logId          The log ID
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 */
	public function onCreateSuccess(int $paymentId, int $transactionId, int $logId): void
	{
	}
}
