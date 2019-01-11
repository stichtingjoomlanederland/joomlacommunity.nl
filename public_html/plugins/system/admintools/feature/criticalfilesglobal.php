<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

class AtsystemFeatureCriticalfilesglobal extends AtsystemFeatureAbstract
{
	protected $loadOrder = 999;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->cparams->getValue('criticalfiles_global', '') != '');
	}

	public function onAfterRender()
	{
		$mustSaveData    = false;

		$criticalFiles = $this->cparams->getValue('criticalfiles_global', '');
		$criticalFiles = str_replace("\r", '', $criticalFiles);
		$criticalFiles = explode("\n", $criticalFiles);

		$loadedFiles   = $this->load();
		$alteredFiles  = [];
		$filesToSave   = [];

		foreach ($criticalFiles as $relPath)
		{
			$curInfo = $this->getFileInfo($relPath);

			if ($curInfo == false)
			{
				// Did that file exist? If so, we need to save the critical files list.
				if (is_array($loadedFiles) && array_key_exists($relPath, $loadedFiles))
				{
					$mustSaveData = true;
				}

				continue;
			}

			$filesToSave[$relPath] = $curInfo;

			// Did the file change?
			$oldInfo = null;

			if (isset($loadedFiles[$relPath]))
			{
				$oldInfo = $loadedFiles[$relPath];
			}

			// File changed or it was added later
			if ($oldInfo !== $curInfo)
			{
				$mustSaveData = true;

				// If it was added later, there's no need to send and email
				if ($oldInfo !== null)
				{
					$alteredFiles[$relPath] = [$oldInfo, $curInfo];
				}
			}
		}

		if ($mustSaveData)
		{
			 $this->save($filesToSave);
		}

		if (!empty($alteredFiles))
		{
			$this->sendEmail($alteredFiles);
		}
	}

	/**
	 * Returns information about a file
	 *
	 * @param   string  $relPath  The path to the file relative to the site's root
	 *
	 * @return  null|array  Null if the file is not there, object with information otherwise
	 */
	protected function getFileInfo($relPath)
	{
		$absolutePath = JPATH_SITE . '/' . $relPath;

		if (!file_exists($absolutePath))
		{
			return null;
		}

		return [
			'size'      => @filesize($absolutePath),
			'timestamp' => filemtime($absolutePath),
			'md5'       => @md5_file($absolutePath),
			'sha1'      => @sha1_file($absolutePath),
		];
	}

	/**
	 * Save the critical file information to the database
	 *
	 * @param   array  $fileList  The list of critical file information
	 *
	 * @return  void
	 */
	protected function save(array $fileList)
	{
		$db   = $this->container->db;
		$data = json_encode($fileList);

		$query = $db->getQuery(true)
		            ->delete($db->quoteName('#__admintools_storage'))
		            ->where($db->quoteName('key') . ' = ' . $db->quote('criticalfiles_global'));
		$db->setQuery($query);
		$db->execute();

		$object = (object) array(
			'key'   => 'criticalfiles_global',
			'value' => $data
		);

		$db->insertObject('#__admintools_storage', $object);
	}

	/**
	 * Load the critical file information from the database
	 *
	 * @return  array
	 */
	protected function load()
	{
		$db    = $this->container->db;
		$query = $db->getQuery(true)
		            ->select($db->quoteName('value'))
		            ->from($db->quoteName('#__admintools_storage'))
		            ->where($db->quoteName('key') . ' = ' . $db->quote('criticalfiles_global'));
		$db->setQuery($query);

		$error = 0;

		try
		{
			$jsonData = $db->loadResult();
		}
		catch (Exception $e)
		{
			$error = $e->getCode();
		}

		if (method_exists($db, 'getErrorNum') && $db->getErrorNum())
		{
			$error = $db->getErrorNum();
		}

		if ($error)
		{
			$jsonData = null;
		}

		if (empty($jsonData))
		{
			return [];
		}

		return json_decode($jsonData, true);
	}

	/**
	 * Sends a warning email to the addresses set up to receive security exception emails
	 *
	 * @param   array $alteredFiles The files which were modified
	 *
	 * @return  void
	 */
	private function sendEmail($alteredFiles)
	{
		if (empty($alteredFiles))
		{
			// What are you doing here? There are no altered files.
			return;
		}

		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Convert the list of modified files to HTML
		$htmlAlteredFiles = <<< HTML
<ul>
HTML;

		foreach ($alteredFiles as $fileName => $fileSet)
		{
			list($oldInfo, $curInfo) = $fileSet;

			$oldTime = Date::getInstance($oldInfo['timestamp']);
			$curTime = Date::getInstance($curInfo['timestamp']);
			$oldInfo['timestamp'] = $oldTime->format(JText::_('DATE_FORMAT_LC2'));
			$curInfo['timestamp'] = $curTime->format(JText::_('DATE_FORMAT_LC2'));

			$htmlAlteredFiles .= <<< HTML
	<li>
		$fileName
	</li>
HTML;

		}

		$htmlAlteredFiles .= <<< HTML
</ul>

HTML;

		// Construct the replacement table
		$substitutions = $this->exceptionsHandler->getEmailVariables('', [
			'[INFO]'      => $htmlAlteredFiles,
		]);

		// Let's get the most suitable email template
		$template = $this->exceptionsHandler->getEmailTemplate('criticalfiles_global', true);

		// Got no template, the user didn't published any email template, or the template doesn't want us to
		// send a notification email. Anyway, let's stop here.
		if (!$template)
		{
			return;
		}

		$subject = $template[0];
		$body 	 = $template[1];

		foreach ($substitutions as $k => $v)
		{
			$subject = str_replace($k, $v, $subject);
			$body    = str_replace($k, $v, $body);
		}

		try
		{
			$config = $this->container->platform->getConfig();
			$mailer = JFactory::getMailer();

			$mailfrom = $config->get('mailfrom');
			$fromname = $config->get('fromname');

			$recipients = explode(',', $this->cparams->getValue('emailbreaches', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->isHtml(true);
				$mailer->setSender(array($mailfrom, $fromname));

				// Resets the recipients, otherwise they will pile up
				$mailer->clearAllRecipients();

				if ($mailer->addRecipient($recipient) === false)
				{
					// Failed to add a recipient?
					continue;
				}

				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->Send();
			}
		}
		catch (\Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}

} 
