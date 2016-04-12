<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewRestore extends JViewLegacy
{
	public function display($tpl = null) {
		$this->addToolbar();
		
		JFactory::getDocument()->addScript(JUri::root(true).'/administrator/components/com_rsform/assets/js/restore.js');
		
		$this->sidebar  	= $this->get('Sidebar');
		$this->key			= $this->get('Key');
		$this->overwrite	= $this->get('Overwrite');
		$this->keepId		= $this->get('KeepId');
		
		$this->config = RSFormProConfig::getInstance();
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		// set title
		JToolBarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('backuprestore');
	}
}