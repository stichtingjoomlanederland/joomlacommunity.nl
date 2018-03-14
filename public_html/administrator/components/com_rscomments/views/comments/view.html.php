<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewComments extends JViewLegacy
{
	public function display($tpl = null) {
		$this->items 		 = $this->get('Items');
		$this->pagination 	 = $this->get('Pagination');
		$this->state 		 = $this->get('State');
		$this->article		 = RSCommentsHelper::ArticleTitle($this->state->get('filter.component'), $this->state->get('filter.component_id'));
		$this->filterForm  	 = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		if (isset($this->activeFilters['component']) && !empty($this->activeFilters['component'])) {
			$component = $this->state->get('filter.component');
			
			if ($component == 'com_content' || $component == 'com_rsblog' || $component == 'com_k2' || $component == 'com_flexicontent') {
				$componentIDXml = new SimpleXMLElement('<field name="component_id" type="selectbtn" context="com_rscomments.comments" />');
				$this->filterForm->setField($componentIDXml, 'filter');
			}
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::sprintf('COM_RSCOMMENTS_COMMENTS_TITLE', (!empty($this->article) ? JText::_('COM_RSCOMMENTS_FROM').$this->article : '')),'rscomment');
		JToolbarHelper::editList('comment.edit');
		JToolbarHelper::publish('comments.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('comments.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('','comments.delete');
		JToolbarHelper::custom('comments.votes','trash','trash',JText::_('COM_RSCOMMENTS_CLEAR_VOTES'));
		JToolbarHelper::preferences('com_rscomments');
	}
}