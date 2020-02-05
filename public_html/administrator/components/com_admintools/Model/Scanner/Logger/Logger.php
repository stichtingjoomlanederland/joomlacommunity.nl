<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Logger;

use Akeeba\AdminTools\Admin\Model\Scanner\Mixin\Singleton;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Configuration;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Filesystem;
use FOF30\Container\Container;

defined('_JEXEC') or die;

class Logger
{
	use Singleton;

	/**
	 * Full path to log file
	 *
	 * @var  string
	 */
	protected $logName = null;

	/**
	 * The file pointer to the current log file
	 *
	 * @var  resource
	 */
	protected $fp = null;

	/**
	 * The minimum log level
	 *
	 * @var  int
	 */
	protected $configuredLoglevel;

	/**
	 * The raw path to the site's root
	 *
	 * @var  string
	 */
	protected $siteRoot;

	/**
	 * The normalized path to the site's root
	 *
	 * @var  string
	 */
	protected $normalizedSiteRoot;

	/**
	 * The warnings in the current queue
	 *
	 * @var string[]
	 */
	private $warningsQueue = [];

	/**
	 * The maximum length of the warnings queue
	 *
	 * @var int
	 */
	private $warningsQueueSize = 0;

	/**
	 * The scanner configuration
	 *
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * Public constructor. Initialises the properties with the parameters from the backup profile and platform.
	 */
	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;

		$this->initialiseWithProfileParameters();
	}

	/**
	 * When shutting down this class always close any open log files.
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Clears the logfile
	 *
	 * @return  void
	 */
	public function reset()
	{
		// Get the file names for the default log and the tagged log
		$currentLogName = $this->logName;
		$this->logName  = $this->getLogFilename();

		// Close the file if it's open
		if ($currentLogName == $this->logName)
		{
			$this->close();
		}

		// Remove the log file if it exists
		@unlink($this->logName);

		// Reset the log file
		$fp = @fopen($this->logName, 'w');

		if ($fp === false)
		{
			return;
		}

		fputs($fp, '<?php die(); ?>' . "\n");
		@fclose($fp);
	}

	/**
	 * Writes a line to the log, if the log level is high enough
	 *
	 * @param   string  $level    The log level
	 * @param   string  $message  The message to write to the log
	 * @param   array   $context  The logging context. For PSR-3 compatibility but not used in text file logs.
	 *
	 * @return  void
	 */
	public function log($level, $message = '', array $context = [])
	{
		// Warnings are enqueued no matter what is the minimum log level to report in the log file
		if ($level == LogLevel::WARNING)
		{
			$this->enqueueWarning($message);
		}

		// If we are told to not log anything we can't continue
		if ($this->configuredLoglevel == 0)
		{
			return;
		}

		// Open the log if it's closed
		if (is_null($this->fp))
		{
			$this->open();
		}

		// If the log could not be opened we can't continue
		if (is_null($this->fp))
		{
			return;
		}

		// If the minimum log level is lower than what we're trying to log we cannot continue
		if ($this->configuredLoglevel < $level)
		{
			return;
		}

		// Replace the site's root with <root> in the log file. Replace newlines with the string literal ' \n '
		$message = strtr($message, [
			$this->siteRoot           => "<root>",
			$this->normalizedSiteRoot => "<root>",
			"\r\n"                    => ' \\n ',
			"\r"                      => ' \\n ',
		]);

		switch ($level)
		{
			case LogLevel::ERROR:
				$string = "ERROR   |";
				break;

			case LogLevel::WARNING:
				$string = "WARNING |";
				break;

			case LogLevel::INFO:
				$string = "INFO    |";
				break;

			case LogLevel::DEBUG:
			default:
				$string = "DEBUG   |";
				break;
		}

		$string .= @strftime("%y%m%d %H:%M:%S") . "|$message\r\n";

		@fwrite($this->fp, $string);
	}

	/**
	 * Calculates the absolute path to the log file
	 *
	 * @return    string    The absolute path to the log file
	 */
	public function getLogFilename()
	{
		$container   = Container::getInstance('com_admintools');
		$tmpPath     = $container->platform->getConfig()->get('tmp_path', JPATH_ROOT);
		$logPath     = $container->platform->getConfig()->get('log_path', $tmpPath);
		$logFilePath = $logPath . '/admintools_filescanner.php';

		return Filesystem::normalizePath($logFilePath);
	}

	/**
	 * Close the currently active log and set the current tag to null.
	 *
	 * @return  void
	 */
	public function close()
	{
		// The log file changed. Close the old log.
		if (is_resource($this->fp))
		{
			@fclose($this->fp);
		}

		$this->fp = null;
	}

	/**
	 * Open a new log instance with the specified tag. If another log is already open it is closed before switching to
	 * the new log tag. If the tag is null use the default log defined in the logging system.
	 *
	 * @return void
	 */
	public function open()
	{
		// If the log is already open do nothing
		if (is_resource($this->fp))
		{
			return;
		}

		// Re-initialise site root and minimum log level since the active profile might have changed in the meantime
		$this->initialiseWithProfileParameters();

		// Get the log filename
		$this->logName = $this->getLogFilename();

		// Touch the file
		@touch($this->logName);

		// Open the log file
		$this->fp = @fopen($this->logName, 'ab');

		// If we couldn't open the file set the file pointer to null
		if ($this->fp === false)
		{
			$this->fp = null;
		}
	}

	/**
	 * The process failed with an error. We cannot proceed beyond that point.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function error($message, array $context = [])
	{
		$this->log(LogLevel::ERROR, $message, $context);
	}

	/**
	 * An error which does not prevent the process from continuing. However, the condition must be reported back to the
	 * user.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function warning($message, array $context = [])
	{
		$this->log(LogLevel::WARNING, $message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function info($message, array $context = [])
	{
		$this->log(LogLevel::INFO, $message, $context);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param   string  $message
	 * @param   array   $context
	 *
	 * @return  void
	 */
	public function debug($message, array $context = [])
	{
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	/**
	 * A combination of getWarnings() and resetWarnings(). Returns the warnings and immediately resets the warnings
	 * queue.
	 *
	 * @return array
	 */
	final public function getAndResetWarnings()
	{
		$ret = $this->getWarnings();

		$this->resetWarnings();

		return $ret;
	}

	/**
	 * Returns an array with all warnings logged since the last time warnings were reset. The maximum number of warnings
	 * returned is controlled by setWarningsQueueSize().
	 *
	 * @return array
	 */
	final public function getWarnings()
	{
		return $this->warningsQueue;
	}

	/**
	 * Resets the warnings queue.
	 *
	 * @return void
	 */
	final public function resetWarnings()
	{
		$this->warningsQueue = [];
	}

	/**
	 * Returns the warnings queue size.
	 *
	 * @return int
	 */
	final public function getWarningsQueueSize()
	{
		return $this->warningsQueueSize;
	}

	/**
	 * Set the warnings queue size. A size of 0 means "no limit".
	 *
	 * @param   int  $queueSize  The size of the warnings queue (in number of warnings items)
	 *
	 * @return void
	 */
	final public function setWarningsQueueSize($queueSize = 0)
	{
		if (!is_numeric($queueSize) || empty($queueSize) || ($queueSize < 0))
		{
			$queueSize = 0;
		}

		$this->warningsQueueSize = $queueSize;
	}

	/**
	 * Initialise the logger properties with parameters from the backup profile and the platform
	 *
	 * @return  void
	 */
	protected function initialiseWithProfileParameters()
	{
		$this->siteRoot           = JPATH_ROOT;
		$this->normalizedSiteRoot = Filesystem::normalizePath($this->siteRoot);
		$this->configuredLoglevel = (int) $this->configuration->get('logLevel');
	}

	/**
	 * Adds a warning to the warnings queue.
	 *
	 * @param   string  $warning
	 */
	final protected function enqueueWarning($warning)
	{
		$this->warningsQueue[] = $warning;

		// If there is no queue size limit there's nothing else to be done.
		if ($this->warningsQueueSize <= 0)
		{
			return;
		}

		// If the queue size is exceeded remove as many of the earliest elements as required
		if (count($this->warningsQueue) > $this->warningsQueueSize)
		{
			$this->warningsQueueSize = array_slice($this->warningsQueue, -$this->warningsQueueSize);
		}
	}
}