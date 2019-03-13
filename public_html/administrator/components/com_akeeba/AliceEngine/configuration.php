<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * The Akeeba Engine configuration registry class
 */
class AliceConfiguration extends \Akeeba\Engine\Configuration
{
	/** @var string Default NameSpace */
	protected $defaultNameSpace = 'global';

	/** @var array Array keys which may contain stock directory definitions */
	protected $directory_containing_keys = array(
		'akeeba.basic.output_directory'
	);

	/** @var array Keys whose default values should never be overridden */
	protected $protected_nodes = array();

	/** @var array The registry data */
	protected $registry = array();

	/** @var int The currently loaded profile */
	public $activeProfile = null;

	public function __construct()
	{
		// Assisted Singleton pattern
		if (function_exists('debug_backtrace'))
		{
			$caller = debug_backtrace();
			$caller = $caller[1];
			if ($caller['class'] != 'AliceFactory')
			{
				trigger_error("You can't create a direct descendant of " . __CLASS__, E_USER_ERROR);
			}
		}

		// Create the default namespace
		$this->makeNameSpace($this->defaultNameSpace);

		// Create a default configuration
		$this->reset();
	}
}

