<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewRules extends JViewLegacy
{
	protected $items;
	protected $sidebar;
	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->sidebar		= $this->get('Sidebar');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_PAYMENT_RULES'),'rseventspro48');
		JToolBar::getInstance('toolbar')->appendButton('Link', 'back', JText::_('COM_RSEVENTSPRO_GLOBAL_BACK_BTN'), JRoute::_('index.php?option=com_rseventspro&view=payments'));
		JToolBarHelper::deleteList('','rules.delete');
		JToolBarHelper::custom('rseventspro','rseventspro32','rseventspro32',JText::_('COM_RSEVENTSPRO_GLOBAL_NAME'),false);
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
	
	protected function getSubject($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('subject'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		return $db->loadResult();
	}
}