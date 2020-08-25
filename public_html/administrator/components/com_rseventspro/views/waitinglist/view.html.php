<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewWaitinglist extends JViewLegacy
{	
	public function display($tpl = null) {
		$layout = $this->getLayout();
		
		if ($layout == 'edit') {
			$this->form  = $this->get('Form');
			$this->item  = $this->get('Item');
		} else {
			$this->id	 = JFactory::getApplication()->input->getInt('id',0);
			
			if (!$this->id) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_WAITING_ERROR'));
				JFactory::getApplication()->redirect('index.php?option=com_rseventspro');
			}
			
			$this->checkWaiting();
			
			$this->items 		= $this->get('Items');
			$this->pagination 	= $this->get('Pagination');
			$this->state 		= $this->get('State');
			$this->filterForm   = $this->get('FilterForm');
		}
		
		$this->addToolBar($layout);
		parent::display($tpl);
	}
	
	protected function addToolBar($layout) {
		if ($layout == 'edit') {
			JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_WAITING_EDIT'),'rseventspro48');
			
			JToolBarHelper::apply('waitinglist.apply');
			JToolBarHelper::save('waitinglist.save');
			JToolBarHelper::cancel('waitinglist.cancel');
		} else {		
			JToolBarHelper::title(JText::sprintf('COM_RSEVENTSPRO_WAITING_LIST_FOR', $this->getEventName()),'rseventspro48');
			
			JToolBarHelper::custom('waitinglist.approve','ok','ok',JText::_('COM_RSEVENTSPRO_WAITING_APPROVE'));
			JToolBarHelper::deleteList('','waitinglist.delete');
		}
		
		JHtml::_('rseventspro.chosen','select');
	}
	
	protected function getEventName() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($this->id));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	protected function checkWaiting() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$now	= JFactory::getDate();
		
		$query->clear()
			->select($db->qn('waitinglist_time'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($this->id));
		$db->setQuery($query);
		if ($seconds = (int) $db->loadResult()) {
			$query->clear()
				->update($db->qn('#__rseventspro_waitinglist'))
				->set($db->qn('hash').' = '.$db->q(''))
				->where($db->qn('sent').' <> '.$db->q($db->getNullDate()))
				->where($db->q($now->toSql()).' > DATE_ADD('.$db->qn('sent').', INTERVAL '.$seconds.' SECOND)')
				->where($db->qn('used').' = '.$db->q(0))
				->where($db->qn('ide').' = '.$db->q($this->id));
			$db->setQuery($query);
			$db->execute();
		}
	}
}