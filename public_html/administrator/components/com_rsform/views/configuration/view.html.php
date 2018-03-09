<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewConfiguration extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->addToolbar();
		
		JToolbarHelper::apply('configuration.apply');
		JToolbarHelper::save('configuration.save');
		JToolbarHelper::cancel('configuration.cancel');
		
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
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('configuration');
	}
}