<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewReports extends JViewLegacy
{
	protected $items;
	protected $filterbar;
	protected $pagination;
	protected $sidebar;
	
	public function display($tpl = null) {		
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		
		$this->addToolbar();

		$this->filterbar	= $this->get('FilterBar');
		$this->sidebar 		= $this->get('SideBar');
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_REPORTS'),'rscomment');
		
		JToolbarHelper::deleteList('','reports.delete');
		JToolbarHelper::preferences('com_rscomments');
		
		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('reports');
	}
}