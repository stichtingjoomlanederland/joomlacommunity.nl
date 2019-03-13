<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is post processing the archive but didn't set any part size.
 * Most likely this could lead to timeouts while uploading
 */
class AliceCoreDomainChecksRuntimeerrorsPartsize extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(70, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_PART_SIZE', $logFile);
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		$partsize  = 0;
		$postproc  = '';
		$prev_data = '';
		$buffer    = 65536;

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

			if ( !$partsize)
			{
				preg_match('#\|Part size.*:(\d+)#i', $data, $match);

				if (isset($match[1]))
				{
					$partsize = $match[1];
				}
			}

			if ( !$postproc)
			{
				preg_match('#Loading.*post-processing.*?\((.*?)\)#i', $data, $match);

				if (isset($match[1]))
				{
					$postproc = trim($match[1]);
				}
			}
		}

		fclose($handle);

		// The default part size 2Gb
		if ($partsize > 2000000000 && $postproc != 'none')
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . " Test error, there is a post processing engine ($postproc) but no part size");

			$this->setResult(0);
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_PART_SIZE_ERROR');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_PART_SIZE_ERROR'));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . " Test passed, part size is consistent with post processing engine seems ok.");

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_PART_SIZE_SOLUTION');
	}
}
