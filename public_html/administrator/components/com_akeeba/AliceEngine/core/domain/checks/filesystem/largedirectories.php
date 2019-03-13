<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup directories with a lot of files
 */
class AliceCoreDomainChecksFilesystemLargedirectories extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(30, 'COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_LARGE_DIRECTORIES', $logFile);
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
		$prev_dir  = '';
		$large_dir = array();

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

			// Let's see if I have the loaded profile. If so, check if the user is already using the LSS engine

			// Let's get all the involved directories
			preg_match_all('#Scanning files of <root>/(.*)#', $data, $matches);

			if ( !isset($matches[1]) || empty($matches[1]))
			{
				continue;
			}

			$dirs = $matches[1];

			if ($prev_dir)
			{
				array_unshift($dirs, $prev_dir);
			}

			foreach ($dirs as $dir)
			{
				preg_match_all('#Adding ' . $dir . '/([^\/]*) to#', $data, $tmp_matches);

				if (count($tmp_matches[0]) > 250)
				{
					$large_dir[] = array('position' => $dir, 'elements' => count($tmp_matches[0]));
				}
			}

			$prev_dir = array_pop($dir);
		}

		fclose($handle);

		if ($large_dir)
		{
			$errorMsg = array();

			// Let's log all the results
			foreach ($large_dir as $dir)
			{
				AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Large directory detected, position: ' . $dir['position'] . ', ' . $dir['elements'] . ' elements');

				$errorMsg[] = $dir['position'] . ', ' . $dir['elements'] . ' files';
			}

			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_DIRECTORIES_ERROR', "\n".implode("\n", $errorMsg)));
			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_DIRECTORIES_ERROR', '<br/>' . implode('<br/>', $errorMsg)));
		}

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_DIRECTORIES_SOLUTION');
	}
}
