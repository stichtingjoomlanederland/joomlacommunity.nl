<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewMessages extends JViewLegacy {
	protected $messages;
	protected $pagination;

	public function display($tpl = null) {
		$this->items 			= $this->get('Items');
		$this->state 			= $this->get('State');
		$this->available_langs 	= $this->get('AvailableLanguages');

		$this->addToolbar();
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_MESSAGES_TITLE'),'rscomment');
		
		// add Menu in sidebar
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('messages');

		
		if(count($this->available_langs) > 0)
			JToolbarHelper::addNew('message.add');
		
		JToolbarHelper::preferences('com_rscomments');
	}
}