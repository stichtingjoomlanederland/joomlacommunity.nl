<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Logs controller.
 *
 * @package  JDiDEAL
 * @since    2.0
 */
class JdidealgatewayControllerLogs extends AdminController
{
	/**
	 * Update the status of one or more log entries.
	 *
	 * @return  void.
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 *
	 * @since   2.13
	 */
	public function checkStatus(): void
	{
		$jdideal = new Gateway;
		$input   = Factory::getApplication()->input;
		$id      = $input->getInt('id', 0);
		$message = Text::_('COM_ROPAYMENTS_CHECKED_TRANSACTION_NOURL');
		$error   = false;

		// Load the details
		$details = $jdideal->getDetails($id);

		try
		{
			$jdideal->log(str_repeat('=*', 25), $id);
			$jdideal->log('Check status on ' . date('Y-m-d H:i:s', time()), $id);

			// Set the processed status to 0, to make sure the status is checked
			$jdideal->setProcessed(0, $id);

			// Construct the URL
			$url    = false;
			$method = 'get';
			$data   = array();

			switch ($jdideal->psp)
			{
				case 'advanced':
					$url = $jdideal->getUrl() . 'cli/notify.php?trxid=' . $details->trans . '&ec=' . $details->id;
					break;
				case 'buckaroo':
					$url = $jdideal->getUrl() . 'cli/notify.php?transactionId=' . $details->trans . '&add_logid=' . $details->id;
					break;
				case 'kassacompleet':
					$url = $jdideal->getUrl() . 'cli/notify.php?order_id=' . $details->trans;
					break;
				case 'mollie':
					if (!$details->paymentId)
					{
						throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_MISSING_PAYMENT_ID'));
					}

					$url = $jdideal->getUrl() . 'cli/notify.php?transaction_id=' . $details->trans . '&id=' . $details->paymentId;
					break;
				case 'onlinekassa':
					if (!$details->paymentId)
					{
						throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_MISSING_PAYMENT_ID'));
					}

					$url = $jdideal->getUrl() . 'cli/notify.php?' . $details->paymentId;
					break;
				case 'sisow':
					$url = $jdideal->getUrl() . 'cli/notify.php?trxid=' . $details->trans . '&callback=1';
					break;
				case 'targetpay':
					$url = $jdideal->getUrl() . 'cli/notify.php?trxid=' . $details->trans;
					break;
				default:
					// Payment method doesn't support checking payment status
					break;
			}

			if ($url)
			{
				try
				{
					$options = new Registry;
					$http    = HttpFactory::getHttp($options, ['curl', 'stream']);

					/** @var JHttpResponse $response */
					switch ($method)
					{
						case 'get':
							$response = $http->get($url);
							break;
						case 'post':
							$response = $http->post($url, $data);
							break;
					}

					// Load the details again
					$details = $jdideal->getDetails($id, 'id', true);

					$message = Text::_('COM_ROPAYMENTS_CHECKED_TRANSACTION_OK');

					if (500 === $response->code)
					{
						$message = Text::sprintf('COM_ROPAYMENTS_CHECKED_TRANSACTION_ERROR', $response->body);
						$error   = true;
					}
				}
				catch (Exception $e)
				{
					$message = $e->getMessage();
					$error   = true;
				}
			}
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$error   = true;
		}

		echo new JsonResponse($details->result, $message, $error);

		Factory::getApplication()->close();
	}
}
