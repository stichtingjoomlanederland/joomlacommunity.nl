<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewGroup extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->tabs 		= $this->get('Tabs');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		
		if (!rseventsproHelper::isJ4()) {
			JHtml::_('formbehavior.chosen', 'select');
		}
		
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_GROUP'),'rseventspro48');
		JToolBarHelper::apply('group.apply');
		JToolBarHelper::save('group.save');
		JToolBarHelper::save2new('group.save2new');
		JToolBarHelper::cancel('group.cancel');
	}
}