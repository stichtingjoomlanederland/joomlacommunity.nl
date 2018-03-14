<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewReports extends JViewLegacy
{	
	public function display($tpl = null) {		
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->filterForm  	= $this->get('FilterForm');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_REPORTS'),'rscomment');
		JToolbarHelper::deleteList('','reports.delete');
		JToolbarHelper::preferences('com_rscomments');
	}
}