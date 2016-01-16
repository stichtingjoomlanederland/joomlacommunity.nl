<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewGroup extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $used;
	protected $excludes;
	protected $tabs;
	
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->used 		= $this->get('Used');
		$this->tabs 		= $this->get('Tabs');
		$this->excludes		= $this->get('Excludes');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_GROUP'),'rseventspro48');
		JToolBarHelper::apply('group.apply');
		JToolBarHelper::save('group.save');
		JToolBarHelper::save2new('group.save2new');
		JToolBarHelper::cancel('group.cancel');
		
		rseventsproHelper::chosen();
	}
}