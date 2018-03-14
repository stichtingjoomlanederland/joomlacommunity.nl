<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewImport extends JViewLegacy
{
	public function display($tpl = null) {
		JPluginHelper::importPlugin('rscomments');

		$this->tabs		 	= $this->get('RSTabs');
		$this->form  		= $this->get('Form');
		$this->fieldsets 	= $this->form->getFieldsets();
		$this->html			= JFactory::getApplication()->triggerEvent('rscommentsButton');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title('RSComments!','rscomments');
		JToolbarHelper::apply('import.save', JText::_('COM_RSCOMMENTS_IMPORT'));
		JToolbarHelper::preferences('com_rscomments');
	}
}