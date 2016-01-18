<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

class rsfilesViewFiles extends JViewLegacy
{
	protected $sidebar;
	protected $filterbar;
	
	public function display($tpl = null) {
		$layout					= $this->getLayout();
		$app					= JFactory::getApplication();
		$this->config			= rsfilesHelper::getConfig();
		$this->root				= rsfilesHelper::getRoot();
		$this->briefcase		= $this->root == 'briefcase';
		
		if ($layout == 'form') {
			$this->form		= $this->get('Form');
			$this->path		= $app->input->getString('path','');
			$this->single	= $app->input->getInt('single',0);
			
			// Safari on Windows has a bug and reports a file size of 0 bytes when selecting multiple files.
			if (rsfilesHelper::isSafariWin()) {
				$this->single = 1;
			}
			
			$uri = JUri::getInstance();
			$uri->setVar('single',1);
			$this->singleupload = $uri->toString();
			
			if ((!empty($this->path) && strpos($this->path, $this->config->{$this->root.'_folder'}) !== 0) || empty($this->config->{$this->root.'_folder'})) {
				echo rsfilesHelper::modalClose();
				$app->close();
			}
			
			$this->addScripts();
		} else {
			$this->folder			= $app->input->getString('folder','');
			$this->from				= $app->input->getString('from','');
			$this->sidebar			= $this->get('Sidebar');
			$this->filterbar		= $this->get('Filterbar');
			$this->current 			= $this->get('Current');
			
			$this->items			= $this->get('Data');
			$this->total			= $this->get('Total');
			$this->pagination		= $this->get('Pagination');
			$this->navigation		= $this->get('Navigation');
			$this->form				= $this->get('BatchForm');
			
			$this->addToolBar();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSFILES_FILES'),'rsfiles');
		
		if ($this->root != 'briefcase' || $this->current != $this->config->briefcase_folder) {
			JToolBarHelper::custom('upload', 'upload', 'upload', JText::_('COM_RSFILES_UPLOAD_FILES'), false);
			
			if ($this->root != 'briefcase')
				JToolBarHelper::addNew('file.add');
		}
		
		// In the main briefcase folder
		if ($this->current == $this->config->briefcase_folder) {
			JToolBarHelper::addNew('briefcase.add');
		}
		
		JToolBarHelper::editList('file.edit');
		JToolBarHelper::deleteList('COM_RSFILES_FILES_DELETE_INFO','files.delete');
		JToolBarHelper::publishList('files.publish');
		JToolBarHelper::unpublishList('files.unpublish');
		
		if ($this->root != 'briefcase') {
			JToolBarHelper::custom('synchronize','synchronize','synchronize',JText::_('COM_RSFILES_SYNCHRONIZE_FILES'),false);
			JToolBarHelper::custom('files.statistics','statistics32','statistics32',JText::_('COM_RSFILES_ENABLE_STATISTICS'),true);
		}
		
		if ($this->root != 'briefcase') {
			if (rsfilesHelper::isJ3()) {
				JHtml::_('bootstrap.modal', 'batchfiles');
				$custom = '<button data-toggle="modal" data-target="#batchfiles" class="btn btn-small"><i class="icon-checkbox-partial" title="'.JText::_('COM_RSFILES_BATCH').'"></i> '.JText::_('COM_RSFILES_BATCH').'</button>';
			} else {
				$doc = JFactory::getDocument();
				$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery-1.11.1.min.js');
				$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.noConflict.js');
				$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/bootstrap.modal.js');
				$doc->addStyleSheet(JURI::root(true).'/components/com_rsfiles/assets/css/bootstrap.modal.css');
				$doc->addScriptDeclaration("(function($){ $('#batchfiles').modal({\"backdrop\": true,\"keyboard\": true,\"show\": true,\"remote\": \"\"}); }) (jQuery);");
				
				$custom = '<a class="toolbar" href="javascript:void(0)" data-toggle="modal" data-target="#batchfiles"><span class="icon-32-list"></span>'.JText::_('COM_RSFILES_BATCH').'</a>';
			}
			
			JToolBar::getInstance()->appendButton('custom',$custom);
		}
		
		JToolBarHelper::custom('rsfiles','rsfiles32','rsfiles32',JText::_('COM_RSFILES'),false);
	}
	
	protected function addScripts() {
		$doc = JFactory::getDocument();
		
		if (rsfilesHelper::isJ3()) {
			JHtml::_('jquery.ui');
		} else {
			$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.ui.js?v='.RSF_RS_REVISION);
		}
		
		$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.iframe-transport.js?v='.RSF_RS_REVISION);
		$doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.fileupload.js?v='.RSF_RS_REVISION);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsfiles/assets/js/jquery.fileupload-process.js?v='.RSF_RS_REVISION);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsfiles/assets/js/jquery.script.js?v='.RSF_RS_REVISION);
	}
}