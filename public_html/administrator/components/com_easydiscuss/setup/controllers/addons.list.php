<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include parent library
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

		// Check if path is empty.

		// Construct the extraction path for the module
		$info = $this->getInfo();
		$modulesExtractPath = ED_TMP . '/modules_v' . $info->version;
		$pluginsExtractPath = ED_TMP . '/plugins_v' . $info->version;

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
		
		return $this->output($result);
	}

	/**
	 * Retrieves a list of plugins to be installed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function getPluginsList($path, $tmp)
	{
		$info = $this->getInfo();
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

				$plugins[] = $plugin;
			}
		}

		return $plugins;
	}

	/**
	 * Retrieves a list of modules to be installed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	private function getModulesList($path, $tmp)
	{
		$info = $this->getInfo();
		$zip = $path . '/modules.zip';

		$state = JArchive::extract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of modules
		$items = JFolder::folders($tmp, '.', false, true);

		$modules = array();
		
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

			$modules[] = $module;
		}

		return $modules;
	}
}
