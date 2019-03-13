<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks if a fatal error occurred during the backup process
 */
class AliceCoreDomainChecksRuntimeerrorsFatalerror extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(110, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_FATALERROR', $logFile);
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
		$error     = false;

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

            preg_match('#ERROR   \|.*?\|(.*)#', $data, $tmp_matches);

            if(isset($tmp_matches[1]))
            {
                $error = $tmp_matches[1];

                break;
            }
		}

		fclose($handle);

		if ($error)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, fatal error detected');

			$this->setResult(-1);
			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_FATALERROR_ERROR', "\n".$error));

			throw new Exception(JText::sprintf('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_FATALERROR_ERROR', '<br/>'.$error));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, there are no issues while creating the backup archive');

		return true;
	}

	public function getSolution()
	{
		return JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_FATALERROR_SOLUTION');
	}
}
