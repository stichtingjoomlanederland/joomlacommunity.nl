<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewGroups extends JViewLegacy
{
	protected $items;
	protected $sidebar;
	
	public function display($tpl = null) {		
		$this->pagination 	= $this->get('Pagination');
		$this->items 		= $this->get('Items');
		$this->canadd 		= RSCommentsHelper::canAdd();
		$this->addToolbar();
		
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS'),'rscomment');
		
		if ($this->canadd)
			JToolbarHelper::addNew('group.add');
		
		JToolbarHelper::editList('group.edit');
		JToolbarHelper::deleteList('','groups.delete');
		JToolbarHelper::preferences('com_rscomments');
		
		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('groups');
	}
}