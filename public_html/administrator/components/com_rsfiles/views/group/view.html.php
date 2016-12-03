<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewGroup extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $used;
	protected $excludes;
	
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->used 		= $this->get('Used');
		$this->excludes		= $this->get('Excludes');

		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_ADD_EDIT_GROUP','rsfiles'));
		JToolBarHelper::apply('group.apply');
		JToolBarHelper::save('group.save');
		JToolBarHelper::save2new('group.save2new');
		JToolBarHelper::cancel('group.cancel');
	}
}