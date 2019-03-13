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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	/**
	 * Renders the settings form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.settings');

		$layout = $this->getLayout();
		$activeTab = $this->input->get('active', '', 'default');

		// Build the namespace
		$namespace = 'settings/' . $layout . '/default';

		if ($layout == 'default') {
			return $this->app->redirect('index.php?option=com_easydiscuss&view=settings&layout=general');
		}

		// Set the title and the description of the page
		$this->setHeading('COM_EASYDISCUSS_SETTINGS_' . strtoupper($layout) . '_TITLE', 'COM_EASYDISCUSS_SETTINGS_' . strtoupper($layout) . '_DESC');

		JToolBarHelper::apply();

		// Get the tabs
		$tabs = $this->getTabs($layout);

		$this->set('tabs', $tabs);
		$this->set('activeTab', $activeTab);
		$this->set('config', $this->config);
		$this->set('layout', $layout);
		$this->set('namespace', $namespace);

		return parent::display('settings/form');

		// $contents = $this->getContents($layout);

		// $this->set('contents', $contents);

		// parent::display('settings/wrapper');
	}

	public function getTabs($layout)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/themes/default/settings/' . $layout;

		$files = JFolder::files($path, '.php');
		$tabs = array();
		
		// Get the current active tab
		$active = $this->input->get('active', '', 'cmd');

		if (!$files) {
			return false;
		}

		foreach ($files as $file) {

			// If a user upgrades from 5.0 or any prior versions, we shouldn't get the default.php
			if ($file == 'default.php') {
				continue;
			}

			$fileName = $file;
			$file = str_ireplace('.php', '', $file);

			$tab = new stdClass();
			$tab->id = str_ireplace(array(' ', '.', '#', '_'), '-', strtolower($file));
			$tab->title = JText::_('COM_EASYDISCUSS_SETTINGS_TAB_' . strtoupper($tab->id));
			$tab->file = $path . '/' . $fileName;
			$tab->active = ($file == 'general' && !$active) || $active === $tab->id;

			// Get the contents of the tab now
			$theme = ED::themes();

			$defaultSAId = ED::getDefaultSAIds();
			$joomlaVersion = ED::getJoomlaVersion();
			$joomlaGroups = ED::getJoomlaUserGroups();

			$theme->set('defaultSAId', $defaultSAId);
			$theme->set('joomlaVersion', $joomlaVersion);
			$theme->set('joomlaGroups', $joomlaGroups);

			if ($layout == 'email') {
				$categories = $this->getCategories();
				$theme->set('categories', $categories);
			}

			// Comments settings
			if (method_exists($this, $layout)) {
				$this->$layout($theme);
			}

			$tab->contents = $theme->output('admin/settings/' . strtolower($layout) . '/' . $file);

			$tabs[$tab->id] = $tab;
		}

		// Sort items manually. Always place "General" as the first item
		if (isset($tabs['general'])) {
		
			$general = $tabs['general'];

			unset($tabs['general']);

			array_unshift($tabs, $general);

		} else {
			// First tab should always be highlighted
			$firstIndex = array_keys($tabs);
			$firstIndex = $firstIndex[0];

			if ($active) {
				$tabs[$firstIndex]->active = $active === $tabs[$firstIndex]->id;
			} else {
				$tabs[$firstIndex]->active = true;
			}
		}

		return $tabs;
	}

	public function getCategories()
	{
		$db = ED::db();
		$query = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
			 . 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		return $categories;
	}
}
