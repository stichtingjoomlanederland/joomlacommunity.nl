<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.522
 * @date				2016-05-17
 */

// no direct access
defined('_JEXEC') or die;

class ShlSystem_Resourcemanager
{

	const CONSUMERS_TABLE_NAME = '#__shlib_consumers';
	const RESOURCES_TABLE_NAME = '#__shlib_resources';

	/**
	 * Register that an extension is using a resource,
	 * possibly specifying required min and max version
	 * of the resource the extension can handle
	 *
	 * @param array $options
	 * @param boolean true if registered, false otherwise
	 */
	public static function register($options)
	{

		if (empty($options['context']))
		{
			return false;
		}

		$resource = empty($options['resource']) ? 'shlib' : $options['resource'];

		$record = array('resource' => $resource, 'context' => $options['context'],
			'min_version' => empty($options['min_version']) ? 0 : $options['min_version'],
			'max_version' => empty($options['max_version']) ? 0 : $options['max_version'],
			'refuse_versions' => empty($options['refuse_versions']) ? '' : $options['refuse_versions'],
			'accept_versions' => empty($options['accept_versions']) ? '' : $options['accept_versions']);

		try
		{
			// ShlDbHelper::insertUpdate( self::CONSUMERS_TABLE_NAME, $record, array( 'context' => $options['context']));
			// do we have a record?
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')->from(self::CONSUMERS_TABLE_NAME);
			$query->where($db->quoteName('context') . '=' . $db->quote($options['context']));
			$existingRecord = $db->setQuery($query)->loadObject();

			// if not, create one, else update existing
			$query->clear();
			if (empty($existingRecord))
			{
				$query->insert(self::CONSUMERS_TABLE_NAME);
			}
			else
			{
				$query->update(self::CONSUMERS_TABLE_NAME);
				$query->where($db->quoteName('context') . '=' . $db->quote($options['context']));
			}
			foreach ($record as $key => $value)
			{
				$query->set($db->quoteName($key) . '=' . $db->quote($value));
			}
			$db->setQuery($query)->execute();

		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}

		return true;
	}

	/**
	 * Unregister an extension from using a resource
	 *
	 * @param string $resource unique identifier for the resource being shared
	 * @param string $context unique identifier for an extension using shLib, usually component name
	 * @return [boolean | object] false if db error occured, object holding previous registration data, or null if was not registered
	 */
	public static function unregister($resource, $context)
	{

		try
		{
			$registrationData = self::isRegistered($resource, $context);
			if (!empty($registrationData))
			{
				//ShlDbHelper::delete(self::CONSUMERS_TABLE_NAME, array('resource' => $resource, 'context' => $context));
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->delete();
				$query->from(self::CONSUMERS_TABLE_NAME);
				$query->where($db->quoteName('resource') . '=' . $db->quote($resource));
				$query->where($db->quoteName('context') . '=' . $db->quote($context));
				$db->setQuery($query)->execute();
			}
			else
			{
				$registrationData = null;
			}
			return $registrationData;
		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}

	}

	/**
	 * Find if an extension is registered as using a resource
	 *
	 * @param string $resource unique identifier for the resource being shared
	 * @param string $context unique identifier for an extension using shLib, usually component name
	 * @return [boolean | object] false if extension not registered, registration data as an object if registered
	 */
	public static function isRegistered($resource, $context)
	{

		try
		{
			//$registrationData = ShlDbHelper::selectObject(self::CONSUMERS_TABLE_NAME, '*', array('resource' => $resource, 'context' => $context));
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')->from(self::CONSUMERS_TABLE_NAME);
			$query->where($db->quoteName('resource') . '=' . $db->quote($resource));
			$query->where($db->quoteName('context') . '=' . $db->quote($context));
			$registrationData = $db->setQuery($query)->loadObject();

			return $registrationData;
		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}
	}

	/**
	 * Finds if an extension can install a new version of a resource, based on
	 * what possibly previously installed other extensions have registered
	 * as acceptable versions.
	 * Also forbid installing an older version over a newer one, unless update is forced
	 *
	 * @param string $resource unique identifier for the resource being shared
	 * @param string $newVersion version number of new version of shLib to be installed
	 * @param string $allowDowngrade if true, installing an older version over a newer is allowed
	 * @return boolean true if allowed to install new version
	 */
	public static function canInstall($resource, $newVersion, $allowDowngrade = false, $acceptMin = 0, $acceptMax = 0)
	{

		$result = new stdClass();
		$result->canInstall = 'yes';
		$result->reason = '';

		// read current version, if any
		$currentVersion = self::getResourceCurrentVersion($resource);

		// cannot install older over newer
		if (!$allowDowngrade)
		{
			if (!empty($currentVersion) && version_compare($newVersion, $currentVersion, 'le'))
			{
				if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'debug'))
				{
					ShlSystem_Log::debug('shlib',
						'Skipping install of ' . $resource . ' version ' . $newVersion . ' over existing version ' . $currentVersion);
				}
				$result->canInstall = 'skip';
				$result->reason = 'Same or more recent version of ' . $resource . ' already installed.';

				// additional checks: can incoming extension work with already installed shared resource?
				if(!empty($acceptMin) && version_compare($currentVersion, $acceptMin, '<'))
				{
					$result->canInstall = 'no';
					$result->reason = 'Cannot install: resource ' . $resource . ' requires at least version ' . $acceptMin . ', version ' . $currentVersion . ' is installed.';
				}
				if(!empty($acceptMax) && version_compare($currentVersion, $acceptMax, '>'))
				{
					$result->canInstall = 'no';
					$result->reason = 'Cannot install: resource ' . $resource . ' requires at most version ' . $acceptMax . ', version ' . $currentVersion . ' is installed.';
				}
			}
		}

		// read existing extensions requirements
		if (!empty($currentVersion) && $result->canInstall != 'no' && $result->canInstall != 'skip')
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')->from(self::CONSUMERS_TABLE_NAME)->where($db->quoteName('resource') . ' = ' . $db->quote($resource));
			$db->setQuery($query);
			try
			{
				$consumers = $db->loadObjectList();
			}
			catch (Exception $e)
			{
				if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
				{
					ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
				}
				$consumers = array();
			}

			if (!empty($consumers))
			{
				$rejected = false;
				foreach ($consumers as $consumer)
				{
					if (!empty($consumer->accept_versions))
					{
						$accepted = explode(',', $consumer->accept_versions);
						if (in_array($newVersion, $accepted))
						{
							$result->reason = 'Resource ' . $consumer->context . ' accepts ' . $resource . ' version ' . $newVersion
								. '. Listed explicitely as an accepted version';
							continue;
						}
					}
					if ($consumer->min_version > '0' && version_compare($newVersion, $consumer->min_version, '<'))
					{
						$result->canInstall = 'no';
						$result->reason = 'Resource ' . $consumer->context . ' cannot accept ' . $resource . ' version ' . $newVersion
							. '. Requires at least ' . $consumer->min_version;
						break;
					}
					else if ($consumer->max_version > '0' && version_compare($newVersion, $consumer->max_version, '>'))
					{
						$result->canInstall = 'no';
						$result->reason = 'Resource ' . $consumer->context . ' cannot accept ' . $resource . ' version ' . $newVersion
							. '. Requires at most ' . $consumer->max_version;
						break;
					}
					else
					{
						if (!empty($consumer->refuse_versions))
						{
							$refused = explode(',', $consumer->refuse_versions);
							if (in_array($newVersion, $refused))
							{
								$result->canInstall = 'no';
								$result->reason = 'Resource ' . $consumer->context . ' cannot accept ' . $resource . ' version ' . $newVersion
									. '. Listed explicitely as a refused version';
								break;
							}
						}
					}
				}
			}
		}

		if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'debug'))
		{
			ShlSystem_Log::debug('shlib',
				'Resource manager: attempt to install ' . $resource . ' version ' . $newVersion . ' | result: ' . $result->canInstall
					. (empty($result->reason) ? '' : ' ' . $result->reason));
		}

		return $result;
	}

	/**
	 * Finds if an extension is allowed to uninstall a resource from the system
	 * This will happen only if no extension at all is currenlty registered as using this resource
	 *
	 * @param string $resource unique identifier for the resource being shared
	 */
	public static function canUninstall($resource)
	{

		try
		{
			//$registered = ShlDbHelper::selectObjectList(self::CONSUMERS_TABLE_NAME, '*', array('resource' => $resource));
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')->from(self::CONSUMERS_TABLE_NAME);
			$query->where($db->quoteName('resource') . '=' . $db->quote($resource));
			$registered = $db->setQuery($query)->loadObjectList();

			$canUninstall = empty($registered);
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'debug'))
			{
				ShlSystem_Log::debug('shlib',
					'Request to uninstall ' . $resource . ': ' . ($canUninstall ? 'granted' : 'denied') . ' [' . print_r($registered, true) . ']');
			}
			return $canUninstall;
		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}
	}

	/**
	 * Register that a resource has been installed on the system
	 *
	 * @param string $resource unique identifier for the resource being shared
	 * @param string $version version number for resource that has been installed
	 */
	public static function registerResource($resource, $version)
	{

		if (empty($version))
		{
			return false;
		}

		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// is this an update or a new install?
			$query->select('id')->from(self::RESOURCES_TABLE_NAME)->where($db->quoteName('resource') . '=' . $db->quote($resource));
			$db->setQuery($query);
			$installed = $db->loadResult();
			if (empty($installed))
			{
				// new install, create record
				$query->insert($db->quoteName(self::RESOURCES_TABLE_NAME))
					->set(
						$db->quoteName('current_version') . '=' . $db->quote($version) . ', ' . $db->quoteName('resource') . '='
							. $db->quote($resource));
				$db->setQuery($query)->execute();
			}
			else
			{
				// existing record, update
				$query->update($db->quoteName(self::RESOURCES_TABLE_NAME))->set($db->quoteName('current_version') . '=' . $db->quote($version))
					->where($db->quoteName('resource') . '=' . $db->quote($resource));
				$db->setQuery($query)->execute();
			}
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'debug'))
			{
				ShlSystem_Log::debug('shlib', 'Installed new shared resource ' . $resource . ' version ' . $version);
			}
		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}

		return true;
	}

	/**
	 * Get currently installed version of a given resource
	 *
	 * @param string $resource unique identifier for the resource being shared
	 * @return [string | boolean] '0' if resource is not installed, version string if available; false if error
	 */
	public static function getResourceCurrentVersion($resource)
	{

		try
		{

			$db = JFactory::getDbo();

			// resource manager may not be installed yet
			$tables = $db->getTableList();
			$tableName = $db->getPrefix() . 'shlib_resources';
			if (!in_array($tableName, $tables))
			{
				// not installed
				return '0';
			}

			// if resource manager is installed and working, find current version of resource
			$query = $db->getQuery(true);
			$query->select($db->quoteName('current_version'))->from(self::RESOURCES_TABLE_NAME)
				->where($db->quoteName('resource') . '=' . $db->quote($resource));
			$db->setQuery($query);
			$currentVersion = $db->loadResult();
			return empty($currentVersion) ? '0' : $currentVersion;
		}
		catch (Exception $e)
		{
			if (class_exists('ShlSystem_Log') && method_exists('ShlSystem_Log', 'error'))
			{
				ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}
			return false;
		}
	}

	/**
	 * Helper to read version number from an extension manifest file
	 *
	 * @param string $file file name, including full path
	 * @return string version string or '0' if not found or otherwise
	 */
	public static function getXmlFileVersion($file)
	{

		$version = '0';
		if (file_exists($file))
		{
			$xml = simplexml_load_file($file);
			if (!empty($xml) && !empty($xml->version))
			{
				$version = (string) $xml->version;
			}
		}

		return $version;
	}

}
