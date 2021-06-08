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
use Joomla\CMS\Application\CliApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Http\Response;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * This is a CRON script which should be called from the command-line, not the
 * web. For example something like:
 * /usr/bin/php /path/to/site/cli/statusupdate.php --host=http://www.example.com/
 */

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER))
{
	die();
}

// Set flag that this is a parent file.
define('_JEXEC', 1);

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', 1);

if (file_exists((dirname(__DIR__)) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
elseif (file_exists(JPATH_LIBRARIES . '/import.php'))
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

// Import necessary classes not handled by the autoloaders
jimport('joomla.environment.uri');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.utility');
jimport('joomla.utilities.arrayhelper');
jimport('joomla.environment.request');
jimport('joomla.application.component.helper');
jimport('joomla.application.component.helper');

// Fool Joomla into thinking we're in the administrator with com_app as active component
Factory::getApplication('administrator');
Factory::getApplication()->input->set('option', 'com_jdidealgateway');

// Set our component define
define('JPATH_COMPONENT', JPATH_BASE . '/components/com_jdidealgateway');
define('JPATH_COMPONENT_SITE', JPATH_BASE . '/components/com_jdidealgateway');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_jdidealgateway');

/**
 * Runs a cron job
 *
 * --arguments can have any value
 * -arguments are boolean
 *
 * @since  3.1
 */
class Statusupdate extends CliApplication
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	public function doExecute(): void
	{
		// Merge the default translation with the current translation
		$language = Factory::getLanguage();
		$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway', 'en-GB', true);
		$language->load(
			'com_jdidealgateway',
			JPATH_SITE . '/components/com_jdidealgateway',
			$language->getDefault(),
			true
		);
		$language->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway', null, true);

		// Check if we are being asked for help
		$help = $this->input->get('h', false, 'bool');

		if ($help)
		{
			$this->out(Text::_('COM_ROPAYMENTS_CRON_HELP'));
			$this->out('============================');
			$this->out();
			$this->out(Text::_('COM_JDIDEALGATWAY_USE_CRON'));
			$this->out();
		}
		else
		{
			$this->out(Text::_('COM_ROPAYMENTS_START_SCRIPT'));

			JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

			if (class_exists(Gateway::class) === false)
			{
				JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES . '/Jdideal');
			}


			/** @var Jdideal\Gateway $jdideal */
			$jdideal = new Jdideal\Gateway;
			$host    = $this->input->getString('host', '');
			$cids    = $this->loadTransactions();

			if (substr($host, 0,-1) !== '/')
            {
                $host .= '/';
            }

			$this->out(Text::sprintf('COM_ROPAYMENTS_PROCESS_TRANSACTIONS', count($cids)));

			// Need to set the processed status to 0, to make sure they get processed again
			if (count($cids) > 0)
			{
				$this->setProcessedStatus($cids);
			}

			// Loop through all IDs and call the notify script
			foreach ($cids as $cid)
			{
				try
				{
					$url = false;

					// Load the details
					$details = $jdideal->getDetails($cid);

					// Load the configuration
					$jdideal->loadConfiguration($details->alias);

					$this->out(Text::_('COM_ROPAYMENTS_ORDERID') . $details->order_id);
					$this->out(Text::_('COM_ROPAYMENTS_ORDERNUMBER') . $details->order_number);

					// Construct the URL
					switch ($jdideal->psp)
					{
						case 'advanced':
							$url = $host . 'cli/notify.php?trxid=' . $details->trans . '&ec=' . $details->id;
							break;
						case 'buckaroo':
							$url = $host . 'cli/notify.php?transactionId=' . $details->trans . '&add_logid=' . $details->id;
							break;
						case 'kassacompleet':
							$url = $host . 'cli/notify.php?order_id=' . $details->trans;
							break;
						case 'mollie':
							if (!$details->paymentId)
							{
								throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_MISSING_PAYMENT_ID'));
							}

							$url = $host . 'cli/notify.php?transaction_id=' . $details->trans . '&id=' . $details->paymentId;
							break;
						case 'onlinekassa':
							if (!$details->paymentId)
							{
								throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_MISSING_PAYMENT_ID'));
							}

							$url = $host . 'cli/notify.php?' . $details->paymentId;
							break;
						case 'sisow':
							$url = $host . 'cli/notify.php?trxid=' . $details->trans . '&callback=1';
							break;
						case 'targetpay':
							$url = $host . 'cli/notify.php?trxid=' . $details->trans;
							break;
					}

					if ($url)
					{
						try
						{
							$this->out(Text::sprintf('COM_ROPAYMENTS_PROCESS_URL', $url));

							$options = new Registry;
							$http    = HttpFactory::getHttp($options, ['curl', 'stream']);

							/** @var Response $response */
							$response = $http->get($url);

							$message = Text::_('COM_ROPAYMENTS_CHECKED_TRANSACTION_OK');

							if (500 === $response->code)
							{
								$message = Text::sprintf(
									'COM_ROPAYMENTS_CHECKED_TRANSACTION_ERROR',
									$response->body
								);
							}
						}
						catch (Exception $exception)
						{
							$jdideal->log($exception->getMessage(), $details->id);
							$message = $exception->getMessage();
						}
					}
				}
				catch (Exception $exception)
				{
					$message = $exception->getMessage();
				}

				$this->out($message);
			}

			// Set the last runtime
			$this->setRuntime();

			$this->out(Text::_('COM_ROPAYMENTS_END_SCRIPT'));
		}
	}

	/**
	 * Load the transactions to check.
	 *
	 * @return  array  List of IDs to check.
	 *
	 * @since   3.1.0
	 *
	 * @throws  RuntimeException
	 */
	private function loadTransactions(): array
	{
		// Find last run time
		$configuration = ComponentHelper::getParams('com_jdidealgateway');

		$lastrun = $configuration->get('lastrun', '1970-01-01 00:00:00');

		// Check if we need to override lastrun
		$lastruncli = $this->input->getString('lastrun', false);

		if ($lastruncli)
		{
			$lastrun = $lastruncli;
		}

		$this->out('Last run date ' . $lastrun);

		// Get any extra results to check
		$statuses = explode(',', $this->input->getString('status', ''));

		// Load the transactions
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where('LENGTH(' . $db->quoteName('trans') . ') > 0')
			->where($db->quoteName('date_added') . ' > ' . $db->quote($lastrun))
			->where(
				$db->quoteName('date_added') . ' < ' . $db->quote(
                    HTMLHelper::_('date', date('Y-m-d H:i:s', strtotime("now - 30 minutes")), 'Y-m-d H:i:s')
                )
			);

		// Add the extra statuses
		if (is_array($statuses))
		{
			$where = '(' . $db->quoteName('result') . ' IS NULL';

			foreach ($statuses as $status)
			{
				$where .= ' OR ' . $db->quoteName('result') . ' = ' . $db->quote($status);
			}

			$where .= ')';

			$query->where($where);
		}
		else
		{
			$query->where($db->quoteName('result') . ' IS NULL');
		}

		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Set the processed status to 0 to make sure they get updated again.
	 *
	 * @param   array  $cids  The IDs of the transactions to reset the processd status for
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 *
	 * @throws  RuntimeException
	 */
	private function setProcessedStatus(array $cids): void
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__jdidealgateway_logs'))
			->set($db->quoteName('processed') . ' = 0')
			->where($db->quoteName('id') . ' IN (' . implode(',', $cids) . ')');
		$db->setQuery($query)->execute();
	}

	/**
	 * Set the last runtime.
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 *
	 * @throws  RuntimeException
	 */
	private function setRuntime(): void
	{
		$configuration = ComponentHelper::getParams('com_jdidealgateway');
		$configuration->set('lastrun', Factory::getDate()->toSql());

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('params') . ' = ' . $db->quote($configuration->toString()))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_jdidealgateway'));
		$db->setQuery($query)->execute();
	}
}

try
{
	CliApplication::getInstance('Statusupdate')->execute();
}
catch (Exception $exception)
{
	echo $exception->getMessage() . "\r\n";

	exit($exception->getCode());
}
