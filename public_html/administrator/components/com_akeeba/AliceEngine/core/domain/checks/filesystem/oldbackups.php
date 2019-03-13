<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup old backups
 */
class AliceCoreDomainChecksFilesystemOldbackups extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(40, 'COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_OLD_BACKUPS', $logFile);
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		$prev_data = '';
		$buffer    = 65536;
		$bigfiles  = array();

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

			// Only looking files with extensions like .jpa, .jps, .j01, .j02, ..., .j99, .j100, ..., .j99999
			preg_match_all('#-- Adding.*? <root>/(.*?)(\.(?:jpa|jps|j\d{2,5}))#i', $data, $tmp_matches);

			if (!isset($tmp_matches[1]) || !$tmp_matches[1])
			{
				continue;
			}

			// Record valid matches only
			for ($i = 0; $i < count($tmp_matches[1]); $i++)
			{
				// Get flagged files only once
				$key = md5($tmp_matches[1][$i].$tmp_matches[2][$i]);

				if ( isset($bigfiles[$key]))
				{
					continue;
				}

                $filename = $tmp_matches[1][$i] . $tmp_matches[2][$i];
                $filePath = JPATH_ROOT . '/' . $filename;
                $fileSize = 0;

                if (@file_exists($filePath) && @is_file($filePath))
                {
                    $fileSize = @filesize($filePath);
                }

                if ($fileSize > 1048576)
                {
                    $bigfiles[$key] = array(
                        'filename' => $filename
                    );

                }
			}
		}

		fclose($handle);

		// Let's log all the results
		foreach ($bigfiles as $file)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Old backups detected, position: ' . $file['filename']);
		}

		if ($bigfiles)
		{
			$errorMsg = array();

			$this->setResult(-1);

			foreach ($bigfiles as $bad)
			{
				$errorMsg[] = 'File: ' . $bad['filename'];
			}

			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, found the following old backup files:' . "\n" . implode("\n", $errorMsg));

			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_OLD_BACKUPS_ERROR', "\n" . implode("\n", $errorMsg)));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_OLD_BACKUPS_ERROR', '<br/>' . implode('<br/>', $errorMsg)));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, no large files issue detected.');

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_OLD_BACKUPS_SOLUTION');
	}
}
