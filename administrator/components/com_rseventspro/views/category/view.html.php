<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewCategory extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	public function display($tpl = null) {
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_('COM_RSEVENTSPRO_CATEGORY_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE'), 'rseventspro48 category-' . ($isNew ? 'add' : 'edit'));
		JToolbarHelper::apply('category.apply');
		JToolbarHelper::save('category.save');
		JToolbarHelper::save2new('category.save2new');
		
		if (!$isNew) {
			JToolbarHelper::save2copy('category.save2copy');
		}

		JToolbarHelper::cancel('category.cancel');
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
}