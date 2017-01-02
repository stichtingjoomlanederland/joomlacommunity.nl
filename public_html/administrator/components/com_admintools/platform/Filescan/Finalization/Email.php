<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Engine\Finalization;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\AdminTools\Admin\Model\ScanAlerts;
use Akeeba\AdminTools\Admin\Model\Scans;
use Akeeba\Engine\Base\Object;
use Akeeba\Engine\Core\Domain\Finalization;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Util\Comconfig;
use FOF30\Container\Container;
use Psr\Log\LogLevel;
use JText;

/**
 * Generates email reports for scan results
 */
class Email extends Object
{
	public function send_scan_email($parent)
	{
		if ($parent instanceof Finalization)
		{
			$parent->relayStep('Sending email');
			$parent->relaySubstep('');
		}

		// If no email is set, quit
		$email = Platform::getInstance()->get_platform_configuration_option('scanemail', '');
		$email = trim($email);

		if (empty($email))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "No email is set. Scan results will not sent by email.");

			return true;
		}

		$container = Container::getInstance('com_admintools');

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Email address set to $email");

		// Get the ID of the scan
		$statistics = Factory::getStatistics();
		$latestBackupId = $statistics->getId();
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Latest scan ID is $latestBackupId");

		// Get scan statistics
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Getting scan statistics");

		/** @var Scans $scanModel */
		$scanModel = $container->factory->model('Scans')->tmpInstance();
		$scanModel->find($latestBackupId);

		// Populate table data for new, modified and suspicious files
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Populating table");
		$body_new = '';
		$body_modified = '';

		/** @var ScanAlerts $scanAlertsModel */
		$scanAlertsModel = $container->factory->model('ScanAlerts')->tmpInstance();

		$totalFiles = $scanAlertsModel
							  ->scan_id($latestBackupId)
							  ->acknowledged(0)
							  ->count();

		$segments = (int)($totalFiles / 100) + 1;
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Processing file list in $segments segment(s)");

		for ($i = 0; $i < $segments; $i++)
		{
			$limitstart = 100 * $i;

			$files = $scanAlertsModel->reset()
							 ->scan_id($latestBackupId)
							 ->acknowledged(0)
							 ->limit(100)
							 ->limitstart($limitstart)
							 ->get();

			if ($files->count())
			{
				/** @var ScanAlerts $file */
				foreach ($files as $file)
				{
					$fileRow = "<tr><td>{$file->path}</td><td>{$file->threat_score}</td></tr>\n";

					if ($file->newfile)
					{
						$body_new .= $fileRow;
					}
					else
					{
						$body_modified .= $fileRow;
					}
				}
			}
		}

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Preparing email text");

		// Prepare the email body
		$body = '<html><head>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_HEADING') . '<title></title></head><body>';
		$body .= '<h1>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_HEADING') . "</h1><hr/>\n";
		$body .= '<h2>' . JText::_('COM_ADMINTOOLS_SCANS_EMAIL_OVERVIEW') . "</h2>\n";
		$body .= "<p>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_TOTAL') . "</strong>: " . $scanModel->multipart . "<br/>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_MODIFIED') . "</strong>: " . $scanModel->files_modified . "<br/>\n";

		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_ADDED') . "</strong>: " . $scanModel->files_new . "<br/>\n";
		$body .= '<strong>' . JText::_('COM_ADMINTOOLS_LBL_SCAN_SUSPICIOUS') . "</strong>: " . (int) $scanModel->files_suspicious . "<br/>\n";
		$body .= "</p>\n";

		// Add the new files report only if we really have some files
		if($body_new)
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
		if($body_modified)
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
		if(!$body_new && !$body_modified)
		{
			$body .= '<p>'.JText::_('COM_ADMINTOOLS_SCANS_EMAIL_NOTHING_TO_REPORT').'</p>';
		}

		unset($body_new);
		unset($body_modified);

		$body .= '</body></html>';

		// Prepare the email subject
		$config = \JFactory::getConfig();
		$sitename = $config->get('sitename', 'Unknown Site');
		$subject = JText::sprintf('COM_ADMINTOOLS_SCANS_EMAIL_SUBJECT', $sitename);

		// Send the email
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ": Ready to send out emails");
		Platform::getInstance()->send_email($email, $subject, $body);

		return true;
	}
}