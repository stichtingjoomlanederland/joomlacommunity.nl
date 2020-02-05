<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner;

use Akeeba\AdminTools\Admin\Model\ScanAlerts;
use Akeeba\AdminTools\Admin\Model\Scanner\Logger\Logger;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Configuration;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Session;
use Akeeba\AdminTools\Admin\Model\Scans;
use FOF30\Container\Container;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text as JText;
use PHPMailer\PHPMailer\Exception;

defined('_JEXEC') or die;

/**
 * Handles sending a notification email after the PHP File Change Scanner has finished executing
 */
class Email
{
	/**
	 * Scanner configuration
	 *
	 * @var   Configuration
	 */
	private $configuration;

	/**
	 * Logger
	 *
	 * @var   Logger
	 */
	private $logger;

	/**
	 * Temporary session storage
	 *
	 * @var   Session
	 */
	private $session;

	/**
	 * Email constructor.
	 *
	 * @param   Configuration  $configuration  Scanner configuration
	 * @param   Session        $session        Temporary session storage
	 * @param   Logger         $logger         Logger
	 */
	public function __construct(Configuration $configuration, Session $session, Logger $logger)
	{
		$this->configuration = $configuration;
		$this->session       = $session;
		$this->logger        = $logger;
	}

	public function sendEmail()
	{
		// If no email is set, quit
		$email = trim($this->configuration->get('scanemail'));

		if (empty($email))
		{
			$this->logger->debug("No email is set. Scan results will not sent by email.");

			return;
		}

		$container = Container::getInstance('com_admintools');

		$this->logger->debug(sprintf("%s: Email address set to %s", __CLASS__, $email));

		// Get the ID of the scan
		$scanID = $this->session->get('scanID');
		$this->logger->debug(sprintf("%s: Latest scan ID is %s", __CLASS__, $scanID));

		// Get scan statistics
		$this->logger->debug(sprintf("%s: Getting scan statistics", __CLASS__));

		/** @var Scans $scanModel */
		$scanModel = $container->factory->model('Scans')->tmpInstance();
		$scanModel->find($scanID);

		// Populate table data for new, modified and suspicious files
		$this->logger->debug(sprintf("%s: Populating table", __CLASS__));

		$body_new      = '';
		$body_modified = '';

		/** @var ScanAlerts $scanAlertsModel */
		$scanAlertsModel = $container->factory->model('ScanAlerts')->tmpInstance();

		$totalFiles = $scanAlertsModel
			->scan_id($scanID)
			->acknowledged(0)
			->count();

		$segments = (int) ($totalFiles / 100) + 1;
		$this->logger->debug(sprintf("%s: Processing file list in $segments segment(s)", __CLASS__));

		$modified   = 0;
		$new        = 0;
		$suspicious = 0;

		for ($i = 0; $i < $segments; $i++)
		{
			$limitStart = 100 * $i;
			$files      = $scanAlertsModel->reset()
				->scan_id($scanID)
				->acknowledged(0)
				->limit(100)
				->limitstart($limitStart)
				->get();

			if (!$files->count())
			{
				continue;
			}

			/** @var ScanAlerts $file */
			foreach ($files as $file)
			{
				if ($file->threat_score > 0)
				{
					$suspicious++;
				}

				$fileRow = "<tr><td>{$file->path}</td><td>{$file->threat_score}</td></tr>\n";

				if ($file->newfile)
				{
					$body_new .= $fileRow;
					$new++;
				}
				else
				{
					$body_modified .= $fileRow;
					$modified++;
				}
			}
		}

		// Conditional email sending only when actionable items are found
		if ($this->configuration->get('scan_conditional_email'))
		{
			$this->logger->info("You have enabled conditional email sending. Calculating number of actionable items (number of added, modified and suspicious files)");

			$numActionableFiles = $modified + $new + $suspicious;

			if ($numActionableFiles < 1)
			{
				$this->logger->info("No actionable items were detected. An email will NOT be sent this time.");

				return;
			}
		}

		$this->logger->debug(sprintf("%s: Preparing email text", __CLASS__));

		// Prepare the email body
		$body = '<html lang="en"><head>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_HEADING') . '<title></title></head><body>';
		$body .= '<h1>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_HEADING') . "</h1><hr/>\n";
		$body .= '<h2>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_OVERVIEW') . "</h2>\n";
		$body .= "<p>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_TOTAL') . "</strong>: " . $totalFiles . "<br/>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_MODIFIED') . "</strong>: " . $modified . "<br/>\n";

		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_ADDED') . "</strong>: " . $new . "<br/>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_SUSPICIOUS') . "</strong>: " . $suspicious . "<br/>\n";
		$body .= "</p>\n";

		// Add the new files report only if we really have some files
		if ($body_new)
		{
			$body .= '<hr/><h2>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_ADDED') . "</h2>\n";
			$body .= "<table width=\"100%\">\n";
			$body .= "\t<thead>\n";
			$body .= "\t<tr>\n";
			$body .= "\t\t<th>" . JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH') . "</th>\n";
			$body .= "\t\t<th width=\"50\">" . JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE') . "</th>\n";
			$body .= "\t</tr>\n";
			$body .= "\t</thead>\n";
			$body .= "\t<tbody>\n";
			$body .= $body_new;
			$body .= "\t</tbody>\n";
			$body .= '</table>';
		}

		// Add the modified files report only if we really have some files
		if ($body_modified)
		{
			$body .= '<hr/><h2>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_MODIFIED') . "</h2>\n";
			$body .= "<table width=\"100%\">\n";
			$body .= "\t<thead>\n";
			$body .= "\t<tr>\n";
			$body .= "\t\t<th>" . JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH') . "</th>\n";
			$body .= "\t\t<th width=\"50\">" . JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE') . "</th>\n";
			$body .= "\t</tr>\n";
			$body .= "\t</thead>\n";
			$body .= "\t<tbody>\n";
			$body .= $body_modified;
			$body .= "\t</tbody>\n";
			$body .= '</table>';
		}

		// No added or modified files? Let's print a message for the user
		if (!$body_new && !$body_modified)
		{
			$body .= '<p>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_NOTHING_TO_REPORT') . '</p>';
		}

		unset($body_new);
		unset($body_modified);

		$body .= '</body></html>';

		// Prepare the email subject
		$config   = $container->platform->getConfig();
		$sitename = $config->get('sitename', 'Unknown Site');
		$subject  = JText::sprintf('COM_ADMINTOOLS_SCANS_EMAIL_SUBJECT', $sitename);

		// Send the email
		$this->logger->debug(sprintf("%s: Ready to send out emails", __CLASS__));

		try
		{
			$mailer = Factory::getMailer();
			$mailer->isHTML(true);
			$mailer->addRecipient($email);
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->Send();
		}
		catch (Exception $e)
		{
			$this->logger->warning(sprintf("Could not set email. Received error from Joomla's Mailer: %s", $e->getMessage()));
		}
	}

}