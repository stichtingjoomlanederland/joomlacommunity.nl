<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewMessages extends JViewLegacy {

	public function display($tpl = null) {
		$this->items 			= $this->get('Items');
		$this->state 			= $this->get('State');
		$this->available_langs 	= $this->get('AvailableLanguages');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_MESSAGES_TITLE'),'rscomment');
		if (count($this->available_langs)) JToolbarHelper::addNew('message.add');
		JToolbarHelper::preferences('com_rscomments');
	}
}