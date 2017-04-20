<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewComments extends JViewLegacy
{
	protected $comments;
	protected $filterbar;
	protected $pagination;

	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->article		= RSCommentsHelper::ArticleTitle($this->state->get('com_rscomments.comments.filter.component'), $this->state->get('com_rscomments.comments.filter.component_id'));

		$this->addToolbar();

		$this->filterbar	= $this->get('FilterBar');
		$this->sidebar 		= $this->get('SideBar');

		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::sprintf('COM_RSCOMMENTS_COMMENTS_TITLE', (!empty($this->article) ? JText::_('COM_RSCOMMENTS_FROM').$this->article : '')),'rscomment');

		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('comments');

		JToolbarHelper::editList('comment.edit');
		JToolbarHelper::publish('comments.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('comments.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('','comments.delete');
		JToolbarHelper::custom('comments.votes','trash','trash',JText::_('COM_RSCOMMENTS_CLEAR_VOTES'));
		JToolbarHelper::preferences('com_rscomments');
	}
}