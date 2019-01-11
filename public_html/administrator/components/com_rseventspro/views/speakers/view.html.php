<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewSpeakers extends JViewLegacy
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
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_LIST_SPEAKERS'),'rseventspro48');
		JToolBarHelper::addNew('speaker.add');
		JToolBarHelper::editList('speaker.edit');
		JToolBarHelper::deleteList('','speakers.delete');
		JToolBarHelper::publishList('speakers.publish');
		JToolBarHelper::unpublishList('speakers.unpublish');
		
		JHtml::_('rseventspro.chosen','select');
	}
}