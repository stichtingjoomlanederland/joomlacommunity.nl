<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Writes messages to the backup log file
 * I couldn't extend the extisting one since there were a lot of "self" calls instead of "static",
 * and inheritance wouldn't work.
 */
class AliceUtilLogger
{
	public static $logName = null;

	static function ResetLog($tag)
	{
		$oldLogName               = AliceUtilLogger::$logName;
		AliceUtilLogger::$logName = AliceUtilLogger::logName($tag);
		$defaultLog               = self::logName(null);

		// Close the file if it's open
		if ($oldLogName == AliceUtilLogger::$logName)
		{
			self::WriteLog(null);
		}

		// Remove any old log file
		@unlink(AliceUtilLogger::$logName);

		if ( !empty($tag))
		{
			// Rename the default log (if it exists) to the new name
			@rename($defaultLog, AliceUtilLogger::$logName);
		}

		// Touch the log file
		$fp = @fopen(AliceUtilLogger::$logName, 'w');
		if ($fp !== false)
		{
			@fclose($fp);
		}

		// Delete the default log
		if ( !empty($tag))
		{
			@unlink($defaultLog);
		}

		@chmod(AliceUtilLogger::$logName, 0666);
		self::WriteLog(true, '');
	}

	static function WriteLog($level, $message = '')
	{
		static $oldLog = null;
		static $configuredLoglevel;
		static $site_root_untranslated;
		static $site_root;
		static $fp = null;

		// Make sure we have a log name
		if (empty(self::$logName))
		{
			self::$logName = self::logName();
		}

		// Check for log name changes
		if (is_null($oldLog))
		{
			$oldLog = self::$logName;
		}
		elseif ($oldLog != self::$logName)
		{
			// The log file changed. Close the old log.
			if (is_resource($fp))
			{
				@fclose($fp);
			}
			$fp = null;
		}

		// Close the log file if the level is set to NULL
		if (is_null($level) && !is_null($fp))
		{
			@fclose($fp);
			$fp = null;

			return;
		}

		if (empty($site_root) || empty($site_root_untranslated))
		{
			$site_root_untranslated = \Akeeba\Engine\Platform::getInstance()->get_site_root();
			$site_root              = \Akeeba\Engine\Factory::getFilesystemTools()
															->TranslateWinPath($site_root_untranslated);
		}

		if (empty($configuredLoglevel) or ($level === true))
		{
			// Load the registry and fetch log level
			$registry           = \Akeeba\Engine\Factory::getConfiguration();
			$configuredLoglevel = $registry->get('akeeba.basic.log_level');
			$configuredLoglevel = $configuredLoglevel * 1;

			return;
		}

		if ($level === false)
		{
			// Pause logging
			$configuredLogLevel = false;

			return;
		}

		// Catch paused logging
		if ($configuredLoglevel === false)
		{
			return;
		}

		if (($configuredLoglevel >= $level) && ($configuredLoglevel != 0))
		{
			if ( !defined('AKEEBADEBUG'))
			{
				$message = str_replace($site_root_untranslated, "<root>", $message);
				$message = str_replace($site_root, "<root>", $message);
			}
			$message = str_replace("\n", ' \n ', $message);
			switch ($level)
			{
				case _AE_LOG_ERROR:
					$string = "ERROR   |";
					break;
				case _AE_LOG_WARNING:
					$string = "WARNING |";
					break;
				case _AE_LOG_INFO:
					$string = "INFO    |";
					break;
				default:
					$string = "DEBUG   |";
					break;
			}
			$string .= @strftime("%y%m%d %H:%M:%S") . "|$message\r\n";

			if (is_null($fp))
			{
				$fp = @fopen(AliceUtilLogger::$logName, "a");
			}

			if ( !($fp === false))
			{
				$result = @fwrite($fp, $string);
				if ($result === false)
				{
					// Try harder with the file pointer, will ya?
					$fp     = @fopen(AliceUtilLogger::$logName, "a");
					$result = @fwrite($fp, $string);
				}
			}
		}
	}

	/**
	 * Calculates the absolute path to the log file. Instead of using the path coming from
	 * Akeeba Backup config, we will always use Joomla Tmp dir
	 *
	 * @param   string $tag The backup run's tag
	 *
	 * @return  string    The absolute path to the log file
	 */
	public static function logName($tag = null)
	{
		if (empty($tag))
		{
			$fileName = 'akeeba.log';
		}
		else
		{
			$fileName = "akeeba.$tag.log";
		}

		// Get log's file name
		return \Akeeba\Engine\Factory::getFilesystemTools()
									 ->TranslateWinPath(JPATH_ROOT . '/tmp' . DIRECTORY_SEPARATOR . $fileName);
	}

	public static function closeLog()
	{
		self::WriteLog(null, null);
	}

	public static function openLog($tag = null)
	{
		self::$logName = self::logName($tag);
		@touch(self::$logName);
	}
}

// Make sure we close the log file every time we finish with a page load
register_shutdown_function(array('AliceUtilLogger', 'closeLog'));
