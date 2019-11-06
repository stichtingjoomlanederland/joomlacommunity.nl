<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_EasyDiscussInstallerScript
{
	const EXTENSION = 'com_easydiscuss';
	const EXTENSION_NAME = 'easydiscuss';

	private $path = null;

	/**
	 * Cleanup older css and script files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function cleanup()
	{
		// 1. Cleanup script files
		$path = JPATH_ROOT . '/media/' . self::EXTENSION . '/scripts';
		$sections = array('admin', 'site');

		foreach ($sections as $section) {
			$scriptFolder = $path . '/' . $section;

			$files = JFolder::files($scriptFolder, 'easydiscuss[\.\-].+\.js', true, true);

			foreach ($files as $file) {
				JFile::delete($file);
			}

			// Delete the core.js (Exclusive for EasyDiscuss only)
			$coreFile = $scriptFolder . '/core.js';

			JFile::delete($coreFile);
		}

		// 2. Cleanup css files
		$path = JPATH_ROOT . '/media/' . SELF::EXTENSION . '/themes';
		$files = JFolder::files($path, '.min.css$', true, true);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}
	}

	/**
	 * Loads up the EasyDiscuss library
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/' . self::EXTENSION . '/includes/' . self::EXTENSION_NAME . '.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);
	}

	/**
	 * Determines if the file is an install.mysql.sql file
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isSqlFile($file)
	{
		if (stristr($file, 'install.mysql.sql') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if database is set to mysql or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isMySQL()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		return $dbType == 'mysql' || $dbType == 'mysqli';
	}

	/**
	 * Determines if the file is part of an SQL query
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isQueries($file)
	{
		if (stristr($file, 'administrator/components/' . self::EXTENSION . '/queries') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed script version
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getScriptVersion()
	{
		$this->engine();

		$table = $this->table('Configs');
		$exists = $table->load(array('name' => 'scriptversion'));

		if ($exists) {
			return $table->params;
		}

		return false;
	}

	/**
	 * Retrieves the currently installed database version
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getDatabaseVersion()
	{
		$this->engine();

		$table = $this->table('Configs');
		$exists = $table->load(array('name' => 'dbversion'));

		if ($exists) {
			return $table->params;
		}

		return false;
	}

	/**
	 * Reads the JSON metadata file
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getMeta()
	{
		$file = $this->path . '/meta.json';

		$contents = JFile::read($file);

		$meta = json_decode($contents);

		return $meta;
	}

	/**
	 * Gets the file source path
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getSource($file)
	{
		return $this->path . '/archive/' . $file;
	}

	/**
	 * Gets the file destination path
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getDestination($file)
	{
		return JPATH_ROOT . '/' . $file;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function preflight($action = 'update', $installer)
	{
		// Not used currently.
		// We can perform other pre-flight scripts before the update executes.
	}

	/**
	 * Performs update on the extension
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function update($installer)
	{
		$engine = $this->engine();

		$parent = $installer->getParent();

		$this->version = (string) $installer->manifest->version;
		$this->path = $parent->getPath('source');

		$meta = $this->getMeta();

		$this->cleanup();
		$this->processMeta($meta, $this->path);
		$this->updateVersion('dbversion', $this->version);
		$this->updateVersion('scriptversion', $this->version);

		// Once we have copied the files, we need to sync the ACL to ensure all ACLs are created
		$this->updateACL();
	}

	/**
	 * Updates the database version
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateVersion($type = 'dbversion', $version)
	{
		$config = $this->table('Configs');
		$config->load(array('name' => $type));
		$config->name = $type;
		$config->params = $version;

		// Save the configuration
		$config->store($config->name);
	}

	/**
	 * Process meta file
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processMeta($meta)
	{
		if ($meta->added) {
			$this->processAddedFiles($meta->added);
		}

		if ($meta->modified) {
			$this->processModifiedFiles($meta->modified);
		}

		if ($meta->deleted) {
			$this->processDeletedFiles($meta->deleted);
		}

		if ($meta->renamed) {
			$this->processRenamedFiles($meta->renamed);
		}

		// Process maintenance scripts
		if ($meta->maintenance) {
			$this->processMaintenanceFiles($meta->maintenance);
		}

		// Process precompiled files
		$this->processPrecompiledFiles();
	}

	/**
	 * Process added files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processAddedFiles($files)
	{
		return $this->processModifiedFiles($files, true);
	}

	/**
	 * Process deleted files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processDeletedFiles($files)
	{
		foreach ($files as $file) {
			$exists = JFile::exists($file);

			if ($exists) {
				JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * Process modified files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processModifiedFiles($files, $isNew = false)
	{
		foreach ($files as $file) {
			$source = $this->getSource($file);
			$dest = $this->getDestination($file);

			// Detect for install.mysql.sql
			if ($this->isSqlFile($file)) {
				JFile::copy($source, $dest);

				$this->runQueries($dest);

				continue;
			}

			// For query files, we need to execute them
			if ($this->isQueries($file)) {

				$folder = dirname($dest);

				if (!JFolder::exists($folder)) {
					JFolder::create($folder);
				}

				JFile::copy($source, $dest);

				$this->runQueries($dest);

				continue;
			}

			$folder = dirname($dest);

			if (!$isNew) {
				if (JFile::exists($dest)) {
					JFile::copy($source, $dest);
				}

				// for modified file, we stop here.
				continue;
			}

			// new files.
			if (!JFolder::exists($folder)) {
				JFolder::create($folder);
			}
			JFile::copy($source, $dest);
		}

		return true;
	}

	/**
	 * Process maintenance files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processMaintenanceFiles($files)
	{
		// Get current DB version
		$version = $this->getDatabaseVersion();
		$updates = array();
		$db = ED::db();

		// Arrays to store all table data
		$tables = array();
		$indexes = array();
		$changes = array();

		$phpFiles = array();

		// Figure out which version script needs to be executed
		// Step 1: lets process json files first.
		foreach ($files as $file) {
			$fileName = basename($file);
			$fileVersion = basename(dirname($file));
			$filePath = $this->path . '/archive/' . $file;

			if (! JFile::exists($filePath)) {
				continue;
			}

			// Determines if the current installed version is lower than the file version
			$outdated = version_compare($version, $fileVersion) === -1;

			if (!$outdated) {
				// continue;
			}

			// Process JSON files
			if (stristr($fileName, '.json') !== false) {
				$this->processJSON($filePath);
			}
		}

		// Step 2: now we process php maintenane scripts.
		foreach ($files as $file) {
			$fileName = basename($file);
			$fileVersion = basename(dirname($file));
			$filePath = $this->path . '/archive/' . $file;

			// Determines if the current installed version is lower than the file version
			$outdated = version_compare($version, $fileVersion) === -1;

			if (!$outdated) {
				// continue;
			}

			// Process PHP files
			if (stristr($fileName, '.php') !== false) {

				// Get folder version
				$folderVersion = basename(dirname($file));
				$updatesFolder = JPATH_ADMINISTRATOR . '/components/' . self::EXTENSION . '/updates/' . $folderVersion;
				$exists = JFolder::exists($updatesFolder);

				if (!$exists) {
					JFolder::create($updatesFolder);
				}

				// Copy the php files into the respective location
				$dest = $updatesFolder . '/' . $fileName;

				if (!JFile::exists($dest)) {
					JFile::copy($filePath, $dest);
				}

				$maintenance = ED::maintenance();
				$maintenance->runScript($dest);
			}
		}
	}

	/**
	 * Process precompiled css and script files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processPrecompiledFiles()
	{
		// 1. Copy admin script files
		$scripts = $this->path . '/archive/precompiled/scripts/admin';
		$files = JFolder::files($scripts, '.', false, true);

		foreach ($files as $script) {
			$dest = JPATH_ROOT . '/media/' . SELF::EXTENSION . '/scripts/admin/' . basename($script);

			JFile::copy($script, $dest);
		}

		// 2. Copy site script files
		$scripts = $this->path . '/archive/precompiled/scripts/site';
		$files = JFolder::files($scripts, '.', false, true);

		foreach ($files as $script) {
			$dest = JPATH_ROOT . '/media/' . SELF::EXTENSION . '/scripts/site/' . basename($script);

			JFile::copy($script, $dest);
		}

		// 3. Copy css files
		$themesFolder = $this->path . '/archive/precompiled/stylesheets';
		$folders = JFolder::folders($themesFolder);

		foreach ($folders as $folder) {
			$target = JPATH_ROOT . '/media/' . self::EXTENSION . '/themes/' . $folder . '/css';
			$source = $themesFolder . '/' . $folder;

			$files = JFolder::files($source);

			if ($files) {
				foreach ($files as $file) {
					$sourceFile = $source . '/' . $file;
					$targetFile = $target . '/' . $file;

					JFile::copy($sourceFile, $targetFile);
				}
			}
		}
	}

	/**
	 * Process JSON files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processJSON($filePath)
	{
		$db = ED::db();
		$contents = JFile::read($filePath);
		$result = json_decode($contents);

		// Default values
		$columnExists = true;
		$indexExists = true;
		$alterTable = false;

		foreach ($result as $row) {

			// New column added
			if (isset($row->column)) {

				// Store the list of tables that needs to be queried
				if (!isset($tables[$row->table])) {
					$tables[$row->table] = $db->getTableColumns($row->table);
				}

				// Check if the column is in the fields or not
				$columnExists = in_array($row->column, $tables[$row->table]);
			}

			// Alter table
			if (isset($row->alter)) {
				$alterTable = true;
			}

			// Index column
			if (isset($row->index)) {
				if (!isset($indexes[$row->table])) {
					$indexes[$row->table] = $db->getTableIndexes($row->table);
				}

				$indexExists = in_array($row->index, $indexes[$row->table]);
			}

			if ($alterTable || !$columnExists || !$indexExists) {
				$db->setQuery($row->query);
				$db->Query();

				if (!$columnExists) {
					$tables[$row->table][] = $row->column;
				}

				if (!$indexExists) {
					$indexes[$row->table][] = $row->index;
				}

				if ($alterTable) {
					$changes[$row->table][] = $row->alter;
				}
			}
		}
	}

	/**
	 * Process renamed files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processRenamedFiles($files)
	{
		foreach ($files as $file) {
			$source = $this->getSource($file->old);
			$dest = $this->getDestination($file->new);

			// Check if the source already removed
			$sourceExists = JFile::exists($source);

			if (!$sourceExists) {
				continue;
			}

			// If old file exists, move it
			JFile::move($source, $dest);
		}

		return true;
	}

	/**
	 * Runs SQL queries based on the files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function runQueries($file)
	{
		$this->engine();

		$db = ED::db();

		// Get the contents of the file
		$contents = JFile::read($file);
		$queries = JInstallerHelper::splitSql($contents);

		foreach ($queries as $query) {
			$query = trim($query);

			if (!empty($query)) {
				$db->setQuery($query);
				$state = $db->execute();
			}
		}
	}

	/**
	 * Retrieve the table for the extension
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function table($name)
	{
		$table = ED::table($name);

		return $table;
	}


	/**
	 * Update the ACL
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateACL()
	{
		$this->engine();

		$db = ED::db();

		// Intelligent fix to delete all records from the #__discuss_acl_group when it contains ridiculous amount of entries
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_acl_group');
		$db->setQuery($query);

		$total = $db->loadResult();

		if ($total > 20000) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl_group');
			$db->setQuery($query);
			$db->Query();
		}

		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl');
		$db->setQuery($query);
		$db->query();

		// Get the list of acl
		$contents = JFile::read(DISCUSS_ADMIN_ROOT . '/defaults/acl.json');
		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl') . '(' . $db->nameQuote('id') . ',' . $db->nameQuote('action') . ',' . $db->nameQuote('group') . ',' . $db->nameQuote('description') . ',' . $db->nameQuote('public') . ',' . $db->nameQuote('default') . ',' . $db->nameQuote('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->public) . ',' . $db->Quote($acl->default) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();

		return true;
	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function assignACL()
	{
		$this->engine();

		// Get the db
		$db = ED::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->nameQuote('id') . ', a.' . $db->nameQuote('title') . ' AS ' . $db->nameQuote('name') . ', COUNT(DISTINCT b.' . $db->nameQuote('id') . ') AS ' . $db->nameQuote('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->nameQuote('id') . ' SEPARATOR \',\') AS ' . $db->nameQuote('parents');
		$query[] = 'FROM ' . $db->nameQuote('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote('lft') . ' > b.'  . $db->nameQuote('lft');
		$query[] = 'AND a.' . $db->nameQuote('rgt') . ' < b.' . $db->nameQuote('rgt');
		$query[] = 'GROUP BY a.' . $db->nameQuote('id');
		$query[] = 'ORDER BY a.' . $db->nameQuote('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__discuss_acl');
		$query[] = 'ORDER BY ' . $db->nameQuote('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {
					$groups[$id] = $installedAcls;
				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(1, 2, 3, 4);
					} else {
						// other groups
						$allowedAcl = array(1, 2, 3, 4, 25, 26, 30);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_acl_group');
			$query[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl_group') . ' (' . $db->nameQuote('content_id') . ',' . $db->nameQuote('acl_id') . ',' . $db->nameQuote('status') . ',' . $db->nameQuote('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->nameQuote('acl_id') . ' FROM ' . $db->nameQuote('#__discuss_acl_group');
				$sub[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($existingGroupAcl, $installedAcls);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}

		return true;
	}
}
