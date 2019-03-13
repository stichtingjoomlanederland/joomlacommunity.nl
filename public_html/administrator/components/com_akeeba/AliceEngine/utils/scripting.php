<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Scripting helper class
 */
class AliceUtilScripting
{
	/**
	 * Returns an array with domain keys and domain class names for the current
	 * analysis. The idea is that shifting this array walks through the analysis
	 * process. When the array is empty, the analysis is done.
	 *
	 * @return array
	 */
	public static function getDomainChain()
	{
		JLoader::import('joomla.filesystem.folder');
		$basepath = JPATH_ADMINISTRATOR . '/components/com_akeeba/AliceEngine/core/domain';
		$files    = JFolder::files($basepath, '.php');

		$result = array();

		foreach ($files as $file)
		{
			if ($file == 'abstract.php')
			{
				continue;
			}

			$file = str_replace('.php', '', $file);
			$temp = AliceFactory::getDomainObject($file);

			$result[$temp->priority] = array(
				'domain' => $file,
				'class'  => ucfirst($file),
				'name'   => $temp->getStepName()
			);

			unset($temp);
		}

		// Sort domains by priority
		ksort($result);

		return $result;
	}

	/**
	 * Builds a stack of checks.
	 * The idea is that shifting this array walks through the check
	 * process. When the array is empty, checks are done.
	 *
	 * @param   string $check Check folder (ie requirements, postprocessing etc etc)
	 *
	 * @return  array   List of checks to be run, in order of priority
	 */
	public static function getChecksStack($check)
	{
		JLoader::import('joomla.filesystem.folder');
		$basepath = JPATH_ADMINISTRATOR . '/components/com_akeeba/AliceEngine/core/domain/checks/' . $check;
		$files    = JFolder::files($basepath, '.php');

		restore_error_handler();

		$result = array();
		foreach ($files as $file)
		{
			$file      = str_replace('.php', '', $file);
			$className = 'AliceCoreDomainChecks' . ucfirst($check) . ucfirst($file);
			$temp      = new $className;

			$result[$temp->getPriority()] = $className;

			unset($temp);
		}

		// Sort domains by priority
		ksort($result);

		return $result;
	}
}
