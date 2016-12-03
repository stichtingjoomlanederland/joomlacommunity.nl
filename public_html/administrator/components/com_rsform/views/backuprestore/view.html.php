<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewBackuprestore extends JViewLegacy
{
	public function display($tpl = null) {
		$this->addToolbar();
		
		// tabs
		$this->tabs		 = $this->get('RSTabs');
		// fields
		$this->form		 = $this->get('Form');
		$this->field	 = $this->get('RSFieldset');
		$this->sidebar 	 = $this->get('SideBar');
		
		$this->tempDir	= $this->get('TempDir');
		$this->writable = $this->get('isWritable');
		$this->forms	= $this->get('forms');
		
		$this->config = RSFormProConfig::getInstance();
		
		JFactory::getDocument()->addScript(JUri::root(true).'/administrator/components/com_rsform/assets/js/backup.js');
		
		if (!$this->writable) {
			JError::raiseWarning(500, JText::sprintf('RSFP_BACKUP_RESTORE_CANNOT_CONTINUE_WRITABLE_PERMISSIONS', '<strong>'.$this->escape($this->tempDir).'</strong>'));
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		// set title
		JToolBarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('backuprestore');
	}
}