<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Dump\Native;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Dump\Base;
use Akeeba\Engine\Factory;
use Psr\Log\LogLevel;


/**
 * Dump class for the "None" database driver (ie no database used by the application)
 *
 */
class None extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Populates the table arrays with the information for the db entities to backup
	 *
	 * @return void
	 */
	protected function getTablesToBackup()
	{
	}

	/**
	 * Runs a step of the database dump
	 *
	 * @return void
	 */
	protected function stepDatabaseDump()
	{
		Factory::getLog()->log(LogLevel::INFO, "Reminder: database definitions using the 'None' driver result in no data being backed up.");

		$this->setState('finished');
	}

	/**
	 * Return the current database name by querying the database connection object (e.g. SELECT DATABASE() in MySQL)
	 *
	 * @return  string
	 */
	protected function getDatabaseNameFromConnection()
	{
		return '';
	}
}
