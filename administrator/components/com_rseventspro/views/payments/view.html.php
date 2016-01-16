<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewPayments extends JViewLegacy
{
	protected $items;
	protected $state;
	protected $sidebar;
	protected $filterbar;
	protected $plugins;
	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->plugins		= $this->get('Plugins');
		$this->filterbar	= $this->get('Filterbar');	
		$this->state 		= $this->get('State');
		$this->sidebar		= $this->get('Sidebar');
		
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
		JToolBarHelper::divider();
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
	}
}