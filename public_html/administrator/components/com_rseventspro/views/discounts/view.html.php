<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewDiscounts extends JViewLegacy
{
	public function display($tpl = null) {
		$this->state 		= $this->get('State');
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->filterForm   = $this->get('FilterForm');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_DISCOUNTS'),'rseventspro48');
		JToolBarHelper::addNew('discount.add');
		JToolBarHelper::editList('discount.edit');
		JToolBarHelper::deleteList('','discounts.delete');
		
		JHtml::_('rseventspro.chosen','select');
	}
}