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

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerAddonsList extends EasyDiscussSetupController
{
	public function execute()
	{
		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		if ($this->isDevelopment()) {

			$result = new stdClass();
			$result->html = '<div style="padding:20px;background: #f4f4f4;border: 1px dotted #d7d7d7;margin-top:20px;">In development mode, this option is disabled.</div>';

			return $this->output($result);
		}

		// Construct the extraction path for the module
		$modulesExtractPath = ED_TMP . '/modules';
		$pluginsExtractPath = ED_TMP . '/plugins';

		// Get the modules list
		$modules = $this->getModulesList($path, $modulesExtractPath);

		// Get the plugins list
		$plugins = $this->getPluginsList($path, $pluginsExtractPath);

		$data = new stdClass();
		$data->modules = $modules;
		$data->plugins = $plugins;
		
		ob_start();
		include(dirname(__DIR__) . '/themes/steps/addons.list.php');
		$contents = ob_get_contents();
		ob_end_clean();

		$result = new stdClass();
		$result->html = $contents;
		$result->modulePath = $modulesExtractPath;
		$result->pluginPath = $pluginsExtractPath;
		
		// Since we combine maintenance page with this,
		// we need to get the scripts to execute as well
		$maintenance = $this->getMaintenanceScripts();

		$result->scripts = $maintenance['scripts'];
		$result->maintenanceMsg = $maintenance['message'];

		return $this->output($result);
	}

	/**
	 * Retrieves the list of maintenance scripts available
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function getMaintenanceScripts()
	{
		$maintenance = ED::maintenance();
		$previous = $this->getPreviousVersion('scriptversion');

		$files = $maintenance->getScriptFiles($previous);

		$msg = JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_NO_SCRIPTS_TO_EXECUTE');
		
		if ($files) {
			$msg = JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_TOTAL_FILES_TO_EXECUTE', count($files));
		}

		$result = array('message' => $msg, 'scripts' => $files);

		return $result;
	}

	/**
	 * Retrieves a list of plugins to be installed
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function getPluginsList($path, $tmp)
	{
		$zip = $path . '/plugins.zip';

		$state = JArchive::extract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of plugin groups
		$groups = JFolder::folders($tmp, '.', false, true);

		$plugins = array();

		foreach ($groups as $group) {
			$groupTitle = basename($group);

			// Get a list of items in each groups
			$items = JFolder::folders($group, '.', false, true);
			
			foreach ($items as $item) {
				$element = basename($item);
				$manifest = $item . '/' . $element . '.xml';

				// Read the xml file
				$parser = JFactory::getXml($manifest);

				if (!$parser) {
					continue;
				}
				$plugin = new stdClass();
				$plugin->element = $element;
				$plugin->group = $groupTitle;
				$plugin->title = (string) $parser->name;
				$plugin->version = (string) $parser->version;
				$plugin->description = (string) $parser->description;
				$plugin->description = trim($plugin->description);
				$plugin->disabled = false; 

				// Installer plugin must be installed
				if ($plugin->group == 'installer') {
					$plugin->disabled = true;
				}

				$plugins[] = $plugin;
			}
		}

		return $plugins;
	}

	/**
	 * Retrieves a list of modules to be installed
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function getModulesList($path, $tmp)
	{
		$zip = $path . '/modules.zip';

		$state = JArchive::extract($zip, $tmp);

		if (!$state) {
			return false;
		}

		// Get a list of modules from the package
		$items = JFolder::folders($tmp, '.', false, true);
		$modules = array();
		$installedModules = array();

		// Get installed module.
		// We only do this for upgrade from 3.x
		if ($this->isUpgradeFrom3x()) {
			$installedModules = $this->getInstalledModules(); 
		}
		
		// Get previous version installed. 
		// If previous version exists, means this is an upgrade
		$isUpgrade = $this->getPreviousVersion('scriptversion');

		foreach ($items as $item) {
			$element = basename($item);
			$manifest = $item . '/' . $element . '.xml';

			// Read the xml file
			$parser = JFactory::getXml($manifest);

			$module = new stdClass();
			$module->title = (string) $parser->name;
			$module->version = (string) $parser->version;
			$module->description = (string) $parser->description;
			$module->description = trim($module->description);
			$module->element = $element;
			$module->checked = true;
			$module->disabled = false;

			// we tick modules that are installed on the site
			if ($isUpgrade) {
				$module->checked = $this->isModuleInstalled($element);
			}

			// Check if the module already installed, put a flag
			// Disable this only if the module is checked.
			if (in_array($module->element, $installedModules)) {
				$module->disabled = true; 
			}

			$modules[] = $module;
		}

		return $modules;
	}

	/**
	 * Retrieves a list of installed modules on the site
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getInstalledModules()
	{
		$db = ED::db();
		$query = array();
		$query[] = 'SELECT ' . $db->qn('module') . ' FROM ' . $db->qn('#__modules');
		$query[] = 'WHERE ' . $db->qn('module') . ' LIKE ' . $db->Quote('%mod_easydiscuss%');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$modules = $db->loadColumn();

		return $modules;
	}

	/**
	 * Determines if the module is installed on the site.
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	private function isModuleInstalled($element)
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__modules');
		$query[] = 'WHERE ' . $db->qn('module') . '=' . $db->Quote($element);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$installed = $db->loadResult() > 0 ? true : false;
		
		return $installed;
	}


	/**
	 * Determines if EasyDiscuss was upgraded
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '" . $prefix . "discuss_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__discuss_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('dbversion');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}
}
