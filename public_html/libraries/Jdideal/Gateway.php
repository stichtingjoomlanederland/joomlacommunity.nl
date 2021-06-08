<?php
/**
 * @package    RO Payments
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

namespace Jdideal;

defined('_JEXEC') or die;

use ContentModelArticle;
use Exception;
use InvalidArgumentException;
use Jdideal\Addons\Addon;
use Jdideal\Addons\AddonInterface;
use JEventDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use RuntimeException;
use stdClass;

/**
 * RO Payments helper.
 *
 * @package     JDiDEAL
 * @subpackage  Core
 * @since       3.0.0
 */
class Gateway
{
	/**
	 * The selected payment processor
	 *
	 * @var    string
	 * @since  3.0.0
	 */
	public $psp = '';

	/**
	 * The ID of the selected payment processor
	 *
	 * @var    integer
	 * @since  4.0.0
	 */
	public $profileId;

	/**
	 * The configuration settings.
	 *
	 * @var    Registry
	 * @since  3.0.0
	 */
	private $configuration = [];

	/**
	 * The transaction details
	 *
	 * @var    array
	 * @since  3.0.0
	 */
	private $logDetails = [];

	/**
	 * JDatabase handler.
	 *
	 * @var    \JDatabaseDriver
	 * @since  3.0.0
	 */
	private $db;

	/**
	 * Construct the helper.
	 *
	 * @param   string  $profileAlias  The name of the profile to use
	 *
	 * @since   3.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  Exception
	 */
	public function __construct($profileAlias = null)
	{
		// Set the database handler
		$this->db = Factory::getDbo();

		// Load the language
		$lang = Factory::getLanguage();
		$lang->load('com_jdidealgateway', JPATH_ADMINISTRATOR . '/components/com_jdidealgateway');
		$lang->load('com_jdidealgateway', JPATH_SITE . '/components/com_jdidealgateway');

		// Find the profile alias, needed to load the configuration
		if ($profileAlias === null)
		{
			$profileAlias = $this->findProfileAlias();
		}

		// Load the configuration
		$this->loadConfiguration($profileAlias);
	}

	/**
	 * Find the log ID for any given payment provider.
	 *
	 * @return  string  The profile alias to load.
	 *
	 * @since   4.0.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  Exception
	 */
	private function findProfileAlias(): string
	{
		// Initialise the alias
		$alias = false;

		// Check how many profiles there are
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'alias',
						'published'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_profiles'))
			->order($db->quoteName('published') . ' DESC,' . $db->quoteName('ordering'));
		$db->setQuery($query);
		$profiles = $db->loadObjectList();

		if (array_key_exists(0, $profiles))
		{
			$alias = $profiles[0]->alias;
		}

		if (count($profiles) > 1)
		{
			$input = Factory::getApplication()->input;

			// List of possible transaction IDs
			$transactionKeys = [
				'trxid',
				'transaction_id',
				'transactionId',
				'PAYID',
				'order_id',
				'oid',
			];

			// Check for known keys
			foreach ($transactionKeys as $key)
			{
				$transactionId = $input->get($key, 0);

				if (0 !== $transactionId)
				{
					// Found a transaction ID
					break;
				}
			}

			// Check if we found a key
			if (0 === $transactionId)
			{
				// No key found yet but we may be using Rabobank Omnikassa, this requires some more work
				$returnData = $input->get('Data', '', 'string');

				if ($returnData)
				{
					$dataArray = explode('|', $returnData);

					foreach ($dataArray as $pair)
					{
						list($name, $value) = explode('=', $pair);

						if ($name === 'transactionReference')
						{
							$transactionId = $value;
						}
					}
				}
			}

			// Check again if we found a key
			$logId = false;

			if (0 !== $transactionId)
			{
				// We found a transaction ID, let's get the log ID for it
				$query = $db->getQuery(true)
					->select($db->quoteName('id'))
					->from($db->quoteName('#__jdidealgateway_logs'))
					->where($db->quoteName('trans') . ' = ' . $db->quote($transactionId));
				$db->setQuery($query);

				$logId = $db->loadResult();
			}

			// If we don't have a log ID try to find one from known keys
			if (!$logId)
			{
				$logKeys = array(
					'ec',
					'add_logid',
					'logid',
					'logId',
					'COMPLUS',
					'id',
				);

				foreach ($logKeys as $key)
				{
					$logId = $input->get($key, 0);

					if (0 !== $logId)
					{
						// Found a log ID
						break;
					}
				}
			}

			if (!$logId)
			{
				// Can't find any reference, take the first available profile
				$alias = $profiles[0]->alias;
			}
			else
			{
				$query->clear()
					->select($db->quoteName('p.alias'))
					->from($db->quoteName('#__jdidealgateway_logs', 'l'))
					->leftJoin(
						$db->quoteName('#__jdidealgateway_profiles', 'p')
						. ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('l.profile_id')
					)
					->where($db->quoteName('l.id') . ' = ' . (int) $logId);
				$db->setQuery($query);
				$alias = $db->loadResult();
			}
		}

		if (!$alias)
		{
			throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_NO_ALIAS_FOUND'));
		}

		return $alias;
	}

	/**
	 * Load the RO Payments configuration.
	 *
	 * @param   string  $profileAlias  The alias of the profile to load.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 * @throws  RuntimeException
	 */
	public function loadConfiguration($profileAlias = null): void
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'psp',
						'paymentInfo'
					]
				)
			)
			->select($db->quoteName('id', 'profile_id'))
			->from($db->quoteName('#__jdidealgateway_profiles'));

		if (null !== $profileAlias)
		{
			$query->where($db->quoteName('alias') . ' = ' . $db->quote($profileAlias));
		}
		else
		{
			$query->where($db->quoteName('published') . ' = 1');
		}

		$db->setQuery($query, 0, 1);

		$config              = $this->db->loadObject();
		$this->configuration = new Registry;

		if ($config)
		{
			$this->configuration = new Registry($config->paymentInfo);
			$this->psp           = $config->psp;
			$this->profileId     = $config->profile_id;
		}
	}

	/**
	 * Set a configuration value.
	 *
	 * @param   string  $name   The name of the parameter
	 * @param   mixed   $value  The value of the parameter
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function set($name, $value): void
	{
		$this->configuration->set($name, $value);
	}

	/**
	 * Send an email to the administrator.
	 *
	 * @param   array  $options  The options to use in the mail.
	 *
	 * @return  boolean  True if mail has been sent | False if mail has not been sent.
	 *
	 * @since   2.2.0
	 * @throws  Exception
	 */
	public function informAdmin($options): bool
	{
		$config   = Factory::getConfig();
		$from     = $config->get('mailfrom');
		$fromname = $config->get('fromname');
		$subject  = false;
		$body     = false;
		$html     = false;

		// Something is up, inform the administrator
		switch ($options['type'])
		{
			case 'status_mismatch':
				$mailTemplate = $this->getMailBody('admin_status_mismatch');

				if ($mailTemplate)
				{
					$find      = [];
					$find[]    = '{ORDERNR}';
					$find[]    = '{EXPECTED_STATUS}';
					$find[]    = '{FOUND_STATUS}';
					$find[]    = '{STATUS}';
					$find[]    = '{HTTP_HOST}';
					$find[]    = '{QUERY_STRING}';
					$find[]    = '{REMOTE_ADDRESS}';
					$find[]    = '{SCRIPT_FILENAME}';
					$find[]    = '{REQUEST_TIME}';
					$replace   = [];
					$replace[] = $options['order_number'];
					$replace[] = $options['expected_status'];
					$replace[] = $options['found_status'];
					$replace[] = $options['status'];
					$replace[] = $_SERVER['HTTP_HOST'];
					$replace[] = $_SERVER['QUERY_STRING'];
					$replace[] = $_SERVER['REMOTE_ADDR'];
					$replace[] = $_SERVER['SCRIPT_FILENAME'];
					$replace[] = $_SERVER['REQUEST_TIME'];
					$body      = str_ireplace($find, $replace, $mailTemplate->body);
					$subject   = $mailTemplate->subject;
					$html      = true;
				}
				else
				{
					$subject = Text::sprintf('COM_ROPAYMENTS_STATUS_MISMATCH', $options['order_number']);
					$body    = "********************\n";
					$body    .= '* ' . Text::_('COM_ROPAYMENTS_STATUS_REPORT') . "\n";
					$body    .= "********************\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_EXPECTED_STATUS', $options['expected_status']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_FOUND_STATUS', $options['found_status']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_ORDER_NUMBER', $options['order_number']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_STATUS', $options['status']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_HTTP_HOST', $_SERVER['HTTP_HOST']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_QUERY_STRING', $_SERVER['QUERY_STRING']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_REMOTE_ADDRESS', $_SERVER['REMOTE_ADDR']) . "\n";
					$body    .= Text::sprintf('COM_ROPAYMENTS_SCRIPT_FILENAME', $_SERVER['SCRIPT_FILENAME']) . "\n";
					$body    .= Text::sprintf(
							'COM_ROPAYMENTS_REQUEST_TIME',
							date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])
						) . "\n";
					$html    = false;
				}
				break;
		}

		if (!$subject && $body && $html)
		{
			return false;
		}

		// Send the e-mail
		$mail = Factory::getMailer();

		return $mail->sendMail($from, $fromname, $from, $subject, $body, $html);
	}

	/**
	 * Load an email message.
	 *
	 * @param   string  $trigger  The name of the email trigger.
	 *
	 * @return  mixed  Mail message object if found | False if no mail message has been found.
	 *
	 * @since   2.8
	 * @throws  RuntimeException
	 */
	public function getMailBody($trigger)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('subject') . ',' . $this->db->quoteName('body'))
			->from($this->db->quoteName('#__jdidealgateway_emails'))
			->where($this->db->quoteName('trigger') . ' = ' . $this->db->quote($trigger));
		$this->db->setQuery($query);

		if (!$this->db->execute())
		{
			return false;
		}

		return $this->db->loadObject();
	}

	/**
	 * Create transaction.
	 *
	 * @param   string  $orderId        The order ID
	 * @param   string  $orderNumber    The order number
	 * @param   string  $quantity       The quantity of items purchased
	 * @param   string  $currency       The currency to use
	 * @param   string  $amount         The amount to be paid
	 * @param   string  $origin         The extension the payment originates from
	 * @param   string  $returnUrl      The return URL
	 * @param   string  $cancelUrl      The cancel URL
	 * @param   string  $notifyUrl      The notify URL
	 * @param   string  $paymentId      The payment ID from the payment provider
	 * @param   string  $transactionId  The transaction ID from RO Payments
	 * @param   string  $language       The user language
	 *
	 * @return  integer  The transaction ID.
	 *
	 * @since   5.0.0
	 */
	public function createTransaction(
		string $orderId,
		string $orderNumber,
		string $quantity,
		string $currency,
		string $amount,
		string $origin,
		string $returnUrl,
		string $cancelUrl,
		string $notifyUrl = '',
		string $paymentId = '',
		string $transactionId = '',
		string $language = ''
	): int {
		$db    = Factory::getDbo();
		$pid   = UserHelper::genRandomPassword(50);
		$date  = HTMLHelper::_('date', 'now', 'Y-m-d H:i:s', false);
		$userId = Factory::getUser()->id;
		$query = $db->getQuery(true);
		$query->insert('#__jdidealgateway_logs')
			->columns(
				$db->quoteName(
					[
						'profile_id',
						'order_id',
						'order_number',
						'quantity',
						'currency',
						'amount',
						'origin',
						'return_url',
						'cancel_url',
						'notify_url',
						'paymentId',
						'trans',
						'language',
						'pid',
						'date_added',
						'user_id'
					]
				)
			)
			->values(
				$db->quote($this->profileId) . ', ' .
				$db->quote($orderId) . ', ' .
				$db->quote($orderNumber) . ', ' .
				$db->quote($quantity) . ', ' .
				$db->quote($currency) . ', ' .
				$db->quote($amount) . ', ' .
				$db->quote($origin) . ', ' .
				$db->quote($returnUrl) . ', ' .
				$db->quote($cancelUrl) . ', ' .
				$db->quote($notifyUrl) . ', ' .
				$db->quote($paymentId) . ', ' .
				$db->quote($transactionId) . ', ' .
				$db->quote($language) . ', ' .
				$db->quote($pid) . ', ' .
				$db->quote($date) . ', ' .
				$db->quote($userId)
			);
		$db->setQuery($query)->execute();

		return $db->insertid();
	}

	/**
	 * Store the transaction details.
	 *
	 * @param   string  $card              The type of payment method that has been used.
	 * @param   int     $processed         Set if the payment has been processed.
	 * @param   int     $logId             The ID of the transaction to add the details to.
	 * @param   string  $paymentReference  The overboeking reference.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function setTransactionDetails($card, $processed, $logId, $paymentReference = null): void
	{
		$this->log('Set transaction details', $logId);
		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__jdidealgateway_logs'))
			->set($this->db->quoteName('card') . ' = ' . $this->db->quote($card))
			->set($this->db->quoteName('processed') . ' = ' . $processed)
			->where($this->db->quoteName('id') . ' = ' . (int) $logId);

		if ($paymentReference)
		{
			$query->set($this->db->quoteName('paymentReference') . ' = ' . $this->db->quote($paymentReference));
		}

		$this->db->setQuery($query)->execute();
	}

	/**
	 * Logs the message to the database.
	 *
	 * @param   string  $message  The message to log.
	 * @param   int     $logId    The ID of the transaction to add the log message to.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function log($message, $logId): void
	{
		if ($logId)
		{
			// Prefix message with timestamp
			$message = '[' . date('Y-m-d H:i:s', time()) . '] ' . $message;

			$query   = $this->db->getQuery(true);
			$message .= "\r\n\r\n";
			$query->update($this->db->quoteName('#__jdidealgateway_logs'))
				->set(
					$this->db->quoteName('history') . ' = CONCAT(' . $this->db->quoteName(
						'history'
					) . ', ' . $this->db->quote($message) . ')'
				)
				->where($this->db->quoteName('id') . ' = ' . (int) $logId);
			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Store the payment ID.
	 *
	 * @param   string  $paymentId  The ID of the payment.
	 * @param   int     $logId      The ID of the transaction to add the details to.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function setPaymentId($paymentId, $logId): void
	{
		if ($logId)
		{
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__jdidealgateway_logs'))
				->set($this->db->quoteName('paymentId') . ' = ' . $this->db->quote($paymentId))
				->where($this->db->quoteName('id') . ' = ' . (int) $logId);

			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Update the transaction status.
	 *
	 * @param   string  $result  The transaction result.
	 * @param   int     $logId   The ID of the transaction to add the status to.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function status($result, $logId): void
	{
		if ($logId)
		{
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__jdidealgateway_logs'))
				->set($this->db->quoteName('result') . ' = ' . $this->db->quote(strtoupper($result)))
				->where($this->db->quoteName('id') . ' = ' . (int) $logId);
			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Update the transaction reference.
	 *
	 * @param   string  $trans  The transaction reference.
	 * @param   int     $logId  The ID of the transaction to add the status to.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 *
	 * @throws  RuntimeException
	 */
	public function setTrans($trans, $logId): void
	{
		if (!$logId)
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__jdidealgateway_logs'))
			->set($this->db->quoteName('trans') . ' = ' . $this->db->quote($trans))
			->where($this->db->quoteName('id') . ' = ' . (int) $logId);
		$this->db->setQuery($query)->execute();
	}

	/**
	 * Update the currency code.
	 *
	 * @param   string  $currency  The currency code.
	 * @param   int     $logId     The ID of the transaction to add the status to.
	 *
	 * @return  void
	 *
	 * @since   4.12.0
	 *
	 * @throws  RuntimeException
	 */
	public function setCurrency($currency, $logId): void
	{
		if (!$logId)
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__jdidealgateway_logs'))
			->set($this->db->quoteName('currency') . ' = ' . $this->db->quote($currency))
			->where($this->db->quoteName('id') . ' = ' . (int) $logId);
		$this->db->setQuery($query)->execute();
	}

	/**
	 * Retrieve transaction reference.
	 *
	 * @param   int  $logId  The ID of the transaction to add the status to.
	 *
	 * @return  string  The transaction reference.
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getTrans($logId): string
	{
		$result = '';

		if ($logId)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('trans'))
				->from($this->db->quoteName('#__jdidealgateway_logs'))
				->where($this->db->quoteName('id') . ' = ' . (int) $logId);
			$this->db->setQuery($query);

			$result = $this->db->loadResult();
		}

		return $result;
	}

	/**
	 * Update the processed status.
	 *
	 * @param   int  $processed  The new processed status.
	 * @param   int  $logId      The ID of the transaction to add the status to.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function setProcessed($processed, $logId): void
	{
		if (!$logId)
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__jdidealgateway_logs'))
			->set($this->db->quoteName('processed') . ' = ' . (int) $processed)
			->where($this->db->quoteName('id') . ' = ' . (int) $logId);
		$this->db->setQuery($query)->execute();
	}

	/**
	 * Load result message.
	 *
	 * @param   int     $logId      The ID of the log entry.
	 * @param   int     $profileId  The ID of the profile
	 * @param   string  $result     The payment result to get the message for
	 *
	 * @return  string  The message with replacements done.
	 *
	 * @since   2.0.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  Exception
	 */
	public function getMessage(int $logId, int $profileId = 0, string $result = ''): string
	{
		$messageType = 0;
		$text        = false;
		$articleId   = false;
		$message     = '';

		// Load the logged details
		if ($logId)
		{
			$details   = $this->getDetails($logId);
			$profileId = $details->profile_id;
			$result    = $details->result;
		}

		// Load the message details
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('message_type', 'messageType'))
			->select($this->db->quoteName('message_text_id', 'articleId'))
			->select($this->db->quoteName('message_text', 'text'))
			->from($this->db->quoteName('#__jdidealgateway_messages'))
			->where($this->db->quoteName('orderstatus') . ' = ' . $this->db->quote($result))
			->where($this->db->quoteName('profile_id') . ' = ' . (int) $profileId)
			->where(
				$this->db->quoteName('language') . ' IN (' . $this->db->quote(
					Factory::getLanguage()->getTag()
				) . ', ' . $this->db->quote('*') . ')'
			);

		$this->db->setQuery($query);
		$messageDetails = $this->db->loadAssoc();

		if (null === $messageDetails)
		{
			return '';
		}

		// Get the message details into the needed variables
		extract($messageDetails, EXTR_OVERWRITE);

		// Check if an article has been selected
		if (!$articleId)
		{
			$messageType = 0;
		}

		switch ($messageType)
		{
			case '2':
				return '';
				break;
			case '1':
				try
				{
					// Load the content route helper for the plugins
					require_once JPATH_SITE . '/components/com_content/helpers/route.php';

					// Load the article model
					require_once JPATH_SITE . '/components/com_content/models/article.php';
					$model = new ContentModelArticle;
					$item  = $model->getItem($articleId);

					$dispatcher = JEventDispatcher::getInstance();
					$params     = Factory::getApplication()->getParams('com_content');
					$user       = Factory::getUser();

					// Check the view access to the article (the model has already computed the values).
					if ($item->params->get('access-view') !== true && ($item->params->get(
								'show_noauth'
							) !== true && $user->get('guest')))
					{
						return '';
					}

					// Set the text
					$item->text = $item->introtext;

					if ($item->params->get('show_intro', 1) === 1)
					{
						$item->text = $item->introtext . ' ' . $item->fulltext;
					}
					elseif ($item->fulltext)
					{
						$item->text = $item->fulltext;
					}

					PluginHelper::importPlugin('content');
					$dispatcher->trigger('onContentPrepare', ['com_content.article', &$item, &$params, 0]);

					// Set the message text
					$message = $item->text;
				}
				catch (Exception $exception)
				{
					$this->log($exception->getMessage(), $logId);
				}
				break;
			default:
				$message = $text;
				break;
		}

		// If there is no log ID, we don't know for which addon the message is, treat it as a general message
		if (!$logId)
		{
			return $message;
		}

		return $this->replacePlaceholders($logId, $message);
	}

	/**
	 * Load transaction details.
	 *
	 * @param   mixed   $logId   The id to search a transaction on.
	 * @param   string  $column  An alternative column to select on.
	 * @param   bool    $force   Force to reload the details.
	 * @param   string  $origin  The origin of the transaction.
	 *
	 * @return  stdClass   The transaction details
	 *
	 * @since    2.7.0
	 *
	 * @throws  RuntimeException
	 */
	public function getDetails($logId, $column = 'id', $force = false, $origin = ''): stdClass
	{
		$identifier = $column . '.' . $logId;

		if ($force || !array_key_exists($identifier, $this->logDetails))
		{
			$query = $this->db->getQuery(true)
				->select(
					$this->db->quoteName(
						[
							'logs.id',
							'logs.profile_id',
							'logs.quantity',
							'logs.currency',
							'logs.amount',
							'logs.trans',
							'logs.cancel_url',
							'logs.return_url',
							'logs.notify_url',
							'logs.origin',
							'logs.order_id',
							'logs.order_number',
							'logs.result',
							'logs.card',
							'logs.processed',
							'logs.paymentReference',
							'logs.paymentId',
							'logs.date_added',
							'logs.language',
							'logs.pid',
							'profiles.alias'
						]
					)
				)
				->from($this->db->quoteName('#__jdidealgateway_logs', 'logs'))
				->leftJoin(
					$this->db->quoteName('#__jdidealgateway_profiles', 'profiles')
					. ' ON ' . $this->db->quoteName('profiles.id') . ' = ' . $this->db->quoteName('logs.profile_id')
				)
				->where($this->db->quoteName('logs.' . $column) . ' = ' . $this->db->quote($logId));

			// Check if we need to verify origin
			if ($origin)
			{
				$query->where($this->db->quoteName('logs.origin') . ' = ' . $this->db->quote($origin));
			}

			$this->db->setQuery($query);
			$this->logDetails[$column . '.' . $logId] = $this->db->loadObject();
		}

		return $this->logDetails[$identifier] ?? new stdClass;
	}

	/**
	 * Replace a given set of variables.
	 *
	 * @param   mixed   $logId    The id to search a transaction on.
	 * @param   string  $message  The message to replace placeholders in
	 * @param   string  $column   An alternative column to select on.
	 *
	 * @return  string  The replaced string.
	 *
	 * @since   4.11.0
	 * @throws  Exception
	 */
	public function replacePlaceholders(int $logId, string $message, string $column = 'id'): string
	{
		$details = $this->getDetails($logId, $column);
		$addon   = $this->getAddon($details->origin);

		// Collect the placeholders
		$find   = [];
		$find[] = '{BEDRAG}';
		$find[] = '{STATUS}';
		$find[] = '{ORDERLINK}';
		$find[] = '{OVERBOEKING_REFERENTIE}';
		$find[] = '{KLANTNR}';
		$find[] = '{ORDERNR}';
		$find[] = '{ORDERID}';

		// Collect the replacements
		$replace   = [];
		$replace[] = number_format($details->amount, 2, ',', '');
		$replace[] = strtolower(Text::_('COM_ROPAYMENTS_STATUS_' . $details->result));
		$link      = $addon->getOrderLink($details->order_id, $details->order_number);
		$replace[] = HTMLHelper::_('link', $link, $link);
		$replace[] = $details->paymentReference;
		$user      = Factory::getUser();
		$replace[] = $user->id;
		$replace[] = $details->order_number;
		$replace[] = $details->order_id;

		$message = $addon->replacePlaceholders($details, $message);

		// Replace and return the string
		return str_ireplace($find, $replace, $message);
	}

	/**
	 * Load the addon.
	 *
	 * @param   string  $origin  The extension to get the addon for.
	 *
	 * @return  AddonInterface  An instance of the addon.
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 */
	public function getAddon($origin): AddonInterface
	{
		$jdidealAddons = new Addon;

		return $jdidealAddons->get($origin);
	}

	/**
	 * Get the new order status.
	 *
	 * @param   string  $status  The status name.
	 *
	 * @return  string  The new status code.
	 *
	 * @since   2.0.0
	 */
	public function getStatusCode($status): string
	{
		switch (strtolower($status))
		{
			case 'success':
			case 'authorized':
			case 'paid':
				$newStatus = $this->get('verifiedStatus');
				break;
			case 'cancel':
			case 'cancelled':
			case 'refunded':
			case 'charged_back':
				$newStatus = $this->get('cancelledStatus');
				break;
			case 'failure':
			case 'fail':
				$newStatus = $this->get('failureStatus');
				break;
			case 'transfer':
				$newStatus = $this->get('transferStatus', $this->get('openStatus'));
				break;
			case 'expired':
				$newStatus = $this->get('expiredStatus', $this->get('openStatus'));
				break;
			default:
			case 'open':
			case 'pending':
			case 'paidout':
			case 'not_authorized':
				$newStatus = $this->get('openStatus');
				break;
		}

		return $newStatus;
	}

	/**
	 * Get a configuration value.
	 *
	 * @param   string  $name     Name of the configuration value to get.
	 * @param   mixed   $default  The default value if nothing is found.
	 *
	 * @return  mixed  The requested value.
	 *
	 * @since   3.0.0
	 */
	public function get($name, $default = '')
	{
		return $this->configuration->get($name, $default);
	}

	/**
	 * Check if the transaction is valid.
	 *
	 * @param   string  $status  The result of the transaction.
	 *
	 * @return  boolean  True if transaction is valid | False if transaction is invalid.
	 *
	 * @since   2.0.0
	 */
	public function isValid($status): bool
	{
		switch (strtolower($status))
		{
			case 'success':
			case 'confirmed':
			case 'authorized':
			case 'transfer':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Get the root URL.
	 *
	 * @return  string  The URL to the site.
	 *
	 * @since   2.9.5
	 */
	public function getUrl(): string
	{
		$uri  = Uri::getInstance();
		$ssl  = $uri->isSSL();
		$root = Uri::root();

		// Check if the root already has https
		if ($ssl && strpos($root, 'https') === false)
		{
			$root = 'https' . substr($root, 4);
		}

		// Remove the cli when ran through the notify script
		if (substr($root, -4) === 'cli/')
		{
			$root = substr($root, 0, -4);
		}

		return $root;
	}

	/**
	 * Get the ID of the selected payment provider.
	 *
	 * @return  integer  The ID of the payment provider.
	 *
	 * @since   4.0.0
	 */
	public function getProfileId(): int
	{
		return $this->profileId;
	}

	/**
	 * Get the alias of the selected payment provider.
	 *
	 * @param   int  $profileId  The ID of the profile to get the alias for.
	 *
	 * @return  string  The alias of the payment provider.
	 *
	 * @since   4.0.0
	 */
	public function getProfileAlias($profileId): string
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('alias'))
			->from($this->db->quoteName('#__jdidealgateway_profiles'))
			->where($this->db->quoteName('id') . ' = ' . (int) $profileId);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Load the PSP notifier.
	 *
	 * @param   string  $psp    The name of the payment provider to load.
	 * @param   Input   $input  The JInput object.
	 *
	 * @return  mixed  The notifier object if found | False if not found.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function loadNotifier($psp, Input $input)
	{
		$notifier = false;

		// Get the active payment provider class
		switch ($psp)
		{
			case 'advanced':
				$notifier = new Psp\Advanced($input);
				break;
			case 'buckaroo':
				$notifier = new Psp\Buckaroo($input);
				break;
			case 'ems':
				$notifier = new Psp\Ems($input);
				break;
			case 'ing-lite':
				$notifier = new Psp\Lite($input);
				break;
			case 'kassacompleet':
				$notifier = new Psp\Kassacompleet($input);
				break;
			case 'mollie':
				$notifier = new Psp\Mollie($input);
				break;
			case 'onlinekassa':
				$notifier = new Psp\Onlinekassa($input);
				break;
			case 'targetpay':
				$notifier = new Psp\Targetpay($input);
				break;
			case 'abn-internetkassa':
			case 'ogone':
				$notifier = new Psp\Internetkassa($input);
				break;
			case 'sisow':
				$notifier = new Psp\Sisow($input);
				break;
		}

		return $notifier;
	}

	/**
	 * Check if an order can be updated or not.
	 *
	 * @param   string  $orderStatus  The current order status
	 *
	 * @return  boolean  True if order can be updated | False otherwise.
	 *
	 * @since   4.8.0
	 */
	public function canUpdateOrder($orderStatus): bool
	{
		return !($orderStatus !== $this->get('pendingStatus', 'P'));
	}
}
