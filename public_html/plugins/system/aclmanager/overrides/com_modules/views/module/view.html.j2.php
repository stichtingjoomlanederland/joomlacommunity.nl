<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Extend Joomla core class
 */
class ModulesViewModule extends ModulesViewModuleCore
{
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo    	= ModulesHelper::getActions($this->item->id);
		$item		= $this->get('Item');

		JToolBarHelper::title( JText::sprintf('COM_MODULES_MANAGER_MODULE', JText::_($this->item->module)), 'module.png');

		$canCreate	= $user->authorise('core.create',		'com_modules');
		$canEdit	= $user->authorise('core.edit',			'com_modules.module.'.$this->item->id);
		$canChange	= $user->authorise('core.edit.state',	'com_modules.module.'.$this->item->id);

		// If not checked out, can save the item.
		if (!$checkedOut && ($canEdit || $canCreate)) {
			JToolBarHelper::apply('module.apply');
			JToolBarHelper::save('module.save');
		}
		if (!$checkedOut && $canCreate) {
			JToolBarHelper::save2new('module.save2new');
		}
			// If an existing item, can save to a copy.
		if (!$isNew && $canCreate) {
			JToolBarHelper::save2copy('module.save2copy');
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('module.cancel');
		} else {
			JToolBarHelper::cancel('module.cancel', 'JTOOLBAR_CLOSE');
		}

		// Get the help information for the menu item.
		$lang = JFactory::getLanguage();

		$help = $this->get('Help');
		if ($lang->hasKey($help->url)) {
			$debug = $lang->setDebug(false);
			$url = JText::_($help->url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}
		JToolBarHelper::help($help->key, false, $url);
	}
}
