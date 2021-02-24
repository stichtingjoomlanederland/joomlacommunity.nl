<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewSponsor extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_SPONSOR'),'rseventspro48');
		JToolBarHelper::apply('sponsor.apply');
		JToolBarHelper::save('sponsor.save');
		JToolBarHelper::save2new('sponsor.save2new');
		JToolBarHelper::cancel('sponsor.cancel');
	}
}