<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewSettings extends JViewLegacy
{
	protected $form;
	protected $fieldsets;
	protected $tabs;
	protected $layouts;
	protected $config;
	
	public function display($tpl = null) {
		$layout				= $this->getLayout();
		$this->config		= $this->get('Config');
		
		if ($layout == 'select') {
			$this->type			= JFactory::getApplication()->input->get('type','');
			$this->elements		= $this->get('Elements');
			$this->folders		= $this->get('Folders');
			$this->previous		= $this->get('Previous');
		} else {
			$this->form			= $this->get('Form');
			$this->tabs			= $this->get('Tabs');
			$this->layouts		= $this->get('Layouts');
			$this->fieldsets	= $this->form->getFieldsets();
			$this->sidebar		= rsfilesHelper::isJ3() ? JHtmlSidebar::render() : '';
			
			$this->addToolBar();
		}
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_SETTINGS'), 'rsfiles');
		
		JToolBarHelper::apply('settings.apply');
		JToolBarHelper::save('settings.save');
		JToolBarHelper::cancel('settings.cancel');
		JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
	}
}