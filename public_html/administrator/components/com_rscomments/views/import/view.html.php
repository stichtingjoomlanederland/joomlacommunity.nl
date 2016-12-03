<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewImport extends JViewLegacy
{
	public function display($tpl = null) {
		$dispatcher	= JDispatcher::getInstance();

		// load the plugins
		JPluginHelper::importPlugin('rscomments');

		//register the plugins 
		$model = $this->getModel();
		$model->registerPlugins();

		//trigger the events
		$this->html = $dispatcher->trigger('rscommentsButton');

		// tabs
		$this->tabs		 	= $this->get('RSTabs');
		$this->form  		= $this->get('Form');
		$this->fieldsets 	= $this->form->getFieldsets();

		$this->addToolbar();
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';

		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title('RSComments!','rscomments');
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('import');

		JToolBarHelper::apply('import.save', JText::_('COM_RSCOMMENTS_IMPORT'));
		JToolBarHelper::preferences('com_rscomments');
	}
}