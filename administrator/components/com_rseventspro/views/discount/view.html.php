<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewDiscount extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $used;
	protected $excludes;
	protected $tabs;
	
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_DISCOUNT'),'rseventspro48');
		JToolBarHelper::apply('discount.apply');
		JToolBarHelper::save('discount.save');
		JToolBarHelper::save2new('discount.save2new');
		JToolBarHelper::cancel('discount.cancel');
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
}