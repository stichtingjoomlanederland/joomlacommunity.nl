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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
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
	 * @since   3.0
	 * @throws  RuntimeException
	 * @throws  ApiException
	 * @throws  Exception
	 */
	public function send(): void
	{
		$jdideal = new Gateway;
		$input   = $this->input;

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

	/**
	 * Process a PSP request.
	 *
	 * @return  void
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	public function request(): void
	{
		$redirect = $this->input->getBase64('redirect');

		if (empty($redirect))
		{
			return;
		}

		$transactionId = '';
		$pid           = '';
		$uri           = Uri::getInstance(base64_decode($redirect));

		if ($uri->hasVar('transactionId'))
		{
			$transactionId = $uri->getVar('transactionId');
		}

		if ($uri->hasVar('pid'))
		{
			$pid = $uri->getVar('pid');
		}

		if (empty($transactionId) || empty($pid))
		{
			return;
		}

		BaseDatabaseModel::addIncludePath(
			JPATH_SITE . '/components/com_jdidealgateway/models'
		);
		/** @var JdidealgatewayModelCheckideal $model */
		$model    = $this->getModel('Checkideal', 'JdidealgatewayModel');
		$userName = $model->getUsername($transactionId, $pid);

		if ($userName
			&& Factory::getApplication()->login(
				[
					'username' => $userName
				],
				[
					'entry_url' => $uri->toString(),
					'source'    => 'RO Payments'
				]
			) === false)
		{
			return;
		}

		$this->setRedirect($uri->toString());
		$this->redirect();
	}
}
