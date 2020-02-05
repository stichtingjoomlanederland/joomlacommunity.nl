<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Util;

use Akeeba\AdminTools\Admin\Model\Scanner\Mixin\Singleton;
use FOF30\Container\Container;

defined('_JEXEC') or die;

/**
 * Temporary session data management.
 *
 * This is used to manage the persistence of temporary information between consecutive steps of the file change scanner
 * engine in the session. During CLI execution the pseudo-session of FOF 3 is used instead.
 */
class Session
{
	use Singleton;

	/**
	 * Known temporary variable keys. Used for reset().
	 *
	 * @var   array
	 */
	private $knownKeys = [
		// Position of the DirectoryIterator when scanning subfolders
		'dirPosition',
		// Position of the DirectoryIterator when scanning files
		'filePosition',
		// Step break flag
		'breakFlag',
		// Files already scanned
		'scannedFiles',
		// ID of this scan
		'scanID',
		// Previously completed step number
		'step',
		// Directories to scan
		'directoryQueue',
		// Files to scan
		'fileQueue',
		// Have I finished listing files in the current directory?
		'hasScannedFiles',
		// Have I finished listing folders in the current directory?
		'hasScannedFolders',
		// Current directory being processed
		'currentDirectory',
		// Current state of the Crawler engine
		'crawlerState',
	];

	/**
	 * The container of the component
	 *
	 * @var   Container
	 */
	private $container;

	/**
	 * Session constructor.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->container = Container::getInstance('com_admintools');
	}

	/**
	 * Get the value of a temporary variable
	 *
	 * @param   string      $key      The temporary variable to retrieve
	 * @param   null|mixed  $default  Default value to return if the variable is not set
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		return $this->container->platform->getSessionVar('filescanner.' . $key, $default, 'com_admintools');
	}

	/**
	 * Set the value of a temporary variable
	 *
	 * @param   string  $key    The temporary variable to set
	 * @param   mixed   $value  The value to set it to
	 *
	 * @return  void
	 */
	public function set($key, $value)
	{
		if (!in_array($key, $this->knownKeys))
		{
			$this->knownKeys[] = $key;
		}

		$this->container->platform->setSessionVar('filescanner.' . $key, $value, 'com_admintools');
	}

	/**
	 * Remove (unset) a temporary variable
	 *
	 * @param   string  $key  The variable to unset
	 *
	 * @return  void
	 */
	public function remove($key)
	{
		$this->container->platform->unsetSessionVar('filescanner.' . $key, 'com_admintools');
	}

	/**
	 * Remove all temporary variables from the session.
	 *
	 * IMPORTANT! This only removes the variables in $knownKeys unless you pass it a list of key names to reset. In the
	 * latter case BOTH the known keys AND the $resetKeys will be reset.
	 *
	 * @param   array  $resetKeys  Optional. Additional keys to reset beyond $knownKeys
	 */
	public function reset(array $resetKeys = [])
	{
		foreach (array_unique(array_merge($this->knownKeys, $resetKeys)) as $key)
		{
			$this->remove($key);
		}
	}

	public function getKnownKeys()
	{
		return $this->knownKeys;
	}
}