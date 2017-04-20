<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewEmoticons extends JViewLegacy
{
	public function display($tpl = null) {
		$this->addToolbar();
		
		$this->items 		= $this->get('Items');
		$this->sidebar 		= $this->get('SideBar');
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title(JText::_('COM_RSCOMMENTS_EMOTICONS'), 'rscomment');

		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('emoticons');
		
		JToolbarHelper::addNew('emoticons.add');
		JToolbarHelper::preferences('com_rscomments');
		
		JFactory::getDocument()->addScript(JURI::root(true).'/administrator/components/com_rscomments/assets/js/scripts.js');
	}
}