<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewSubscriptions extends JViewLegacy
{
	protected $pagination;
	protected $items;
	protected $state;
	protected $filterbar;
	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->addToolbar();
		
		$this->filterbar	= $this->get('FilterBar');
		$this->sidebar 		= $this->get('SideBar');
		
		$this->article		= RSCommentsHelper::ArticleTitle($this->state->get('subscriptions.filter.component'), $this->state->get('subscriptions.filter.component_id'));
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::sprintf('COM_RSCOMMENTS_SUBSCRIPTIONS_TITLE', (!empty($this->article) ? JText::_('COM_RSCOMMENTS_FROM').$this->article : '')),'rscomment');
		
		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('subscriptions');

		JToolbarHelper::deleteList('','subscriptions.delete');
		JToolbarHelper::preferences('com_rscomments');
	}
}