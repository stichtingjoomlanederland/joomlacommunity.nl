<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   ALICE
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

abstract class AliceCoreDomainChecksAbstract
{
	/** @var int Check priority */
	protected $priority = 0;
	/** @var null Handle to log file */
	protected $logFile = null;
	/** @var string Human name of the running check */
	protected $checkName = '';

	protected $result = 1;

	public function __construct($priority, $checksName, $logFile = null)
	{
		$this->priority  = $priority;
		$this->checkName = $checksName;
		$this->logFile   = $logFile;
	}

	/**
	 * Performs check.
	 *
	 * @throws Exception    If the check is not passed, a detailed error message should be set
	 *                      inside the exception
	 *
	 * @return bool         True on success
	 */
	abstract public function check();

	abstract public function getSolution();

	/**
	 * Set the result for current check. Allowed values:
	 *  1 (success)
	 *  0 (warning)
	 * -1 (failure)
	 *
	 * @param $result
	 */
	public function setResult($result)
	{
		// Allow only a set of results
		if ( !in_array($result, array(1, 0, -1)))
		{
			return;
		}

		$this->result = $result;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function getName()
	{
		return $this->checkName;
	}

	public function setLogFile($log)
	{
		$this->logFile = $log;
	}
}