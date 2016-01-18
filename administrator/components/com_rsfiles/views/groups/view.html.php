<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewGroups extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $sidebar;
	protected $filterbar;
	
	public function display($tpl = null) {
		$this->filterbar	= $this->get('Filterbar');
		$this->state 		= $this->get('State');
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->sidebar		= $this->get('Sidebar');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_GROUPS'),'rsfiles');
		JToolBarHelper::addNew('group.add');
		JToolBarHelper::editList('group.edit');
		JToolBarHelper::deleteList('','groups.delete');
		JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
	}
}