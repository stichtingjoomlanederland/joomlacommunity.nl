<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewFile extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $fieldsets;
	protected $tabs;
	protected $layouts;
	
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->tabs			= $this->get('Tabs');
		$this->layouts		= $this->get('Layouts');
		$this->fieldsets	= $this->form->getFieldsets();
		$this->type			= rsfilesHelper::getType($this->item->IdFile);
		$this->briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		if ($this->type != 'folder') {
			$this->mirrors 		= $this->get('Mirrors');
			$this->screenshots 	= $this->get('Screenshots');
		}
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		if ($this->type == 'folder') {
			JToolBarHelper::title(JText::_('COM_RSFILES_ADD_EDIT_FOLDER','rsfiles'));
		} else {
			JToolBarHelper::title(JText::_('COM_RSFILES_ADD_EDIT_FILE','rsfiles'));
		}
		
		JToolBarHelper::apply('file.apply');
		JToolBarHelper::save('file.save');
		JToolBarHelper::cancel('file.cancel');
	}
}