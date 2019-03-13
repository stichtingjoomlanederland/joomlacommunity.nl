<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewConfiguration extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('core.admin', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

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
	
	public function triggerEvent($event, $args=null)
	{
        JFactory::getApplication()->triggerEvent($event, $args);
	}
	
	protected function addToolbar() {
		// set title
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('configuration');
	}
}