<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewSubscriptions extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->items 		 = $this->get('Items');
		$this->pagination 	 = $this->get('Pagination');
		$this->state 		 = $this->get('State');
		$this->article		 = RSCommentsHelperAdmin::ArticleTitle($this->state->get('filter.component'), $this->state->get('filter.component_id'));
		$this->filterForm  	 = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		if (isset($this->activeFilters['component']) && !empty($this->activeFilters['component'])) {
			$component = $this->state->get('filter.component');
			
			if ($component == 'com_content' || $component == 'com_rsblog' || $component == 'com_k2' || $component == 'com_flexicontent') {
				$componentIDXml = new SimpleXMLElement('<field name="component_id" type="selectbtn" context="com_rscomments.subscriptions" />');
				$this->filterForm->setField($componentIDXml, 'filter');
			}
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::sprintf('COM_RSCOMMENTS_SUBSCRIPTIONS_TITLE', (!empty($this->article) ? JText::_('COM_RSCOMMENTS_FROM').$this->article : '')),'rscomment');
		JToolbarHelper::deleteList('','subscriptions.delete');
		JToolbarHelper::preferences('com_rscomments');
	}
}