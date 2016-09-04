<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.580
 * @date                2016-08-25
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Installation/Uninstallation script
 *
 */
class plgSystemShlibInstallerScript
{

	const MIN_JOOMLA_VERSION = '2.5.6';
	const MAX_JOOMLA_VERSION = '4';

	public function install($parent)
	{
		if (function_exists('apc_clear_cache'))
		{
			apc_clear_cache();
		}
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}

		// create a db table to register extensions using shlib
		// will allow those extensions to decide whether
		// to remove or not shlib when they are uninstalled themselves
		// id, extension, min_version, max_version, refuse_versions, accept_versions
		// n, com_sh404sef, [0|1.2], [0|3.4]
		// if an extension wants to install a new version of shlib, it must first check that
		// existing extensions can accept the new one
		// When uninstalling, the extension must unregister itself from the db
		// then decide whether to uninstall shlib (only if no other extension is using it)
		$this->_updateDbStructure();

		$this->_removeOldAssets();

		$this->_hacks();
	}

	public function uninstall($parent)
	{
		$db = JFactory::getDbo();
		$db->dropTable('#__shlib_consumers');
		$db->dropTable('#__shlib_resources');
		$db->dropTable('#__wblib_messages');
		$db->dropTable('#__wblib_keystore');
	}

	public function update($parent)
	{
		// create registration table, if it was not done upon initial install
		$this->_updateDbStructure();

		$this->_removeOldAssets();

		$this->_hacks();
	}

	public function preflight($route, $installer)
	{

		if ($route == 'install' || $route == 'update')
		{
			// check Joomla! version
			if (version_compare(JVERSION, self::MIN_JOOMLA_VERSION, '<') || version_compare(JVERSION, self::MAX_JOOMLA_VERSION, 'ge'))
			{
				JFactory::getApplication()->enqueueMessage(sprintf('shLib requires Joomla! version between %s and %s (you are using %s). Aborting installation', self::MIN_JOOMLA_VERSION, self::MAX_JOOMLA_VERSION, JVERSION));
				return false;
			}
		}
	}

	public function postflight($type, $parent)
	{
	}

	/**
	 * Remove assets from previous versions, stored in their versioned folders
	 * Only assets in folder listed in version.json file should be kept
	 */
	protected function _removeOldAssets()
	{
		$types = array('css', 'js');
		foreach ($types as $type)
		{
			$jsonFile = JPATH_ROOT . '/media/plg_shlib/dist/' . $type . '/version.json';
			if (file_exists($jsonFile))
			{
				$rawJson = file_get_contents($jsonFile);
				$decoded = json_decode($rawJson, true);
				$currentVersion = empty($decoded) ? '' : $decoded['currentVersion'];
				if (!empty($currentVersion))
				{
					jimport('joomla.filesystem.folder');
					$folders = JFolder::folders(JPATH_ROOT . '/media/plg_shlib/dist/' . $type, '^[0-9]{8,25}$');
					foreach ($folders as $folder)
					{
						if ($folder != $currentVersion)
						{
							JFolder::delete(JPATH_ROOT . '/media/plg_shlib/dist/' . $type . '/' . $folder);
						}
					}
				}
			}
		}
	}

	/**
	 * Things that don't fit elsewhere
	 */
	protected function _hacks()
	{

		// Josetta registration
		// First versions of Josetta used shLib, but a version prior
		// to the addition of the resource manager. Therefore when
		// installing an extension (sh404sef) using shLib on a site
		// running an old version of Josetta (pre-1.3.0), Josetta
		// does not register itself. Thus uninstalling sh404sef, in that example,
		// may result in uninstalling also shLIb, and breaking Josetta
		// so if Josetta is installed on that site, we make sure
		// there's an entry for it in the registration table
		$path = JPATH_ROOT . '/administrator/components/com_josetta/index.html';
		jimport('joomla.filesystem.file');
		if (JFile::exists($path))
		{
			try
			{
				// do we have a record?
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select('*')->from('#__shlib_consumers');
				$query->where($db->quoteName('resource') . '=' . $db->quote('shlib'));
				$query->where($db->quoteName('context') . '=' . $db->quote('com_josetta'));
				$existingRecord = $db->setQuery($query)->loadObject();

				// if not, create one
				if (empty($existingRecord))
				{
					$query->clear();
					$query->insert('#__shlib_consumers');
					$query->set($db->quoteName('resource') . '=' . $db->quote('shlib'));
					$query->set($db->quoteName('context') . '=' . $db->quote('com_josetta'));
					$db->setQuery($query)->execute();
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
		}
	}

	/**
	 * Create database table needed to register/unregister
	 * resource shared by several extensions
	 *
	 * @throws Exception
	 */
	protected function _updateDbStructure()
	{
		$queries = array();

		// shared resource manager
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__shlib_consumers` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource` VARCHAR(50) NOT NULL DEFAULT '',
    `context` VARCHAR(50) NOT NULL DEFAULT '',
    `min_version` VARCHAR(20) NOT NULL DEFAULT '0',
    `max_version` VARCHAR(20) NOT NULL DEFAULT '0',
    `refuse_versions` VARCHAR(255) NOT NULL DEFAULT '',
    `accept_versions` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_context` (`context`)
    ) DEFAULT CHARSET=utf8;";

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__shlib_resources` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource` VARCHAR(50) NOT NULL DEFAULT '',
    `current_version` VARCHAR(20) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_resource` (`resource`)
    ) DEFAULT CHARSET=utf8;";

		// message and notifications manager
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__wblib_messages` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `scope` VARCHAR(50) NOT NULL DEFAULT '',
    `type` VARCHAR(50) NOT NULL DEFAULT '',
    `sub_type` VARCHAR(150) NOT NULL DEFAULT '',
    `display_type` TINYINT(3) NOT NULL DEFAULT 0,
    `uid` VARCHAR(50) NOT NULL DEFAULT '',
    `title` VARCHAR(512) NOT NULL DEFAULT '',
    `body` VARCHAR(2048) NOT NULL DEFAULT '',
    `action` TINYINT(3) NOT NULL DEFAULT 0,
    `created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`acked_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hide_after` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`hide_until` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `scope` (`scope`),
    KEY `type_index` (`type`, `sub_type`),
    KEY `acked_on` (`acked_on`)
    ) DEFAULT CHARSET=utf8;";

		// generic key store
		$queries[] = "CREATE TABLE IF NOT EXISTS `#__wblib_keystore` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`scope` VARCHAR(50) NOT NULL DEFAULT 'default',
			`key` VARCHAR(255) NOT NULL DEFAULT '',
			`value` MEDIUMTEXT NOT NULL,
			`user_id` INT NOT NULL DEFAULT 0,
			`format` TINYINT(3) NOT NULL DEFAULT 1,
			`modified_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`id`),
			KEY `main` (`scope`,`key`)
			) ENGINE = InnoDB DEFAULT CHARSET=utf8;";

		// make sure have 512 chars for message titles
		$db = JFactory::getDbo();
		$queries[] = 'alter table' . $db->qn('#__wblib_messages') . ' modify ' . $db->qn('title') . ' VARCHAR(512);';

		// run query
		$this->_runQueries($queries);
	}

	private function _runQueries($queries)
	{
		if (empty($queries))
		{
			return;
		}

		try
		{
			$db = JFactory::getDBO();
			foreach ($queries as $query)
			{
				$db->setQuery($query);
				$db->query();
				$error = $db->getErrorNum();
				if (!empty($error))
				{
					throw new Exception($db->getErrorMsg());
				}
			}
		}
		catch (Exception $e)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(
				'Error while creating/upgrading the database : ' . $e->getMessage()
				. '.<br />shLib will probably not operate properly. Please uninstall it, then try again after checking your database server setup. Contact us in case this happens again.'
			);
		}
	}

	private function _runAlterQueries($alterQueries)
	{
		if (empty($alterQueries))
		{
			return;
		}
		$db = JFactory::getDBO();
		$updatedQueries = array();
		foreach ($alterQueries as $table => $queries)
		{
			// aggregate sub-queries
			$queries = implode(', ', $queries);

			// prepend query
			$updatedQueries[] = 'ALTER TABLE ' . $db->qn($table) . ' ' . $queries;

			// run query
			$this->_runQueries($updatedQueries);
		}
	}
}
