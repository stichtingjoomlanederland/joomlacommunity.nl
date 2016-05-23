<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewComment extends JViewLegacy
{
	protected $form;
	protected $item;

	public function display($tpl = null) {
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title(JText::sprintf('COM_RSCOMMENTS_EDIT_COMMENT', '<i>'.$this->item->subject.'</i> - '.RSCommentsHelper::showDate($this->item->date)),'rscomments');

		JToolBarHelper::apply('comment.apply');
		JToolBarHelper::save('comment.save');
		JToolBarHelper::cancel('comment.cancel');
	}
}