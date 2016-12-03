<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RSFormViewConfiguration extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->addToolbar();
		
		JToolBarHelper::apply('configuration.apply');
		JToolBarHelper::save('configuration.save');
		JToolBarHelper::cancel('configuration.cancel');
		
		// tabs
		$this->tabs		 = $this->get('RSTabs');
		// fields
		$this->field	 = $this->get('RSFieldset');
		// form
		$this->form		 = $this->get('Form');
		$this->fieldsets = $this->form->getFieldsets();
		
		$this->sidebar	= $this->get('SideBar');
		
		parent::display($tpl);
	}
	
	function triggerEvent($event, $args=null)
	{
		$mainframe = JFactory::getApplication();
		$mainframe->triggerEvent($event, $args);
	}
	
	protected function addToolbar() {
		// set title
		JToolBarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('configuration');
	}
}