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
class AclmanagerViewUser extends JViewLegacy
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

		// Load the toolbar
		$this->addToolbar();

		// Show edit notice
		JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_NOTICE_VIEW_USER_PERMISSIONS'), 'notice');

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
		$user = JFactory::getUser($this->state->get('filter.user_id'));
		JToolBarHelper::title(JText::_('COM_ACLMANAGER').': '.JText::_('COM_ACLMANAGER_SUBMENU_USER').' - '.$user->name, 'aclmanager.png');

		// Buttons
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::cancel('cancel');
		JToolBarHelper::divider();
		$bar->appendButton( 'Popup', 'print', 'JGLOBAL_PRINT', 'index.php?option=com_aclmanager&view=user&id='.$user->id.'&layout=print&tmpl=component',875,550 );
	}
}