<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewGroup extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $tabs;
	protected $used;
	
	public function display($tpl = null) {
		$this->tabs		 = $this->get('Tabs');
		$this->form  	 = $this->get('Form');
		$this->item		 = $this->get('Item');
		$this->used		 = $this->get('Used');
		$this->fieldsets = $this->form->getFieldsets('permissions');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		$this->item->IdGroup ? JToolBarHelper::title(JText::sprintf('COM_RSCOMMENTS_EDIT_GROUP',$this->item->GroupName),'rscomment') : JToolBarHelper::title(JText::_('COM_RSCOMMENTS_ADD_GROUP'),'rscomment');

		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('configuration');

		JToolBarHelper::apply('group.apply');
		JToolBarHelper::save('group.save');
		JToolBarHelper::cancel('group.cancel');
	}
}