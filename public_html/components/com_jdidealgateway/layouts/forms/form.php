<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Jdideal\Psp\Advanced;
use Jdideal\Psp\Buckaroo;
use Jdideal\Psp\Ems;
use Jdideal\Psp\Internetkassa;
use Jdideal\Psp\Kassacompleet;
use Jdideal\Psp\Lite;
use Jdideal\Psp\Mollie;
use Jdideal\Psp\Onlinekassa;
use Jdideal\Psp\Sisow;
use Jdideal\Psp\Targetpay;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * The display data can contain the following parameters
 *
 * amount         The price to be paid
 * order_number   The order number
 * order_id       The order ID
 * origin         The name of the extension calling this form
 * return_url     The URL to send any status to
 * notify_url     The URL to send the notification to
 * cancel_url     The URL to send a cancelled status to
 * email          The customers email
 * payment_method The selected payment method to use
 * currency       The currency to use for the payment
 * profileAlias   The profile alias to use
 * custom_html    Extra text to show on the payment form
 * silent         Set to true to send the customer directly to the payment provider and hide the form
 * banks          The value of the selected bank in case of an iDEAL payment
 */

// Load the language file
$language = Factory::getLanguage();
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway', 'en-GB', true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway', $language->getDefault(), true);
$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway', null, true);

// Data is stored in an array called $displayData, let's put it in a regular array
/** @var array $displayData */
$data = (array) $displayData['data'];

// Turn it into an object
$data = ArrayHelper::toObject($data);

// Check if we have a profile alias, otherwise we set one
if (!isset($data->profileAlias) || empty($data->profileAlias))
{
	$data->profileAlias = null;
}

// Set the autoloader, we may come from a place that never heard about us
JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

// Load the basics
$jinput = Factory::getApplication()->input;
$jdideal = new Gateway($data->profileAlias ?? null);

// Load the profile if needed
if ('' !== $data->profileAlias && null !== $data->profileAlias)
{
	$jdideal->loadConfiguration($data->profileAlias);
}

// Check if ideal is configured
if ($jdideal->psp)
{
	// Check if data is filled
	if (is_object($data) && isset($data->order_id) && '' !== $data->order_id)
	{
		// Fix the amount in case it is in the format of 1,234.56
		$clean        = str_replace(',', '.', $data->amount);
		$lastpos      = strrpos($clean, '.');
		$data->amount = str_replace('.', '', substr($clean, 0, $lastpos)) . substr($clean, $lastpos);

		// Check if the amount has a maximum of 2 digits
		$data->amount = round($data->amount, 2);

		// Check if the order number is not empty
		if ('' === $data->order_number || null === $data->order_number)
		{
			$data->order_number = $data->order_id;
		}

		// Check for defaults
		$data->quantity    = $data->quantity ?? 1;
		$data->currency    = $data->currency ?? 'EUR';
		$data->custom_html = $data->custom_html ?? '';
		$data->silent      = $data->silent ?? false;
		$data->banks       = $data->banks ?? '';
		$data->notify_url  = $data->notify_url ?? '';
		$data->cancel_url  = $data->cancel_url ?? '';
		$data->return_url  = $data->return_url ?? '';
		$data->email       = $data->email ?? '';

		// Set the root URL
		$root = $jdideal->getUrl();

		// Store the information in the log table
		$data->logid = $jdideal->createTransaction(
			$data->order_id,
			$data->order_number,
			$data->quantity,
			$data->currency,
			$data->amount,
			$data->origin,
			$data->return_url,
			$data->cancel_url,
			$data->notify_url,
			'',
			'',
			$language->getTag()
		);

		// Check if we have an empty order number, if so use our own log ID
		if (!$data->order_number)
		{
			$data->order_number = $data->logid;
		}

		// Send the email to the manager if set
		if ($jdideal->get('inform_email', true))
		{
			$config   = Factory::getConfig();
			$from     = $config->get('mailfrom');
			$fromname = $config->get('fromname');
			$mail     = Factory::getMailer();

			// Construct the body
			$mail_tpl = $jdideal->getMailBody('admin_inform_email');

			if ($mail_tpl)
			{
				$find = array();
				$find[] = '{BEDRAG}';
				$find[] = '{ORDERNR}';
				$find[] = '{ORDERID}';
				$replace = array();
				$replace[] = number_format($data->amount, 2, ',', '.');
				$replace[] = $data->order_number;
				$replace[] = $data->order_id;
				$body = str_ireplace($find, $replace, $mail_tpl->body);

				// Construct the subject
				$subject = str_ireplace($find, $replace, $mail_tpl->subject);

				// Send the e-mail
				$emailtos = explode(',', $jdideal->get('jdidealgateway_emailto'));

				if (!empty($emailtos))
				{
					foreach ($emailtos as $email)
					{
						$mail->clearAddresses();
						$mail->sendMail($from, $fromname, $email, $subject, $body, true);
					}
				}
			}
		}

		// Load the form based on active iDEAL
		switch ($jdideal->psp)
		{
			case 'advanced':
				$psp = new Advanced($jinput);
				$output = $psp->getForm($jdideal, $data);

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(array('jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output));
				break;
			case 'mollie':
				$psp = new Mollie($jinput);

				try
				{
					$output = $psp->getForm($jdideal, $data);
				}
				catch (Exception $exception)
				{
					$jdideal->log($exception->getMessage(), $data->logid);

					throw new RuntimeException($exception->getMessage(), $exception->getCode());
				}

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
			case 'targetpay':
				$psp = new Targetpay($jinput);
				$output = $psp->getForm($jdideal, $data);

				$layout = new FileLayout('forms.' . $output->file, null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
			case 'sisow':
				$psp = new Sisow($jinput);
				$output = $psp->getForm($jdideal, $data);
				$data->grouped = false;

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
			case 'buckaroo':
				$psp = new Buckaroo($jinput);
				$output = $psp->getForm($jdideal, $data);

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
			case 'rabo-lite':
			case 'ing-lite':
				$psp = new Lite($jinput);
				$data = $psp->getForm($jdideal, $data);

				// Get the URL
				$url = $psp->getLiveUrl();

				if ($jdideal->get('testmode') === '1')
				{
					$url = $psp->getTestUrl();
				}

				$layout = new FileLayout('forms.lite', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'url' => $url]);
				break;
			case 'abn-internetkassa':
			case 'ogone':
				$psp = new Internetkassa($jinput);
				$data = $psp->getForm($jdideal, $data);

				// Get the URL
				$url = $psp->getLiveUrl($jdideal->psp);

				if ($jdideal->get('testmode') === '1')
				{
					$url = $psp->getTestUrl($jdideal->psp);
				}

				$layout = new FileLayout('forms.internetkassa', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'url' => $url]);
				break;
			case 'kassacompleet':
				$psp = new Kassacompleet($jinput);
				$output = $psp->getForm($jdideal, $data);
				$data->grouped = false;

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
			case 'ems':
				$psp = new Ems($jinput);
				$output = $psp->getForm($jdideal, $data);

				// Get the URL
				$url = $psp->getLiveUrl();

				if ((int) $jdideal->get('testmode') === 1)
				{
					$url = $psp->getTestUrl();
				}

				$layout = new FileLayout('forms.ems', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'url' => $url]);
				break;
			case 'onlinekassa':
				$psp = new Onlinekassa($jinput);
				$output = $psp->getForm($jdideal, $data);

				$layout = new FileLayout('forms.psp', null, ['component' => 'com_jdidealgateway']);
				echo $layout->render(['jdideal' => $jdideal, 'data' => $data, 'root' => $root, 'output' => $output]);
				break;
		}
	}
	else
	{
		if (!is_object($data))
		{
			echo Text::sprintf('COM_ROPAYMENTS_DATA_NOT_OBJECT', gettype($data));
		}
		elseif (!isset($data->order_id))
		{
			echo Text::_('COM_ROPAYMENTS_DATA_HAS_NO_ORDER_ID');
		}
		elseif ('' === $data->order_id)
		{
			echo Text::_('COM_ROPAYMENTS_DATA_HAS_EMPTY_ORDER_ID');
		}
		else
		{
			echo Text::_('COM_ROPAYMENTS_DATA_FAILURE');
		}
	}
}
else
{
	echo Text::_('COM_ROPAYMENTS_NO_IDEAL_CONFIGURED');
}
