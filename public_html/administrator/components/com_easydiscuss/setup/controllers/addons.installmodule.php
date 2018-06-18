<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerAddonsInstallModule extends EasyDiscussSetupController
{
	public function execute()
	{
		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Determines which module to install on the site
		$module = $this->input->get('module', '', 'cmd');

		// Construct the absolute path to the module
		$absolutePath = $path . '/' . $module;

		// Try to install the module now.
		$state = $this->install($module, $absolutePath);

		$this->setInfo(JText::sprintf('Module %1$s installed on the site', $module), true);
		return $this->output();
	}

	public function install($element, $path)
	{
		// Get Joomla's installer instance
		$installer = new JInstaller();

		// Allow overwriting existing modules
		$installer->setOverwrite(true);

		// Install the module
		$state = $installer->install($path);

		if (!$state) {
			return false;
		}

		$db = ED::db();

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__extensions') . ' SET ' . $db->qn('access') . '=' . $db->Quote(1);
		$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote('module');
		$query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote($element);
		$query[] = 'AND ' . $db->qn('access') . '=' . $db->Quote(0);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->Query();

		// Check if this module already exists on module_menu
		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ', b.' . $db->qn('moduleid') . ' FROM ' . $db->qn('#__modules') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__modules_menu') . ' AS b ON a.' . $db->qn('id') . ' = b.' . $db->qn('moduleid');
		$query[] = 'WHERE a.' . $db->qn('module') . ' = ' . $db->Quote($element);
		$query[] = 'AND b.' . $db->qn('moduleid') . ' IS NULL';

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return false;
		}

		foreach ($result as $row) {
			$mod = new stdClass();
			$mod->moduleid = $row->id;
			$mod->menuid = 0;

			$db->insertObject('#__modules_menu', $mod);
		}

		return true;
	}
}