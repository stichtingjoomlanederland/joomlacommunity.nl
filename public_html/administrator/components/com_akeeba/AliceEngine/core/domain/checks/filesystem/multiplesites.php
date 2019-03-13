<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup multiple Joomla! installations with a single backup
 */
class AliceCoreDomainChecksFilesystemMultiplesites extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(10, 'COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_SITES', $logFile);
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		$prev_data  = '';
		$buffer     = 65536;
		$subfolders = array();

		while ( !feof($handle))
		{
			$data = $prev_data . fread($handle, $buffer);

			// Let's find the last occurrence of a new line
			$newLine = strrpos($data, "\n");

			// I didn't hit any EOL char, let's keep reading
			if ($newLine === false)
			{
				$prev_data = $data;
				continue;
			}
			else
			{
				// Gotcha! Let's roll back to its position
				$prev_data = '';
				$rollback  = strlen($data) - $newLine + 1;
				$len       = strlen($data);

				$data = substr($data, 0, $newLine);

				// I have to rollback only if I read the whole buffer (ie I'm not at the end of the file)
				// Using this trick should be much more faster than calling ftell to know where we are
				if ($len == $buffer)
				{
					fseek($handle, -$rollback, SEEK_CUR);
				}
			}

			preg_match_all('#Adding\s(.*?)/administrator/index\.php to archive#i', $data, $matches);

			if ($matches[1])
			{
				$subfolders = array_merge($subfolders, $matches[1]);
			}
		}

		fclose($handle);

		if ($subfolders)
		{
			$this->setResult(0);
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, found the following Joomla! sub-directories:' . "\n" . implode("\n", $subfolders));

			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_SITES_ERROR', "\n" . implode("\n", $subfolders)));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_SITES_ERROR', '<br/>' . implode('<br/>', $subfolders)));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, no multiples sites detected.');

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_SITES_SOLUTION');
	}
}
