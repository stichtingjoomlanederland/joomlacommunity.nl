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
class Sqlite extends None
{
	public function __construct()
	{
		parent::__construct();

		throw new \RuntimeException("Please do not add SQLite databases, they are files. If they are under your site's root they are backed up automatically. Otherwise use the Off-site Directories Inclusion to include them in the backup.");
	}

}
