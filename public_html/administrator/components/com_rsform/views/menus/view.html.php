<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewMenus extends JViewLegacy
{
	public function display($tpl = null) {
		JToolbarHelper::title('RSForm! Pro','rsform');
		
		$lang = JFactory::getLanguage();
		$lang->load('com_rsform.sys', JPATH_ADMINISTRATOR);

        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&task=forms.manage', true);
        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&task=submissions.manage');
        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&task=configuration.edit');
        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_BACKUP_RESTORE'), 'index.php?option=com_rsform&task=backup.restore');
        JHtmlSidebar::addEntry(JText::_('COM_RSFORM_PLUGINS'), 'index.php?option=com_rsform&task=goto.plugins');
		
		$this->formId 		= JFactory::getApplication()->input->getInt('formId');
		$this->formTitle 	= $this->get('formtitle');
		$this->menus 		= $this->get('menus');
		$this->pagination 	= $this->get('pagination');
		
		parent::display($tpl);
	}
}