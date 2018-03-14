<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
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
		
		JHtml::_('rseventspro.chosen','select');
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