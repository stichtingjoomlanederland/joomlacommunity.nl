<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewUsers extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_USERS'),'rseventspro48');
		JToolBarHelper::editList('user.edit');
		JToolBarHelper::deleteList('','users.delete');
		
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('id' => 'toolbar-reset', 'listCheck' => true, 'text' => JText::_('COM_RSEVENTSPRO_EVENTS_CREATED_RESET'), 'task' => 'users.reset', 'doTask' => 'if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._(\'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST\')); } else { if (confirm(\''.JText::_('COM_RSEVENTSPRO_EVENTS_CREATED_RESET_MESSAGE', true).'\')) { Joomla.submitbutton(\'users.reset\'); } }', 'htmlAttributes' => '', 'btnClass' => 'btn', 'class' => 'icon-refresh', 'message' => JText::_('COM_RSEVENTSPRO_EVENTS_CREATED_RESET_MESSAGE')));
		JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'reset');
	}
	
	public function hasProfile($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_user_info'))
			->where($db->qn('id').' = '.(int) $id);
		$db->setQuery($query);
		return (bool) $db->loadResult();
	}
}