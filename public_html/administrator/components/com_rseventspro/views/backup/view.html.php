<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewBackup extends JViewLegacy
{	
	public function display($tpl = null) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/backup.php';
		
		$backup			 = new RSEBackup;
		$this->files	 = $backup->getBackups();
		$this->hash		 = JFactory::getApplication()->input->getString('hash');
		$this->overwrite = JFactory::getApplication()->input->getInt('overwrite',0);
		$this->tabs		 = new RSTabs('backuprestore');
		$this->sidebar	 = rseventsproHelper::isJ3() ? JHtmlSidebar::render() : '';
		
		JText::script('COM_RSEVENTSPRO_BACKUP_OVERWRITE_RESTORE');
		JText::script('COM_RSEVENTSPRO_BACKUP_RESTORE');
		JText::script('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN');
		
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_BACKUP_RESTORE_TITLE'),'rseventspro48');
		parent::display($tpl);
	}
}