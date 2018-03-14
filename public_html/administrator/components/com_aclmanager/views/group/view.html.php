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
class AclmanagerViewGroup extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $form;

	public function display($tpl = null)
	{
		$this->params 			= JComponentHelper::getParams('com_aclmanager');
		$this->actions			= AclmanagerHelper::getActions();
		$this->permissions		= $this->get('Permissions');
		$this->items			= $this->get('Items');
		$this->state			= $this->get('State');
		$this->assets			= AclmanagerHelper::asset($this->items,$this->state);
		$this->pagination		= $this->get('Pagination');
		$this->form				= $this->get('Form');

		// Load the tooltip behavior.
		JHtml::_('behavior.tooltip');

		// Load the modal behavior
		JHTML::_('behavior.modal');

		// Load javascript
		if (version_compare(JVERSION, '3.0', 'ge')) {
			JHtml::_('jquery.framework');
		} else {
			if (JFactory::getApplication()->get('jquery') !== true) {
				JHtml::script('administrator/components/com_aclmanager/assets/js/jquery-1.8.3.min.js');
				JFactory::getApplication()->set('jquery', true);
			}
			JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');
			JHTML::script('administrator/components/com_aclmanager/assets/js/scrollspy.js');
		}
		JHTML::script('administrator/components/com_aclmanager/assets/js/datatables.min.js');
		JHTML::script('administrator/components/com_aclmanager/assets/js/permissions.js');

		// Show reset message
		$layout		= JFactory::getApplication()->input->get('layout',null);
		$session 	= JFactory::getSession();
		$task 		= $session->get('aclmanager_reset');

		if(!$layout && $task == 'revert') {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_PERMISSIONS_REVERTED'));
			$session->set('aclmanager_reset', null);
		} elseif(!$layout && $task == 'clear') {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_PERMISSIONS_CLEARED'));
			$session->set('aclmanager_reset', null);
		}

		// Load the toolbar
		$this->addToolbar();

		// Warning on diagnostic issues
		$diagnostic 	= JModelLegacy::getInstance('Diagnostic', 'AclmanagerModel', array('ignore_request' => true));
		$orphanassets 	= '';
		$missingassets 	= '';
		$assetissues		= '';
		$orphanassets = $diagnostic->getOrphanAssets();
		if(!$orphanassets) {
			$missingassets = $diagnostic->getMissingAssets();
			if(!$missingassets) {
				$assetissues = $diagnostic->getAssetIssues();
			}
		}
		$adminconflicts	= $diagnostic->getAdminConflicts();

		if($orphanassets || $missingassets || $assetissues || $adminconflicts) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_NOTICE_FIX_DIAGNOSTIC_ISSUES'), 'warning');
		}

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
		// Disable mainmenu
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		// Title
		$groupId = $this->state->get('filter.group_id');
		$groupname = AclmanagerHelper::groupName($groupId);
		JToolBarHelper::title(JText::_('COM_ACLMANAGER').': '.JText::_('COM_ACLMANAGER_SUBMENU_GROUP').' - '.$groupname, 'aclmanager.png');

		// Buttons
		$bar = JToolBar::getInstance('toolbar');
		if (JFactory::getUser()->authorise('core.edit', 'com_aclmanager')) {
			JToolBarHelper::apply('group.apply');
			JToolBarHelper::save('group.save');
			JToolBarHelper::divider();
		}
		JToolBarHelper::cancel('cancel');
		if (JFactory::getUser()->authorise('core.edit', 'com_aclmanager')) {
			JToolBarHelper::divider();

			if (version_compare(JVERSION, '3.0', 'ge')) {
				JHtml::_('bootstrap.modal', 'collapseModal');
				$title = JText::_('COM_ACLMANAGER_RESET');
				$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
							<i class=\"icon-refresh\" title=\"$title\"></i>
							$title</button>";
				$bar->appendButton('Custom', $dhtml, 'reset');
			} else {
				$bar->appendButton( 'Popup', 'purge', 'COM_ACLMANAGER_RESET', 'index.php?option=com_aclmanager&view=group&id='.$groupId.'&layout=reset&tmpl=component',290,200);
			}
		}
		JToolBarHelper::divider();
		$bar->appendButton( 'Popup', 'print', 'JGLOBAL_PRINT', 'index.php?option=com_aclmanager&view=group&id='.$groupId.'&layout=print&tmpl=component',875,550 );
	}
}