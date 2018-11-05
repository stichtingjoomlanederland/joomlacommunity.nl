<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Asset;
use Joomla\CMS\Table\Category;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

// No direct access.
defined('_JEXEC') or die;

/**
 * PWT ACL Diagnostics Model
 *
 * @since   3.0
 */
class PwtaclModelDiagnostics extends ListModel
{
	/**
	 * Asset Root ID.
	 *
	 * @var    integer
	 * @since  3.0
	 */
	private $rootId = 1;

	/**
	 * All changes made by the diagnostics tool
	 *
	 * @var    array
	 * @since  3.0
	 */
	protected $changes = array();

	/**
	 * Constructor
	 *
	 * @param   array $config Optional parameters.
	 *
	 * @since   3.0
	 */
	public function __construct(array $config = array())
	{
		Log::addLogger(array('text_file' => 'com_pwtacl.diagnostics.php'), Log::ALL, array('com_pwtacl'));
		parent::__construct($config);
	}

	/**
	 * Get a list of the diagnostics checks steps
	 *
	 * @return  array Diagnostics Steps
	 * @since   3.0
	 */
	public function getDiagnosticsSteps()
	{
		return array(
			1  => 'CLEANUP',
			2  => 'LEGACY',
			3  => 'ROOT',
			4  => 'GENERAL_COMPONENTS',
			5  => 'GENERAL_CATEGORIES',
			6  => 'GENERAL_ARTICLES',
			7  => 'GENERAL_UCMCONTENT',
			8  => 'GENERAL_MODULES',
			9  => 'GENERAL_MENUS',
			10 => 'GENERAL_FIELDGROUPS',
			11 => 'GENERAL_FIELDS',
			12 => 'GENERAL_LANGUAGES',
			13 => 'ADMINCONFLICTS',
			14 => 'REBUILD'
		);
	}

	/**
	 * Performs a quick scan to check for Asset Issues.
	 *
	 * @param   boolean $force Force quickscan without cache
	 *
	 * @return  boolean
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getQuickScan($force = false)
	{
		// Get variables needed
		$params = ComponentHelper::getParams('com_pwtacl');
		$now    = time();
		$last   = (int) $params->get('diagnosticslastrun', 0);
		$issues = $params->get('diagnosticsissues', false);

		// Run quickscan only if last quickscan was over 6 hours ago
		if (!$force && abs($now - $last) < 21600)
		{
			return $issues;
		}

		// Log quickscan
		Log::add(
			'--- QUICKSCAN STARTED ---',
			Log::INFO,
			'com_pwtacl'
		);

		// Start timer
		$startTime = microtime(true);

		// Cleanup assets first
		$issues = $this->cleanUp(
			false
		);

		// Check for any legacy changes
		if (!$issues)
		{
			$issues = $this->checkLegacy(
				false
			);
		}

		// Check if root asset is correct
		if (!$issues)
		{
			$issues = $this->checkRoot(
				false
			);
		}

		// Check components
		if (!$issues)
		{
			$issues = $this->checkComponents(
				false
			);
		}

		// Check [component].category.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'categories',
				'com_categories',
				'category',
				'CONCAT(categories.extension, ".category.", categories.parent_id)'
			);
		}

		// Check com_content.article.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'content',
				'com_content',
				'article',
				'CONCAT("com_content.category.", content.catid)'
			);
		}

		// Check #__ucm_content.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'ucm_content',
				'ucm_content',
				'',
				'"root.1"'
			);
		}

		// Check com_modules.module.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'modules',
				'com_modules',
				'module',
				'"com_modules"'
			);
		}

		// Check com_menus.menu.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'menu_types',
				'com_menus',
				'menu',
				'"com_menus"'
			);
		}

		// Check [component].fieldgroup.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'fields_groups',
				'com_fields',
				'fieldgroup',
				'SUBSTRING_INDEX(fields_groups.context, ".", 1)'
			);
		}

		// Check [component].field.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'fields',
				'com_fields',
				'field',
				'SUBSTRING_INDEX(fields.context, ".", 1)'
			);
		}

		// Check com_languages.language.[id]
		if (!$issues)
		{
			$issues = $this->checkAssets(
				false,
				'languages',
				'com_languages',
				'language',
				'"com_languages"'
			);
		}

		// Fix admin access conflict
		if (!$issues)
		{
			$issues = $this->fixAdminConflicts(
				false
			);
		}

		// End timer and log time needed
		$endTime       = microtime(true);
		$timeToRebuild = sprintf('%0.2f', $endTime - $startTime);

		Log::add(
			'Quickscan took ' . $timeToRebuild . ' seconds and found ' . (($issues) ? '' : 'no ') . 'issues',
			Log::INFO,
			'com_pwtacl'
		);

		Log::add('---',
			Log::INFO,
			'com_pwtacl'
		);

		// Set last run data in params
		$params->set('diagnosticslastrun', $now);
		$params->set('diagnosticsissues', $issues ? true : false);

		// Update extension params
		$this->storeParams($params);

		return $issues ? true : false;
	}

	/**
	 * Run the diagnostics tool to check the #__assets table
	 *
	 * @param   integer $step Step to run
	 *
	 * @return  array Asset changes
	 * @since   3.0
	 * @throws  Exception
	 */
	public function runDiagnostics($step, $cli = false)
	{
		// Log changes made
		if ($step == 1)
		{
			if ($cli)
			{
				Log::add(
					'--- CHECK STARTED, via CLI ---',
					Log::INFO,
					'com_pwtacl'
				);
			}
			else
			{
				Log::add(
					'--- CHECK STARTED ---',
					Log::INFO,
					'com_pwtacl'
				);
			}
		}

		switch ($step)
		{
			case 1:
				// Cleanup assets first
				$this->cleanUp(
					true
				);
				break;

			case 2:
				// Check for any legacy changes
				$this->checkLegacy(
					true
				);
				break;

			case 3:
				// Check if root asset is correct
				$this->checkRoot(
					true
				);
				break;

			case 4:
				// Check components
				$this->checkComponents(
					true
				);
				break;

			case 5:
				// Check [component].category.[id]
				$this->checkAssets(
					true,
					'categories',
					'com_categories',
					'category',
					'CONCAT(categories.extension, ".category.", categories.parent_id)'
				);
				break;

			case 6:
				// Check com_content.article.[id]
				$this->checkAssets(
					true,
					'content',
					'com_content',
					'article',
					'CONCAT("com_content.category.", content.catid)'
				);
				break;

			case 7:
				// Check #__ucm_content.[id]
				$this->checkAssets(
					true,
					'ucm_content',
					'ucm_content',
					'',
					'"root.1"'
				);
				break;

			case 8:
				// Check com_modules.module.[id]
				$this->checkAssets(
					true,
					'modules',
					'com_modules',
					'module',
					'"com_modules"'
				);
				break;

			case 9:
				// Check com_menus.menu.[id]
				$this->checkAssets(
					true,
					'menu_types',
					'com_menus',
					'menu',
					'"com_menus"'
				);
				break;

			case 10:
				// Check [component].fieldgroup.[id]
				$this->checkAssets(
					true,
					'fields_groups',
					'com_fields',
					'fieldgroup',
					'SUBSTRING_INDEX(fields_groups.context, ".", 1)'
				);
				break;

			case 11:
				// Check [component].field.[id]
				$this->checkAssets(
					true,
					'fields',
					'com_fields',
					'field',
					'SUBSTRING_INDEX(fields.context, ".", 1)'
				);
				break;

			case 12:
				// Check com_menus.menu.[id]
				$this->checkAssets(
					true,
					'languages',
					'com_languages',
					'language',
					'"com_languages"'
				);
				break;

			case 13:
				// Fix admin access conflict
				$this->fixAdminConflicts(
					true
				);
				break;

			case 14:
				// Always end with rebuild for lft & rgt values
				$this->rebuildAssetsTable();
				break;
		}

		// Log changes made
		$changelog = 'Step ' . $step . ' total: ' . (isset($this->changes['total']) ? $this->changes['total'] : 0) . ' changes';

		if (isset($this->changes['items']))
		{
			foreach ($this->changes['items'] as $action => $types)
			{
				foreach ($types as $type => $items)
				{
					$changelog .= ' / [' . $action . '][' . $type . ']: ' . count($items);
				}
			}
		}

		Log::add(
			$changelog,
			Log::INFO,
			'com_pwtacl'
		);

		// Add seperator to log
		Log::add(
			'---',
			Log::INFO,
			'com_pwtacl'
		);

		// Set last run data in params
		$params = ComponentHelper::getParams('com_pwtacl');
		$params->set('diagnosticslastrun', null);
		$params->set('diagnosticsissues', false);

		// Update extension params
		$this->storeParams($params);

		return $this->changes;
	}

	/**
	 * Method to cleanup the assets table.
	 *
	 * @param   boolean $fix Fix issues
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function cleanUp($fix = false)
	{
		$db = $this->getDbo();

		// Get assets with name starting with #__
		/** @var PwtaclModelAssets $assetsModel */
		$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtaclModel');
		$assets      = $assetsModel->getAssets(
			'(' . $db->quoteName('name') . ' LIKE CONCAT("#", "__%") AND ' . $db->quoteName('name') . ' NOT LIKE "%__ucm_content.%")'
		);

		// Delete assets
		if ($fix)
		{
			$this->deleteAssets($assets);
		}

		return count($assets);
	}

	/**
	 * Method to check for legacy issues and fix them.
	 *
	 * @param   boolean $fix Fix issues
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function checkLegacy($fix = false)
	{
		// Check for old com_contact_details category type
		$db               = $this->getDbo();
		$query            = $db->getQuery(true);
		$incorrectObjects = array();

		$query
			->select(
				array(
					'id',
					'title',
					'"com_contact" AS extension',
					'extension AS old_extension'
				)
			)
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = "com_contact_details"');

		try
		{
			$incorrectObjects = $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve incorrect articles with: ' . $e->getMessage());
		}

		if ($fix)
		{
			$this->fixObjects('categories', $incorrectObjects);
		}

		// Get assets with rules set
		/** @var PwtaclModelAssets $assetsModel */
		$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtaclModel');
		$assets      = $assetsModel->getAssets($db->quoteName('rules') . ' != ' . $db->quote('{}'));

		// Check if rules need cleanup
		foreach ($assets as $id => $asset)
		{
			// Track old rules
			$asset->old_rules = $asset->rules;

			// Convert to array
			$oldAssetrules = json_decode($asset->rules);

			// Get actions for asset
			/** @var PwtaclModelAssets $assetsModel */
			$assetsModel = BaseDatabaseModel::getInstance('Assets', 'PwtaclModel');
			$actions     = $assetsModel->getActions($asset->name, true);

			// Filter out actions not available for asset
			if ($oldAssetrules)
			{
				foreach ($oldAssetrules as $action => $settings)
				{
					// Only remove if no actions are set, mainly for extensions abusing the Joomla core...
					if (!in_array($action, $actions->items) && (count((array) $settings) === 0))
					{
						unset($oldAssetrules->{$action});
					}
				}
			}

			// Filter actions without settings
			$asset->rules = json_encode((object) array_filter((array) $oldAssetrules));

			// Unset action if rules are not changed
			if ($asset->rules == $asset->old_rules)
			{
				unset($assets[$id]);
			}

			unset($asset->component);
			unset($asset->objectid);
			unset($asset->additional);
			unset($asset->type);
		}

		// Update assets
		if ($fix)
		{
			$this->fixObjects('assets', $assets);
		}

		return count($incorrectObjects) + count($assets);
	}

	/**
	 * Method to check the root asset.
	 *
	 * @param   boolean $fix Fix issues
	 *
	 * @return  integer
	 * @since   3.0
	 */
	public function checkRoot($fix = false)
	{
		$wrongAssets = array();
		$assets      = array();

		// Asset Table
		/** @var Asset $asset */
		$asset = Table::getInstance('Asset');
		$asset->loadByName('root.1');

		// Get root ID
		if ($asset)
		{
			$this->rootId = (int) $asset->id;
		}

		// See if we can find the root Asset
		if (!$asset->id)
		{
			$this->rootId = $asset->getRootId();
			$asset->load($this->rootId);
		}

		// Check the root Asset if exists
		if ($this->rootId)
		{
			$wrongAssets = $this->getWrongRoot($this->rootId);

			if ($fix)
			{
				$this->fixObjects('assets', $wrongAssets);
			}
		}

		// Add the root Asset if missing
		if ($this->rootId === false)
		{
			$assets[] = array(
				'parent_id' => 0,
				'lft'       => 0,
				'level'     => 0,
				'name'      => 'root.1',
				'title'     => 'Root Asset',
				'rules'     => '{"core.login.site":{"6":1,"2":1},"core.login.admin":{"6":1},"core.admin":{"8":1},"core.manage":{"7":1},
					"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},
					"core.edit.own":{"6":1,"3":1}}'
			);

			if ($fix)
			{
				$this->addAssets($assets);
			}

			$asset->loadByName('root.1');
			$this->rootId = (int) $asset->id;
		}

		return count($wrongAssets) + count($assets);
	}

	/**
	 * Method to add, remove and check components.
	 *
	 * @param   boolean $fix Fix issues
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function checkComponents($fix = false)
	{
		// Step 1: Remove orphan assets
		$orphanComponents = $this->getOrphanAssets('extensions');

		if ($fix)
		{
			$this->deleteAssets($orphanComponents);
		}

		// Step 2: Add missing assets
		$missingComponents = $this->getMissingComponents();

		if ($fix)
		{
			$this->addAssets($missingComponents);
		}

		// Step 3: Check assets
		$wrongAssets = $this->getWrongComponents();

		if ($fix)
		{
			$this->fixObjects('assets', $wrongAssets);
		}

		return count($orphanComponents) + count($missingComponents) + count($wrongAssets);
	}

	/**
	 * Method to add, remove and check assets.
	 *
	 * @param   boolean $fix        Fix issues
	 * @param   string  $table      Name of the table.
	 * @param   string  $extension  Name of the extension.
	 * @param   string  $view       Name of the view.
	 * @param   string  $parentjoin Parent join value.
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function checkAssets($fix = false, $table = '', $extension = '', $view = '', $parentjoin = '')
	{
		// Check the categories table
		if ($table == 'categories')
		{
			$this->fixCategoriesRoot();
			$this->fixCategories();

			// Rebuild categories first
			/** @var Category $category */
			$category = Table::getInstance('Category');
			$category->rebuild();
		}

		// Check the content table
		if ($table == 'content')
		{
			$this->fixContent();
		}

		// Step 1: Remove orphan assets
		$orphanAssets = $this->getOrphanAssets($table, $extension, $view);

		if ($fix)
		{
			$this->deleteAssets($orphanAssets);
		}

		// Step 2: Add missing assets
		$missingAssets = $this->getMissingAssets($table, $extension, $view, $parentjoin);

		if ($fix)
		{
			$this->addAssets($missingAssets);
		}

		// Step 3: Check related objects for incorrect asset_id
		$incorrectAssetIds = $this->getIncorrectAssetIds($table, $extension, $view);

		if ($fix)
		{
			$this->fixObjects($table, $incorrectAssetIds);
		}

		// Step 4: Check assets for correct values
		$wrongAssets = $this->getWrongAssets($table, $extension, $view, $parentjoin);

		if ($fix)
		{
			$this->fixObjects('assets', $wrongAssets);
		}

		return count($orphanAssets) + count($missingAssets) + count($incorrectAssetIds) + count($wrongAssets);
	}

	/**
	 * Delete assets from the #__assets table and log removals.
	 *
	 * @param   array $assets Array with the assets to remove.
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	protected function deleteAssets($assets)
	{
		// Only proceed if we have assets
		if (!$assets)
		{
			return;
		}

		// Get ID's to delete
		foreach ($assets as $asset)
		{
			// Log changes
			$this->log('delete', 'assets', $asset);
		}

		// Delete
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->delete($db->quoteName('#__assets'))
			->where($db->quoteName('id') . ' IN (' . implode(',', array_keys($assets)) . ')');

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		return;
	}

	/**
	 * Add assets to the #__assets table and log additions.
	 *
	 * @param   array $assets Array with the assets to add.
	 *
	 * @return  void
	 * @since   3.0
	 */
	protected function addAssets($assets)
	{
		// Only proceed if we have assets
		if (!$assets)
		{
			return;
		}

		foreach ($assets as $asset)
		{
			$this->getDbo()->insertObject('#__assets', $asset);
			$assetId   = (int) $this->getDbo()->insertid();
			$asset->id = $assetId;

			// Log changes
			$this->log('add', 'assets', $asset);
		}

		return;
	}

	/**
	 * Fixes the objects and log the changes.
	 *
	 * @param   string $table   The name of the table.
	 * @param   array  $objects Array with the objects to fix.
	 *
	 * @return  void
	 * @since   3.0
	 */
	protected function fixObjects($table, $objects)
	{
		// Only proceed if we have assets
		if (!$objects)
		{
			return;
		}

		switch ($table)
		{
			case 'ucm_content':
				$key = 'core_content_id';
				break;

			case 'languages':
				$key = 'lang_id';
				break;

			default:
				$key = 'id';
				break;
		}

		// Fix the assets
		foreach ($objects as $object)
		{
			$changes  = array();
			$tempid   = '';
			$ucmtitle = '';

			if ($key !== 'id')
			{
				$tempid         = $object->{'id'};
				$object->{$key} = $object->{'id'};
				unset($object->{'id'});
			}

			if ($table == 'ucm_content')
			{
				$ucmtitle = $object->{'title'};
				unset($object->{'title'});
			}

			// Check for each asset field
			foreach ($object as $field => $value)
			{
				// Track category changes
				if (isset($object->{'old_' . $field}) && ($object->{$field} != $object->{'old_' . $field}))
				{
					$changes[$field]['old'] = $object->{'old_' . $field};
					$changes[$field]['new'] = $object->{$field};
				}

				// Unset old field
				unset($object->{'old_' . $field});
			}

			// Store asset changes
			$this->getDbo()->updateObject('#__' . $table, $object, array($key));

			if ($key !== 'id')
			{
				$object->{'id'} = $tempid;
			}

			if ($table == 'ucm_content')
			{
				$object->{'title'} = $ucmtitle;
			}

			// Log changes
			$this->log('fix', $table, $object, $changes);
		}
	}

	/**
	 * Records which are present in the #__assets table, but not in the related table.
	 *
	 * @param   string $table     Name of the table.
	 * @param   string $extension Name of the extension.
	 * @param   string $view      Name of the view.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getOrphanAssets($table = '', $extension = '', $view = '')
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		switch ($table)
		{
			case 'extensions':
				$id = $table . '.element';
				break;

			case 'ucm_content':
				$id = $table . '.core_content_id';
				break;

			case 'languages':
				$id = $table . '.lang_id';
				break;

			default:
				$id = $table . '.id';
				break;
		}

		$query
			->select(
				array(
					'assets.id AS id',
					'assets.parent_id AS parent_id',
					'assets.level AS level',
					'assets.name AS name',
					'assets.title AS title',
					'assets.rules AS rules'
				)
			)
			->from($db->quoteName('#__assets', 'assets'))
			->leftJoin($db->quoteName('#__' . $table, $table) . ' ON ' . $id . ' = SUBSTRING_INDEX(assets.name, ".", -1)')
			->where($db->quoteName($id) . ' IS NULL');

		switch ($table)
		{
			case 'extensions':
				$query
					->where($db->quoteName('assets.name') . ' NOT LIKE  ' . $db->quote('%.%'))
					->where($db->quoteName('assets.name') . ' NOT IN ("' . $this->getComponents(false, 'categories') . '")');
				break;

			case 'categories':
			case 'fields_groups':
			case 'fields':
				$query
					->where($this->getComponents(true, $table, $view));
				break;

			case 'ucm_content':
				$query
					->where($db->quoteName('assets.name') . ' LIKE "#__ucm_content.%"');
				break;

			default:
				$query
					->where($db->quoteName('assets.name') . ' LIKE "' . $extension . '.' . $view . '.%"');
				break;
		}

		try
		{
			return $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve orphaned assets with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Records which are present in the #__extensions table, but not in the #__assets table.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getMissingComponents()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					$this->rootId . ' AS parent_id',
					'"1" AS level',
					'ext.element AS name',
					'ext.element AS title',
					'"{}" AS rules'
				)
			)
			->from($db->quoteName('#__extensions', 'ext'))
			->leftJoin($db->quoteName('#__assets', 'assets') . ' ON assets.name = ext.element')
			->where($db->quoteName('assets.name') . ' IS NULL')
			->where($db->quoteName('ext.type') . ' = ' . $db->quote('component'))
			->order($db->quoteName('ext.element') . ' ASC');

		try
		{
			return $db->setQuery($query)->loadObjectList('name');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve missing components with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Records which are present in the related table, but not in the #__assets table.
	 *
	 * @param   string $table      Name of the table.
	 * @param   string $extension  Name of the extension.
	 * @param   string $view       Name of the view.
	 * @param   string $parentjoin Parent join value.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getMissingAssets($table = '', $extension = '', $view = '', $parentjoin = '')
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$parent_id = 'parentassets.id';
		$title     = $table . '.title';

		switch ($table)
		{
			case 'categories':
				$name       = 'CONCAT(' . $table . '.extension, ".category.", ' . $table . '.id)';
				$level      = '(' . $table . '.level + "1")';
				$parentjoin = 'categories.extension';
				break;

			case 'fields_groups':
				$name  = 'CONCAT(SUBSTRING_INDEX(' . $table . '.context, ".", 1), ".' . $view . '.",' . $table . '.id)';
				$level = '(parentassets.level + "1")';
				break;

			case 'fields':
				$name      = 'CONCAT(SUBSTRING_INDEX(' . $table . '.context, ".", 1), ".' . $view . '.",' . $table . '.id)';
				$level     = 'IF(parentfieldsgroup.id IS NULL, parentassets.level, parentfieldsgroup.level) + "1"';
				$parent_id = 'IF(parentfieldsgroup.id IS NULL, parentassets.id, parentfieldsgroup.id)';
				break;

			case 'ucm_content':
				$name      = 'CONCAT("#", "__ucm_content.",' . $table . '.core_content_id)';
				$level     = "1";
				$parent_id = "1";
				$title     = $name;
				break;

			case 'languages':
				$name  = 'CONCAT("' . $extension . '.' . $view . '.", ' . $table . '.lang_id)';
				$level = '(parentassets.level + "1")';
				break;

			default:
				$name  = 'CONCAT("' . $extension . '.' . $view . '.", ' . $table . '.id)';
				$level = '(parentassets.level + "1")';
				break;
		}

		$query
			->select(
				array(
					$parent_id . ' AS parent_id',
					$level . ' AS level',
					$name . ' AS name',
					$title . ' AS title',
					'"{}" AS rules'
				)
			)
			->from($db->quoteName('#__' . $table, $table))
			->leftJoin($db->quoteName('#__assets', 'assets') . ' ON assets.name = ' . $name)
			->leftJoin($db->quoteName('#__assets', 'parentassets') . ' ON parentassets.name = ' . $parentjoin)
			->where($db->quoteName('assets.name') . ' IS NULL');

		switch ($table)
		{
			case 'categories':
				$query
					->where($db->quoteName($table . '.level') . ' > 0')
					->where($db->quoteName('extension') . ' NOT LIKE ' . $db->quote('%.%'));
				break;

			case 'fields':
				$query
					->leftJoin($db->quoteName('#__assets', 'parentfieldsgroup') . '
					 ON parentfieldsgroup.name = CONCAT(SUBSTRING_INDEX(fields.context, ".", 1), ".fieldgroup.", fields.group_id)'
					);
				break;
		}

		try
		{
			return $db->setQuery($query)->loadObjectList('name');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve orphaned content with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Records in the related table and #__assets, but with an incorrect asset_id.
	 *
	 * @param   string $table     Name of the table.
	 * @param   string $extension Name of the extension.
	 * @param   string $view      Name of the view.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getIncorrectAssetIds($table = '', $extension = '', $view = '')
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$id    = $table . '.id';
		$title = $table . '.title';

		switch ($table)
		{
			case 'ucm_content':
				$id    = $table . '.core_content_id';
				$title = 'CONCAT("#", "__ucm_content.",' . $table . '.core_content_id)';
				break;

			case 'languages':
				$id = $table . '.lang_id';
				break;
		}

		$query
			->select(
				array(
					$id . ' AS id',
					'assets.id AS asset_id',
					$table . '.asset_id AS old_asset_id',
					$title . ' AS title'
				)
			);

		$query
			->from($db->quoteName('#__' . $table, $table))
			->where($db->quoteName('assets.id') . ' != ' . $table . '.asset_id');

		switch ($table)
		{
			case 'categories':
				$query
					->leftJoin($db->quoteName('#__assets', 'assets') . ' 
						ON assets.name = CONCAT(' . $table . '.extension, ".category.", ' . $table . '.id)'
					)
					->where($db->quoteName($table . '.level') . ' > 0')
					->where($db->quoteName('extension') . ' NOT LIKE ' . $db->quote('%.%'));
				break;

			case 'fields_groups':
			case 'fields':
				$query
					->leftJoin($db->quoteName('#__assets', 'assets') . ' 
						ON assets.name = CONCAT(SUBSTRING_INDEX(' . $table . '.context, ".", 1), ".' . $view . '.",' . $table . '.id)'
					);
				break;

			case 'ucm_content':
				$query
					->leftJoin($db->quoteName('#__assets', 'assets') . ' 
						ON assets.name = CONCAT("#", "__ucm_content.", ' . $table . '.core_content_id)'
					);
				break;

			default:
				$query
					->leftJoin($db->quoteName('#__assets', 'assets') . ' 
						ON assets.name = CONCAT("' . $extension . '.' . $view . '.", ' . $id . ')'
					);
				break;
		}

		try
		{
			return $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve incorrect articles with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Gets the incorrect root.1 asset with the corrected values.
	 *
	 * @param   integer $id The ID of the root.1 asset.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getWrongRoot($id)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					'assets.id AS id',
					'"0" AS parent_id',
					'"0" AS level',
					'"root.1" AS name',
					'"Root Asset" AS title',
					'assets.rules AS rules',
					'assets.name AS old_name',
					'assets.title AS old_title',
					'assets.parent_id AS old_parent_id',
					'assets.level AS old_level'
				)
			)
			->from($db->quoteName('#__assets', 'assets'))
			->where($db->quoteName('assets.id') . ' = ' . $id)
			->where('('
				. $db->quoteName('assets.parent_id') . ' != ' . $db->quote(0) . ' OR '
				. $db->quoteName('assets.level') . ' != ' . $db->quote(0) . ' OR '
				. $db->quoteName('assets.name') . ' != ' . $db->quote('root.1') . ' OR '
				. $db->quoteName('assets.title') . ' != ' . $db->quote('Root Asset')
				. ')'
			);

		try
		{
			return $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve wrong components with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Gets the incorrect component assets with the corrected values.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getWrongComponents()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					'assets.id AS id',
					$this->rootId . ' AS parent_id',
					'"1" AS level',
					'assets.name AS name',
					'assets.title AS title',
					'assets.rules AS rules',
					'assets.parent_id AS old_parent_id',
					'assets.level AS old_level'
				)
			)
			->from($db->quoteName('#__assets', 'assets'))
			->where($db->quoteName('assets.name') . ' NOT LIKE  ' . $db->quote('%.%'))
			->where('('
				. $db->quoteName('assets.level') . ' != ' . $db->quote(1) . ' OR '
				. $db->quoteName('assets.parent_id') . ' != ' . $db->quote($this->rootId)
				. ')'
			);

		try
		{
			return $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve wrong components with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Gets the incorrect assets with the corrected values.
	 *
	 * @param   string $table      Name of the table.
	 * @param   string $extensions Name of the extension.
	 * @param   string $view       Name of the view.
	 * @param   string $parentjoin Parent join value.
	 *
	 * @return  array
	 * @since   3.0
	 */
	protected function getWrongAssets($table = '', $extensions = '', $view = '', $parentjoin = '')
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$id    = $table . '.id';
		$title = 'SUBSTRING(' . $table . '.title, 1, 100)';

		switch ($table)
		{
			case 'categories':
				$parent_id = 'IF(parentassets.id IS NULL, parentassetsextension.id, parentassets.id)';
				$level     = 'categories.level + "1"';
				break;

			case 'fields':
				$parent_id = 'IF(parentfieldsgroup.id IS NULL, parentassets.id, parentfieldsgroup.id)';
				$level     = 'IF(parentfieldsgroup.id IS NULL, parentassets.level, parentfieldsgroup.level) + "1"';
				break;

			case 'ucm_content':
				$parent_id = "1";
				$level     = "1";
				$id        = $table . '.core_content_id';
				$title     = 'CONCAT("#", "__ucm_content.",' . $table . '.core_content_id)';
				break;

			case 'languages':
				$parent_id = 'parentassets.id';
				$level     = 'parentassets.level + "1"';
				$id        = $table . '.lang_id';
				break;

			default:
				$parent_id = 'parentassets.id';
				$level     = 'parentassets.level + "1"';
				break;
		}

		$query
			->select(
				array(
					'assets.id AS id',
					$parent_id . ' AS parent_id',
					$level . ' AS level',
					'assets.name AS name',
					$title . ' AS title',
					'assets.rules AS rules',
					'assets.parent_id AS old_parent_id',
					'assets.level AS old_level',
					'assets.title AS old_title'
				)
			)
			->from($db->quoteName('#__assets', 'assets'))
			->leftJoin($db->quoteName('#__' . $table, $table) . ' ON ' . $id . ' = SUBSTRING_INDEX(assets.name, ".", -1)')
			->leftJoin($db->quoteName('#__assets', 'parentassets') . ' ON parentassets.name = ' . $parentjoin)
			->where('('
				. $db->quoteName('assets.level') . ' != ' . $level . ' OR '
				. $db->quoteName('assets.parent_id') . ' != ' . $parent_id . ' OR '
				. $db->quoteName('assets.title') . ' != ' . $title
				. ')'
			);

		switch ($table)
		{
			case 'categories':
				$query
					->leftJoin($db->quoteName('#__assets', 'parentassetsextension') . ' 
						ON ' . $table . '.extension = parentassetsextension.name'
					)
					->where($this->getComponents(true, $table, $view));
				break;

			case 'fields_groups':
				$query
					->where($this->getComponents(true, $table, $view));
				break;

			case 'fields':
				$query
					->leftJoin($db->quoteName('#__assets', 'parentfieldsgroup') . ' 
						ON parentfieldsgroup.name = CONCAT(SUBSTRING_INDEX(fields.context, ".", 1), ".fieldgroup.", fields.group_id)'
					)
					->where($this->getComponents(true, $table, $view));
				break;

			case 'ucm_content':
				$query
					->where($db->quoteName('assets.name') . ' LIKE CONCAT("#", "__ucm_content.%")');
				break;

			default:
				$query
					->where($db->quoteName('assets.name') . ' LIKE "' . $extensions . '.' . $view . '.%"');
				break;
		}

		try
		{
			return $db->setQuery($query)->loadObjectList('id');
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve wrong menus with: ' . $e->getMessage());
		}

		return array();
	}

	/**
	 * Gets a comma seperated list or query of available components.
	 *
	 * @param   boolean $returnquery Should we return the query or the list.
	 * @param   string  $table       Name of the table.
	 * @param   string  $view        Name of the view.
	 *
	 * @return  string
	 * @since   3.0
	 */
	protected function getComponents($returnquery, $table, $view = '')
	{
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
		$components = array();

		$query
			->from($db->quoteName('#__' . $table));

		if ($table == 'categories')
		{
			$query
				->select('DISTINCT extension')
				->where($db->quoteName('level') . ' > 0')
				->where($db->quoteName('extension') . ' NOT LIKE' . $db->quote('%.%'));
		}
		else
		{
			$query
				->select('DISTINCT SUBSTRING_INDEX(context, ".", 1)');
		}

		try
		{
			$components = $db->setQuery($query)->loadColumn();
		}
		catch (Exception $e)
		{
			Log::add('Failed to retrieve components with categories: ' . $e->getMessage());
		}

		if (!$returnquery && $components)
		{
			return implode('", "', $components);
		}

		if ($components)
		{
			$where = array();

			foreach ($components as $component)
			{
				$where[] = $db->quoteName('assets.name') . ' LIKE ' . $db->quote($component . '.' . $view . '.%');
			}

			return '(' . implode(' OR ', $where) . ')';
		}
		else
		{
			return $db->quoteName('assets.name') . ' LIKE NULL';
		}
	}

	/**
	 * Fix admin access conflicts, groups with backend access but not part of the backend viewlevel.
	 *
	 * @param   boolean $fix Fix issues
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function fixAdminConflicts($fix = false)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Get required backend menu Access Level
		$query
			->select('access')
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('module') . ' = ' . $db->quote('mod_menu'))
			->where($db->quoteName('client_id') . ' = 1');

		try
		{
			$backendAccessLevels = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// Get rules for backend access level
		$query
			->clear()
			->select('*')
			->from($db->quoteName('#__viewlevels'))
			->where($db->quoteName('id') . ' IN (' . implode(',', $backendAccessLevels) . ')');

		try
		{
			$viewlevels = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// Get all user groups
		$groups = JHelperUsergroups::getInstance()->getAll();

		// Collect all groups with backend access
		$groupsWithBackend = array();

		foreach ($viewlevels as $viewlevel)
		{
			$rules = json_decode($viewlevel->rules);

			foreach ($rules as $group)
			{
				$groupsWithBackend[] = $group;
			}
		}

		$groupsWithBackend = array_unique($groupsWithBackend);

		// Get the old groups of first viewlevel, we will use this one to add the missing groups
		$viewgroups = array();
		$oldrules   = json_decode($viewlevels[0]->rules);

		foreach ($oldrules as $groupid)
		{
			$viewgroups[] = $groups[$groupid]->title;
		}

		// Track old rules
		$changes['rules']['old'] = implode(', ', $viewgroups);

		// Detect which groups should be part of at least one backend access level
		$missingGroups = array();

		foreach ($groups as $group)
		{
			if ((Access::checkGroup($group->id, 'core.login.admin') || Access::checkGroup($group->id, 'core.admin'))
				&& !count(array_intersect($groupsWithBackend, $group->path)))
			{
				$groupsWithBackend[] = $group->id;
				$missingGroups[]     = (int) $group->id;
			}
		}

		// Add missing groups to current rules
		$newrules = array_merge($oldrules, $missingGroups);

		// Generate new array of groups that should be in the access rules
		$newAccessLevelRules = json_encode($newrules);

		// Update rules if changed
		if ($fix && $missingGroups)
		{
			// Update rules for access level
			$query = $db->getQuery(true);
			$query
				->update($db->quoteName('#__viewlevels'))
				->set($db->quoteName('rules') . ' = ' . $db->quote($newAccessLevelRules))
				->where($db->quoteName('id') . ' = ' . $db->quote($viewlevels[0]->id));

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500, $e);
			}

			// New Groups in viewlevel
			$viewgroups = array();

			foreach ($newrules as $groupid)
			{
				$viewgroups[] = $groups[$groupid]->title;
			}

			$changes['rules']['new'] = implode(', ', $viewgroups);

			// Log changes
			$this->log('fix', 'viewlevels', $viewlevels[0], $changes);
		}

		return ($missingGroups) ? 1 : 0;
	}

	/**
	 * Method to rebuild the assets table.
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function rebuildAssetsTable()
	{
		// Start timer
		$startTime = microtime(true);

		/** @var JTableAsset $asset */
		$asset = Table::getInstance('Asset');
		$asset->rebuild();

		// End timer and log time needed
		$endTime       = microtime(true);
		$timeToRebuild = sprintf('%0.2f', $endTime - $startTime);

		Log::add(
			'Rebuilding #__assets table took ' . $timeToRebuild . ' seconds',
			Log::INFO,
			'com_pwtacl'
		);
	}

	/**
	 * Log all changes made by the diagnostics tool.
	 *
	 * @param   string $action  Action made.
	 * @param   string $type    Logging type.
	 * @param   object $object  The object.
	 * @param   array  $changes The changes made.
	 *
	 * @return  void
	 * @since   3.0
	 */
	private function log($action, $type, $object, $changes = array())
	{
		// Calculate total changes
		$this->changes['total'] = (isset($this->changes['total'])) ? $this->changes['total'] + 1 : 1;

		// Merge earlier changes
		if (isset($this->changes['items'][$action][$type][$object->id]['changes']))
		{
			$changes = array_merge($this->changes['items'][$action][$type][$object->id]['changes'], $changes);
		}

		// Add to changes
		$object->name = (!isset($object->name)) ? $object->title : $object->name;

		// Try to Text component title
		if (strpos($object->title, 'com_') !== false)
		{
			$lang = JFactory::getLanguage();
			$lang->load($object->title . '.sys', JPATH_BASE, null, false, true)
			|| $lang->load($object->title . '.sys', JPATH_ADMINISTRATOR . '/components/' . $object->title, null, false, true);

			$object->title = Text::_($object->title);
		}

		// Set label color
		switch ($action)
		{
			case 'delete':
				$label = 'danger';
				break;

			case 'add':
				$label = 'success';
				break;

			case 'fix':
			default:
				$label = 'warning';
				break;
		}

		// Set label color
		switch ($type)
		{
			case 'components':
			case 'fields_groups':
			case 'fields':
				$icon = 'icon-puzzle';
				break;

			case 'categories':
				$icon = 'icon-folder';
				break;

			case 'content':
				$icon = 'icon-stack';
				break;

			case 'modules':
				$icon = 'icon-cube';
				break;

			case 'menu_types':
				$icon = 'icon-list';
				break;

			case 'assets':
			default:
				$icon = 'icon-lock';
				break;
		}

		// Collect changes
		$this->changes['items'][$action][$type][$object->id] = array(
			'id'      => (int) $object->id,
			'name'    => ($type == 'assets') ? $object->name : '',
			'title'   => $object->title,
			'changes' => $changes,
			'label'   => $label,
			'action'  => Text::_('COM_PWTACL_DIAGNOSTICS_ACTION_' . $action),
			'icon'    => $icon,
			'object'  => ucfirst(str_replace('_', ' ', $type))
		);

		// Log asset changes, only if less then 1000 changes to prevent delays
		if ($this->changes['total'] < 1000)
		{
			Log::add(
				'[' . $action . '][' . $type . '][' . $object->id . '] ' . $object->name . ' ' . json_encode($changes),
				Log::INFO,
				'com_pwtacl'
			);
		}
	}

	/**
	 * Method to fix categories root
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	protected function fixCategoriesRoot()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->select('id')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('system'));

		try
		{
			$root = $db->setQuery($query)->loadResult();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// If root is ID 1, all is fine
		if ($root == 1)
		{
			return;
		}

		// We need to add a root entry
		$root = (object) array(
			'id'        => 1,
			'asset_id'  => 0,
			'parent_id' => 0,
			'level'     => 0,
			'extension' => 'system',
			'title'     => 'ROOT',
			'alias'     => 'root',
			'access'    => 1
		);

		$this->getDbo()->insertObject('#__categories', $root);

		return;
	}

	/**
	 * Method to fix bogus categories entries
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	protected function fixCategories()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->update($db->quoteName('#__categories'))
			->set($db->quoteName('parent_id') . ' = 1')
			->where($db->quoteName('parent_id') . ' = 0')
			->where($db->quoteName('extension') . ' != ' . $db->quote('system'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}
	}

	/**
	 * Method to fix bogus content entries
	 *
	 * @return  void
	 * @since   3.1
	 * @throws  Exception
	 */
	protected function fixContent()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->select('content.id')
			->from($db->quoteName('#__content', 'content'))
			->leftJoin($db->quoteName('#__categories', 'category') . ' ON category.id = content.catid')
			->where('(' . $db->quoteName('category.title') . ' IS NULL OR ' . $db->quoteName('category.title') . ' = ' . $db->quote('ROOT') . ')');

		try
		{
			$itemsToFix = $db->setQuery($query)->loadColumn();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// If we have items to fix, fix them!
		if ($itemsToFix)
		{
			// Get PWT ACL Fix Category
			$catId = $this->pwtAclCategory();

			// Set content catid to PWT ACL Fix category
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query
				->update($db->quoteName('#__content'))
				->set($db->quoteName('catid') . ' = ' . $catId)
				->where($db->quoteName('id') . ' IN (' . implode(',', $itemsToFix) . ')');

			try
			{
				$db->setQuery($query)->execute();
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500, $e);
			}
		}
	}

	/**
	 * Method to get the category ID of the PWT ACL fix category
	 *
	 * @return  integer
	 * @since   3.1
	 * @throws  Exception
	 */
	protected function pwtAclCategory()
	{
		// Check if we already have PWT Fix category
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query
			->select('id')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('alias') . ' = ' . $db->quote('fixed-pwt-acl'));

		try
		{
			$categoryId = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// If we have a category ID, return it
		if ($categoryId)
		{
			return $categoryId;
		}

		// Create PWT Fix category
		$category            = Table::getInstance('Category');
		$category->extension = 'com_content';
		$category->title     = 'Content fixed by PWT ACL';
		$category->alias     = 'fixed-pwt-acl';
		$category->published = 0;
		$category->language  = '*';

		// Set the location in the tree
		$category->setLocation(1, 'last-child');

		// Check to make sure our data is valid
		if (!$category->check())
		{
			$this->setError($category->getError());

			return false;
		}

		// Store the category
		if (!$category->store(true))
		{
			$this->setError($category->getError());

			return false;
		}

		// Rebuild category
		$category->rebuildPath($category->id);

		return $category->id;
	}

	/**
	 * Store PWT ACL Params
	 *
	 * @param   Registry $params Extension params.
	 *
	 * @return   void
	 * @since    3.2
	 * @throws   Exception
	 */
	protected function storeParams(Registry $params)
	{
		// Update extension params
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('params') . ' = ' . $db->quote($params->toString('JSON')))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_pwtacl'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}
	}
}
