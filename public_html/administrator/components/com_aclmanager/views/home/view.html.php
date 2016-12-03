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
class AclmanagerViewHome extends JViewLegacy
{
	protected $items;
	protected $form;

	public function display($tpl = null)
	{
		$diagnostic 			= JModelLegacy::getInstance('diagnostic', 'AclmanagerModel');
		$this->params 			= JComponentHelper::getParams('com_aclmanager');
		$this->orphanassets 	= '';
		$this->missingassets 	= '';
		$this->assetissues		= '';
		$this->orphanassets = $diagnostic->getOrphanAssets();
		if(!$this->orphanassets) {
			$this->missingassets = $diagnostic->getMissingAssets();
			if(!$this->missingassets) {
				$this->assetissues = $diagnostic->getAssetIssues();
			}
		}
		$this->adminconflicts	= $diagnostic->getAdminConflicts();

		// Load the tooltip behavior.
		JHtml::_('behavior.tooltip');

		// Load the modal behavior
		JHtml::_('behavior.modal');

		// Load javascript
		if (version_compare(JVERSION, '3.0', 'ge')) {
			JHtml::_('jquery.framework');
		} else {
			if (JFactory::getApplication()->get('jquery') !== true) {
				JHtml::script('administrator/components/com_aclmanager/assets/js/jquery-1.8.3.min.js');
				JFactory::getApplication()->set('jquery', true);
			}
		}
		JHtml::script('administrator/components/com_aclmanager/assets/js/datatables.min.js');

		$this->assign('updateInfo', LiveUpdate::getUpdateInformation(0));

		// Load the submenu
		AclmanagerHelper::addSubmenu('home');

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
		JToolBarHelper::title(JText::_('COM_ACLMANAGER'), 'aclmanager.png');

		// Buttons
		if (JFactory::getUser()->authorise('core.admin', 'com_aclmanager')) {
			JToolBarHelper::preferences('com_aclmanager');
		}
	}
}