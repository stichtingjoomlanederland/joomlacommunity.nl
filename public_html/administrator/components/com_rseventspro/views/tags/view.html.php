<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewTags extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_TAGS'),'rseventspro48');
		JToolBarHelper::addNew('tag.add');
		JToolBarHelper::editList('tag.edit');
		JToolBarHelper::deleteList('','tags.delete');
		JToolBarHelper::publishList('tags.publish');
		JToolBarHelper::unpublishList('tags.unpublish');
		
		JHtml::_('rseventspro.chosen','select');
	}
	
	protected function getEvents($id) {
		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true); 
		$html	= array();
		
		$query->clear();
		$query->select($db->qn('e.id'))
			->select($db->qn('e.name'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('t.ide').' = '.$db->qn('e.id'))
			->where($db->qn('t.type').' = '.$db->q('tag'))
			->where($db->qn('t.id').' = '.$db->q($id));
		
		$db->setQuery($query,0,5);
		if ($events = $db->loadObjectList()) {
			foreach ($events as $event)
				$html[] = '<a href="index.php?option=com_rseventspro&task=event.edit&id='.$event->id.'">'.$event->name.'</a>';
		}
		
		return !empty($html) ? implode('<br />',$html) : '';
	}
}