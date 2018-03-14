<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewCategories extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		// Prepare the toolbar.
		JToolbarHelper::title(JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'), 'rseventspro48');
		JToolbarHelper::addNew('category.add');
		JToolbarHelper::editList('category.edit');
		JToolbarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('', 'categories.delete');
		JToolbarHelper::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		JToolbarHelper::preferences('com_rseventspro');
		
		JHtml::_('rseventspro.chosen','select');
	}
}