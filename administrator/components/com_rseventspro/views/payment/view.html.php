<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewPayment extends JViewLegacy
{
	protected $form;
	protected $item;
	
	public function display($tpl = null) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php';
		
		$this->placeholders = RSEventsProPlaceholders::get('payment');
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_PAYMENT'),'rseventspro48');
		JToolBarHelper::apply('payment.apply');
		JToolBarHelper::save('payment.save');
		JToolBarHelper::save2new('payment.save2new');
		JToolBarHelper::cancel('payment.cancel');
	}
}