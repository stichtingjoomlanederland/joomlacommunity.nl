<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Util;

use Akeeba\AdminTools\Admin\Model\Scanner\Mixin\Singleton;
use FOF30\Container\Container;
use FOF30\Params\Params;

defined('_JEXEC') or die;

/**
 * File Change Scanner configuration management
 *
 * The configuration is persisted in the component's configuration (Options page)
 */
class Configuration
{
	use Singleton;

	/**
	 * Default configuration for the File Change Scanner
	 *
	 * @var   array
	 */
	protected $defaultConfig = [
		// Log level (see LogLevel class)
		'logLevel'               => 4,
		// Minimum execution time
		'minExec'                => 3,
		// Maximum execution time
		'maxExec'                => 5,
		// Runtime bias
		'runtimeBias'            => 75,
		// Maximum directories to scan per batch
		'dirThreshold'           => 50,
		// Maximum files to scan per batch
		'fileThreshold'          => 100,
		// Directories to exclude
		'directoryFilters'       => [],
		// Files to exclude
		'fileFilters'            => [],
		// File extensions to scan (everything else is excluded)
		'scanExtensions'         => ['php', 'phps', 'phtml', 'php3', 'inc'],
		// Large file threshold
		'largeFileThreshold'     => 524288,
		// Create diffs for scanned files
		'scandiffs'              => false,
		// Do not create a record for non-threat files
		'scanignorenonthreats'   => false,
		// Do not scan file over this threshold
		'oversizeFileThreshold'  => 5242880,
		// Email address to send scan results to
		'scanemail'              => '',
		// Conditional email sending only when actionable items are found
		'scan_conditional_email' => false,
	];

	/**
	 * The Admin Tools options storage
	 *
	 * @var   Params
	 */
	private $componentConfig;

	/**
	 * Config constructor.
	 *
	 * Initializes the storage.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->componentConfig = Container::getInstance('com_admintools')->params;
	}

	/**
	 * Get a configuration key.
	 *
	 * @param   string  $key  The configuration key to retrieve
	 *
	 * @return  mixed  The value of the configuration key.
	 */
	public function get($key)
	{
		$default = array_key_exists($key, $this->defaultConfig) ? $this->defaultConfig[$key] : null;

		return $this->componentConfig->get($key, $default);
	}

	/**
	 * Set a configuration key.
	 *
	 * Do not include the 'filescanner.' prefix, it is added automatically.
	 *
	 * @param   string  $key    The configuration key to set
	 * @param   mixed   $value  The value to set it to
	 *
	 * @return  void
	 */
	public function set($key, $value)
	{
		$this->componentConfig->set($key, $value);
	}

	/**
	 * Set multiple configuration keys at once
	 *
	 * Do not include the 'filescanner.' prefix in the keys, it is added automatically.
	 *
	 * @param   array  $params  An array of configuration key => value pairs
	 * @param   bool   $save    Should I persist the keys to the database upon setting their value?
	 *
	 * @return  void
	 */
	public function setMany(array $params, $save = true)
	{
		$this->componentConfig->setParams($params);

		if ($save)
		{
			$this->save();
		}
	}

	/**
	 * Persist the configuration to the database
	 *
	 * @return  void
	 */
	public function save()
	{
		$this->componentConfig->save();
	}
}