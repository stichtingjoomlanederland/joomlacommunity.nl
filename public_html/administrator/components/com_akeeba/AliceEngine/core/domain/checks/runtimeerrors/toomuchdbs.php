<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup too many databases, causing the system to fail
 */
class AliceCoreDomainChecksRuntimeerrorsToomuchdbs extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(40, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMUCHDBS', $logFile);
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
		$tables    = array();
		$ex_tables = array();

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

			// Let's save every scanned table
			preg_match_all('#Native\\[a-zA-Z]* :: Adding.*?\(internal name (.*?)\)#i', $data, $matches);

			if (isset($matches[1]))
			{
				$tables = array_merge($tables, $matches[1]);
			}
		}

		fclose($handle);

		// Let's loop on saved tables and look at their prefixes
		foreach ($tables as $table)
		{
			preg_match('/^(.*?_)/', $table, $matches);


			if ($matches[1] !== '#_' && !in_array($matches[1], $ex_tables))
			{
				$ex_tables[] = $matches[1];
			}
		}

		if (count($ex_tables))
		{
			if (count($ex_tables) > 0 && count($ex_tables) <= 3)
			{
				$this->setResult(0);
			}
			else
			{
				$this->setResult(-1);
			}

			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, user is trying to backup ' . count($ex_tables) . ' different databases at once.');

			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMUCHDBS_ERROR');

			throw new Exception(JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMUCHDBS_ERROR'));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, there are no issues while creating the backup archive ');

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMUCHDBS_SOLUTION');
	}
}
