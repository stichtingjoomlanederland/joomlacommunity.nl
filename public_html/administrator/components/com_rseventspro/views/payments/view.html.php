<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewPayments extends JViewLegacy
{
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->plugins		= $this->get('Plugins');
		$this->state 		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_PAYMENTS'),'rseventspro48');
		JToolBarHelper::addNew('payment.add');
		JToolBarHelper::editList('payment.edit');
		JToolBarHelper::deleteList('','payments.delete');
		JToolBarHelper::publishList('payments.publish');
		JToolBarHelper::unpublishList('payments.unpublish');
		JToolBarHelper::divider();
		JToolBar::getInstance('toolbar')->appendButton('Link', 'list', JText::_('COM_RSEVENTSPRO_PAYMENT_RULES'), JRoute::_('index.php?option=com_rseventspro&view=rules'));
		
		JHtml::_('rseventspro.chosen','select');
	}
}