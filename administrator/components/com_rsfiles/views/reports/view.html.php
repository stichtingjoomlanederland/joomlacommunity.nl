<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewReports extends JViewLegacy
{
	public function display($tpl = null) {
		$app	= JFactory::getApplication();
		$layout	= $this->getLayout();
		
		if ($layout == 'view') {
			$id = $app->input->getString('id','');
			if (empty($id)) {
				echo rsfilesHelper::modalClose();
				$app->close();
			}
			
			$this->data				= $this->get('Report');
		} else {
			$id = $app->input->getString('id','');
			if (empty($id))
				$app->redirect('index.php?option=com_rsfiles&view=files');
			
			$this->data				= $this->get('Data');
			$this->pagination		= $this->get('Pagination');
			$this->sidebar			= $this->get('Sidebar');
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_REPORTS'),'rsfiles');
		JToolBarHelper::deleteList('','reports.delete');
		JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
	}
}