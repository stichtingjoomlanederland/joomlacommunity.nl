<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewComment extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::sprintf('COM_RSCOMMENTS_EDIT_COMMENT', '<i>'.$this->item->subject.'</i> - '.RSCommentsHelperAdmin::showDate($this->item->date)),'rscomments');
		JToolbarHelper::apply('comment.apply');
		JToolbarHelper::save('comment.save');
		JToolbarHelper::cancel('comment.cancel');
	}
}