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

use Jdideal\Gateway;
use Jdideal\Psp\Advanced;
use Jdideal\Psp\Buckaroo;
use Jdideal\Psp\Kassacompleet;
use Jdideal\Psp\Mollie;
use Jdideal\Psp\Onlinekassa;
use Jdideal\Psp\Sisow;
use Jdideal\Psp\Targetpay;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Mollie\Api\Exceptions\ApiException;

/**
 * RO Payments Controller.
 *
 * @package  JDiDEAL
 * @since    3.0
 */
class JdidealgatewayControllerCheckIdeal extends BaseController
{
	/**
	 * Process the transaction request and send the customer to the bank
	 *
	 * Internetkassa goes directly to the bank.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  ApiException
	 *
	 * @since   3.0
	 */
	public function send()
	{
		$jdideal = new Gateway;
		$input   = Factory::getApplication()->input;

		switch ($jdideal->psp)
		{
			case 'advanced':
				/** @var Advanced $notifier */
				$notifier = new Advanced($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'mollie':
				/** @var Mollie $notifier */
				$notifier = new Mollie($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'targetpay':
				/** @var Targetpay $notifier */
				$notifier = new Targetpay($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'sisow':
				/** @var Sisow $notifier */
				$notifier = new Sisow($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'buckaroo':
				/** @var Buckaroo $notifier */
				$notifier = new Buckaroo($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'kassacompleet':
				/** @var Kassacompleet $notifier */
				$notifier = new Kassacompleet($input);
				$notifier->sendPayment($jdideal);
				break;
			case 'onlinekassa':
				/** @var Onlinekassa $notifier */
				$notifier = new Onlinekassa($input);
				$notifier->sendPayment($jdideal);
				break;
		}

		Factory::getApplication()->close();
	}
}
