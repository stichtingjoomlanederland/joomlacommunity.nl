<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewMessage extends JViewLegacy
{
	protected $form;
	protected $item;
	
	public function display($tpl = null) {
		$this->tabs		 	= $this->get('RSTabs');
		$this->form  		= $this->get('Form');
		$this->fieldsets 	= $this->form->getFieldsets();
		$this->item  		= $this->get('Item');
		$this->languages	= $this->get('Languages');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		if(empty($this->item->tag))
			JToolBarHelper::title(JText::_('COM_RSCOMMENTS_ADD_NEW_MESSAGE_FOR_LANGUAGE'),'rscomments');
		else 
			JToolBarHelper::title(JText::sprintf('COM_RSCOMMENTS_EDIT_MESSAGE_FOR_LANGUAGE',$this->languages[$this->item->tag]['name']),'rscomments');
		
		JToolBarHelper::apply('message.apply');
		JToolBarHelper::save('message.save');
		JToolBarHelper::cancel('message.cancel');
	}
}