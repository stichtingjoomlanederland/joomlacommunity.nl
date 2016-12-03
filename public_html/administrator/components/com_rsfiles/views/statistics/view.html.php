<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewStatistics extends JViewLegacy
{
	protected $sidebar;
	protected $filterbar;
	
	public function display($tpl = null) {
		$layout				= $this->getLayout();
		$this->filterbar	= $this->get('Filterbar');
		$this->sidebar		= $this->get('Sidebar');
		$this->return		= base64_encode(JURI::getInstance());
		
		if ($layout == 'view') {
			$this->items		= $this->get('Statistics');
			$this->pagination	= $this->get('sPagination');
			$this->filepath		= $this->get('Filepath');
		} else {
			$this->items 		= $this->get('Data');
			$this->pagination	= $this->get('Pagination');
		}
		
		$this->addToolBar($layout);
		parent::display($tpl);
	}
	
	protected function addToolBar($layout) {
		if ($layout == 'view') {
			JToolBarHelper::title(JText::sprintf('COM_RSFILES_VIEW_STATISTIC_FOR',$this->filepath),'rsfiles');
			JToolBarHelper::deleteList('COM_RSFILES_STATISTICS_CONFIRM_DELETE','statistics.delete','COM_RSFILES_STATISTICS_DELETE');
			JToolBarHelper::cancel('statistics.cancel');
		} else {
			JToolBarHelper::title(JText::_('COM_RSFILES_STATISTICS'),'rsfiles');
			JToolBarHelper::deleteList('COM_RSFILES_STATISTICS_DEFAULT_CONFIRM_DELETE','statistics.delete','COM_RSFILES_STATISTICS_DELETE');
			JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
		}
	}
}