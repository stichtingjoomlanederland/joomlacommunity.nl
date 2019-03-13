<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewRestore extends JViewLegacy
{
	public function display($tpl = null) {
        if (!JFactory::getUser()->authorise('backuprestore.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$this->addToolbar();

        JHtml::script('com_rsform/admin/restore.js', array('relative' => true, 'version' => 'auto'));
		
		$this->sidebar  	= $this->get('Sidebar');
		$this->key			= $this->get('Key');
		$this->overwrite	= $this->get('Overwrite');
		$this->keepId		= $this->get('KeepId');
		
		$this->config = RSFormProConfig::getInstance();
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		// set title
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('backuprestore');
	}
}