<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewModules extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.modules');
		$this->setHeading('COM_ED_HEADING_MODULES_PACKAGE_MANAGER');

		// Get the server keys
		$key = $this->config->get('general.key');

		// Check if there's any data on the server
		$model = ED::model('Modules');
		$initialized = $model->initialized();

		if (!$initialized) {
			return parent::display('modules/initialize/default');
		}

		JToolbarHelper::custom('install', 'upload' , '' , JText::_('Install / Update'));
		JToolbarHelper::custom('uninstall', 'remove', '', JText::_('COM_ED_TOOLBAR_BUTTON_UNINSTALL'));
		JToolbarHelper::custom('discover', 'refresh', '', JText::_('COM_ED_TOOLBAR_BUTTON_FIND_UPDATES'), false);

		// Ordering
		$ordering = $this->getUserState('modules.filter_order', 'filter_order', 'a.id', 'cmd');
		$direction = $this->getUserState('modules.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$published = $this->getUserState('modules.published', 'published', '*', 'word');

		// Search query
		$search = $this->getUserState('modules.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Get the list of languages now
		$modules = $model->getModules();
		$pagination	= $model->getPagination();

		$this->set('published', $published);
		$this->set('search', $search);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('modules', $modules);
		$this->set('pagination', $pagination);

		return parent::display('modules/default/default');
	}
}
