<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewLicense extends JViewLegacy
{
	protected $form;
	protected $item;
	
	public function display($tpl = null) {
		$this->form 	= $this->get('Form');
		$this->item 	= $this->get('Item');
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_ADD_EDIT_LICENSE','rsfiles'));
		
		JToolBarHelper::apply('license.apply');
		JToolBarHelper::save('license.save');
		JToolBarHelper::save2new('license.save2new');
		JToolBarHelper::cancel('license.cancel');
	}
}