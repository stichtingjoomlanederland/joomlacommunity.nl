<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * HTML View class for the ACL Manager component
 */
class AclmanagerViewDiagnostic extends JViewLegacy
{
	protected $items;
	protected $form;

	public function display($tpl = null)
	{
		$this->params 			= JComponentHelper::getParams('com_aclmanager');
		$this->orphanassets 	= '';
		$this->missingassets 	= '';
		$this->assetissues		= '';
		$this->orphanassets = $this->get('orphanAssets');
		if(!$this->orphanassets) {
			$this->missingassets = $this->get('missingAssets');
			if(!$this->missingassets) {
				$this->assetissues = $this->get('assetIssues');
			}
		}
		$this->adminconflicts	= $this->get('adminConflicts');

		// Load the tooltip behavior.
		JHtml::_('behavior.tooltip');

		// Load the modal behavior
		JHTML::_('behavior.modal');

		// Load the submenu
		if (version_compare(JVERSION, '3.0', 'ge')) {
			//
		} else {
			AclmanagerHelper::addSubmenu('diagnostic');
		}

		// Load the toolbar
		$this->addToolbar();

		// Display the view
		if (version_compare(JVERSION, '3.0', 'ge')) {
			parent::display('bootstrap');
		} else {
			parent::display($tpl);
		}
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		// Title
		JToolBarHelper::title(JText::_('COM_ACLMANAGER').': '.JText::_('COM_ACLMANAGER_SUBMENU_DIAGNOSTIC'), 'aclmanager.png');

		// Buttons
		if (JFactory::getUser()->authorise('core.admin', 'com_aclmanager')) {
			JToolBarHelper::custom('diagnostic.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_aclmanager');
		}
	}
}