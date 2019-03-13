<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Dump\Reverse;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * A PostgreSQL database dump class, using reverse engineering of the
 * INFORMATION_SCHEMA views to deduce the DDL of the database entities.
 *
 * Configuration parameters:
 * host            <string>    PostgreSQL database server host name or IP address
 * port            <string>    PostgreSQL database server port (optional)
 * username        <string>    PostgreSQL user name, for authentication
 * password        <string>    PostgreSQL password, for authentication
 * database        <string>    PostgreSQL database
 * dumpFile        <string>    Absolute path to dump file; must be writable (optional; if left blank it is automatically calculated)
 */
class Pgsql extends Postgresql
{
	/**
	 * Implements the constructor of the class
	 */
	public function __construct()
	{
		parent::__construct();
	}
}