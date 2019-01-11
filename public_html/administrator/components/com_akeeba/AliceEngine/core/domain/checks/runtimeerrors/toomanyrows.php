<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup tables with too many rows, causing the system to fail
 */
class AliceCoreDomainChecksRuntimeerrorsToomanyrows extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(50, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS', $logFile);
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
		$row_limit = 1000000;

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
			preg_match_all('#Continuing dump of (.*?) from record \#(\d+)#i', $data, $matches);

			if (isset($matches[1]) && $matches[1])
			{
				for ($i = 0; $i < count($matches[1]); $i++)
				{
					if ($matches[2][$i] >= $row_limit)
					{
						$table          = trim($matches[1][$i]);
						$tables[$table] = $matches[2][$i];
					}
				}

			}
		}

		fclose($handle);

		if (count($tables))
		{
			$errorMsg = array();

			foreach ($tables as $table => $rows)
			{
				$errorMsg[] = JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS_TABLE') . ' ' . $table . ' ' .
					number_format((float)$rows) . ' ' . JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS_ROWS');
			}

			// Let's raise only a warning, maybe the server is powerful enough to dump huge tables and the problem is somewhere else
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, user is trying to backup huge tables (more than 1M of rows).');

			$this->setResult(0);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS_ERROR', "\n" . implode("\n", $errorMsg)));

			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS_ERROR', '<br/>' . implode('<br/>', $errorMsg)));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, there are no issues while creating the backup archive ');

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TOOMANYROWS_SOLUTION');
	}
}
