<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewRsfiles extends JViewLegacy
{
	public function display($tpl = null) {
		$layout				= $this->getLayout();
		$this->db			= JFactory::getDbo();
		$this->app			= JFactory::getApplication();
		$this->doc			= JFactory::getDocument();
		$this->user			= JFactory::getUser();
		$this->date			= JFactory::getDate();
		$this->session		= JFactory::getSession();
		$this->ds			= rsfilesHelper::ds();
		$this->config		= rsfilesHelper::getConfig();
		$this->itemid		= rsfilesHelper::getItemid();
		$this->params		= rsfilesHelper::getParams();
		$this->dld_fld		= $this->session->get('rsfilesdownloadfolder');
		$this->current		= $this->get('Current');
		$this->currentRel	= $this->get('CurrentRelative');
		$this->briefcase	= rsfilesHelper::isBriefcase();
		$this->return_page	= $this->get('ReturnPage');
		$uri				= JURI::getInstance();
		$this->base			= $uri->toString(array('scheme', 'host', 'port'));
		$pathway			= $this->app->getPathway();
		$menus				= $this->app->getMenu();
		$menu				= $menus->getActive();
		$this->listOrder	= $this->get('Order');
		$this->listDirn		= $this->get('OrderDir');
		$this->hash			= $this->app->input->getString('hash','');
		
		if ($layout != 'briefcase') {
			if (empty($this->config->download_folder) || !is_dir(realpath($this->config->download_folder))) {
				throw new Exception(JText::_('COM_RSFILES_NO_DOWNLOAD_FOLDER'),500);
			}
		}
		
		if ($layout == 'briefcase') {
			
			if (!$this->config->enable_briefcase) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_BRIEFCASE_DISABLED'), JRoute::_('index.php?option=com_rsfiles&layout=default',false));
			}
			
			if (empty($this->config->briefcase_folder) || !is_dir(realpath($this->config->briefcase_folder))) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_NO_BRIEFCASE_FOLDER'), JRoute::_('index.php?option=com_rsfiles&layout=default',false));
			}
			
			if ($this->user->get('id') == 0) {
				$this->app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($uri),false),JText::_('COM_RSFILES_PLEASE_LOGIN'),'error');
			}
			
			$this->items		= $this->get('BriefcaseFiles');
			$this->pagination	= $this->get('Pagination');
			$this->download		= rsfilesHelper::briefcase('CanDownloadBriefcase');
			$this->upload		= rsfilesHelper::briefcase('CanUploadBriefcase');
			$this->maintenance	= rsfilesHelper::briefcase('CanMaintainBriefcase');
			$this->delete		= rsfilesHelper::briefcase('CanDeleteBriefcase');
			$this->maxfilesize	= rsfilesHelper::getMaxFileSize();
			$this->maxfilessize	= rsfilesHelper::getMaxFilesSize();
			$this->maxfilesno	= rsfilesHelper::getMaxFilesNo();
			$this->folder	 	= rsfilesHelper::getFolder();
			$briefcase_root		= $this->maintenance ? $this->config->briefcase_folder : $this->config->briefcase_folder.$this->ds.$this->user->get('id');
			
			// Check if requested briefcase folder exits
			$current_path = $briefcase_root.$this->ds.$this->folder;
			
			if (!JFolder::exists($current_path)) {
				$this->app->redirect(JRoute::_('index.php?option=com_rsfiles&layout=briefcase',false));
			}
			
			// Get user root quota
			if ($this->maintenance && !empty($this->folder)) {
				$user_folder = explode($this->ds, $this->folder);
				$user_folder = (int) $user_folder[0];
				$user_briefcase = $this->config->briefcase_folder.$this->ds.$user_folder;
			} else {
				$user_briefcase = $this->config->briefcase_folder.$this->ds.$this->user->get('id');
			}
			
			if ($this->maintenance && strpos(realpath($briefcase_root.$this->ds.$this->folder), realpath($briefcase_root)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_BRIEFCASE'), JRoute::_('index.php?option=com_rsfiles&layout=briefcase',false));
			}
			
			if (!$this->maintenance) {
				$this->currentfolder = empty($this->folder) ? '' : $this->folder;
			} else {
				$current_folder = (!$this->maintenance && $this->upload) ? str_replace($this->config->briefcase_folder.$this->ds,'',rsfilesHelper::getBriefcase()) : $this->folder;
				$current_folder = rtrim($current_folder, $this->ds);
				$this->currentfolder = $current_folder;
			}

			$this->previous			= rsfilesHelper::getPrevious(true);
			$this->curentfilesno	= rsfilesHelper::getCurrentFilesNo();
			$this->briefcase_root	= $briefcase_root;
			$this->currentquota		= rsfilesHelper::getFoldersize($user_briefcase);
			$this->fdescription		= $this->get('FolderDescription');
			
			//set pathway
			if (!$menu) {
				$pathway->addItem(JText::_('COM_RSFILES_BC_BRIEFCASE'), '');
			}
			
		} else if ($layout == 'search') {
			
			if (!$this->config->show_search) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_SEARCH_DISABLED'), JRoute::_('index.php?option=com_rsfiles&layout=default',false));
			}
			
			$this->filter		= $this->app->input->getString('filter_search');
			$this->order		= $this->app->input->getString('rsfl_ordering');
			$this->order_dir	= $this->app->input->getString('rsfl_ordering_direction');
			$this->items		= $this->get('Results');
			
			//set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_SEARCH'), '');
		
		} else if ($layout == 'license') {
			
			$this->license		= $this->get('License');
			
		} else if ($layout == 'report') {
			
			if (!$this->config->show_report) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_REPORTS_DISABLED'), JRoute::_('index.php?option=com_rsfiles&layout=default',false));
			}
		
		} else if ($layout == 'agreement') {
			
			$this->license 		= $this->get('License');
			$this->item 		= $this->get('File');
			$this->path			= rsfilesHelper::getPath();
		
		} else if ($layout == 'validate') {
			
			if ($this->config->captcha_enabled == 0) {
				echo rsfilesHelper::modalClose();
				$this->app->close();
			}
			
			$this->path = rsfilesHelper::getPath();
			$this->return = base64_encode(JURI::getInstance());
			
			JText::script('COM_RSFILES_NO_CAPTCHA');
			
			$this->doc->addScript('https://www.google.com/recaptcha/api.js');
		
		} else if ($layout == 'email') {
		
			$this->path = rsfilesHelper::getPath();
			$this->item	= $this->get('File');
			$this->doc->addScript('https://www.google.com/recaptcha/api.js');
			
			JText::script('COM_RSFILES_CHECK_AGREEMENT');
			JText::script('COM_RSFILES_NO_CAPTCHA');
		
		} else if ($layout == 'bookmarks') {
			
			if (!$this->config->show_bookmark) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_BOOKMARKS_DISABLED'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$this->items = $this->get('Bookmarks');
			
			//set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_BOOKMARKS'), '');
			
		} else if ($layout == 'edit') {
			$this->path	= rsfilesHelper::getPath();
			$this->item	= $this->get('File');
			
			if ($this->briefcase) {
				$root_path = rsfilesHelper::getBriefcase();
				$canedit = rsfilesHelper::briefcase('CanMaintainBriefcase') ? rsfilesHelper::briefcase('CanMaintainBriefcase') : rsfilesHelper::briefcase('CanUploadBriefcase');
			} else {
				$root_path = $this->dld_fld;
				$checkPath = empty($this->currentRel) ? $this->path : $this->currentRel.rsfilesHelper::ds().$this->path;
				$canedit = rsfilesHelper::permissions('CanEdit',$checkPath) || (rsfilesHelper::briefcase('editown') && $this->item->IdUser == $this->user->get('id'));
			}
			
			$current_path		= $this->current.$this->ds.$this->path;
			
			if (!rsfilesHelper::external($this->path) && strpos(realpath($current_path), realpath($root_path)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_EDIT_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Check to see if the user has permissions to edit
			if (!$canedit) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_EDIT_DENIED'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$this->form		= $this->get('Form');
			$this->state	= $this->get('States');
			$this->yesno	= $this->get('YesNo');
			
			//set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_EDIT'), '');
			
		} else if ($layout == 'create') {
			$this->folder 	= rsfilesHelper::getFolder();
			
			if ($this->briefcase) {
				$upload		= rsfilesHelper::briefcase('CanUploadBriefcase');
				$maintain	= rsfilesHelper::briefcase('CanMaintainBriefcase');

				$user_folder = explode($this->ds,$this->folder);
				if (empty($folder)) {
					$user_folder[0] = $this->user->get('id');
					$ext = $user_folder[0];
				}

				// Check to see if the user has permissions to create a new folder
				if (!$upload && !$maintain)
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_CREATE_FOLDER'), JRoute::_('index.php?option=com_rsfiles',false));
				
				if (!$maintain && $user_folder[0] != $this->user->get('id'))
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_CREATE_FOLDER'), JRoute::_('index.php?option=com_rsfiles',false));
				
				if (JFile::stripExt($ext) == '')
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_CREATE_FOLDER'), JRoute::_('index.php?option=com_rsfiles',false));
			} else {
				// Check to see if the user has permissions to create a new folder
				$thepath = empty($this->currentRel) ? 'root_rs_files' : $this->currentRel;
				if (!rsfilesHelper::permissions('CanCreate',$thepath)) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_CREATE_FOLDER'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			}
		} else if ($layout == 'upload') {
			
			if ($this->config->enable_upload == 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_UPLOAD_DENIED'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$this->folder 	= rsfilesHelper::getFolder();
			
			if ($this->briefcase) {
				$upload 	 	  = rsfilesHelper::briefcase('CanUploadBriefcase');
				$maintenance 	  = rsfilesHelper::briefcase('CanMaintainBriefcase');
				$root			  = $this->config->briefcase_folder;
				
				if ($maintenance) {
					$user_folder 	  = !empty($this->folder) ? current(explode($this->ds,$this->folder)) : $this->user->get('id');
				} else {
					$user_folder 	  = $this->user->get('id');
				}
				
				// Check for maximum quota and maximum upload files
				$this->maxfilessize		= rsfilesHelper::getMaxFilesSize();
				$this->maxfilesno		= rsfilesHelper::getMaxFilesNo();
				$this->curentfilesno	= rsfilesHelper::getCurrentFilesNo();
				$this->currentquota		= rsfilesHelper::getFoldersize($root.$this->ds.$user_folder);
				
				if ($this->maxfilesno <= $this->curentfilesno) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_MAX_FILES_REACHED'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				if ($this->maxfilessize <= $this->currentquota) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_MAX_QUOTA_REACHED'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				// Check to see if it is out of the root 
				if ($maintenance && strpos(realpath($root.$this->ds.$this->folder), realpath($root)) !== 0) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_BRIEFCASE'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				// Check to see if the user uploads in his folder
				if (!$maintenance && ($user_folder != $this->user->get('id'))) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_UPLOAD_IN_FOLDER'), JRoute::_('index.php?option=com_rsfiles',false));
				}

				// Check to see if the user has permission
				if (!$upload && !$maintenance) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_UPLOAD_DENIED'), JRoute::_('index.php?option=com_rsfiles',false));
				}

				$this->root_folder	= $root;
			} else {
				// Check to see if the user has permissions to upload
				$thepath = empty($this->currentRel) ? 'root_rs_files' : $this->currentRel;
				if (!rsfilesHelper::permissions('CanUpload',$thepath)) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_UPLOAD_DENIED'), JRoute::_('index.php?option=com_rsfiles',false));
				}

				$this->root_folder	= $this->session->get('rsfilesdownloadfolder');
			}
			
			if (rsfilesHelper::isJ3()) {
				JHtml::_('jquery.ui');
			} else {
				$this->doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.ui.js?v='.RSF_RS_REVISION);
			}
			
			$this->doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.iframe-transport.js?v='.RSF_RS_REVISION);
			$this->doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.fileupload.js?v='.RSF_RS_REVISION);
			$this->doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.fileupload-process.js?v='.RSF_RS_REVISION);
			$this->doc->addScript(JURI::root(true).'/components/com_rsfiles/assets/js/jquery.script.js?v='.RSF_RS_REVISION);
			
			$this->previous 	= rsfilesHelper::getFolder();
			
			$uri = JUri::getInstance();
			$uri->setVar('single',1);
			$this->singleupload = $uri->toString();
			$this->single = $this->app->input->getInt('single',0);
			
			// Safari on Windows has a bug and reports a file size of 0 bytes when selecting multiple files.
			if (rsfilesHelper::isSafariWin()) {
				$this->single = 1;
			}
			
			//set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_UPLOAD'), '');
			
		} else if ($layout == 'download') {
			
			// Get path
			$this->path			= rsfilesHelper::getPath();
			
			// Get file
			$this->file	= $this->get('File');
			
			if ($this->briefcase) {
				$candelete		= rsfilesHelper::briefcase('CanDeleteBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$candownload	= rsfilesHelper::briefcase('CanDownloadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$canupload		= rsfilesHelper::briefcase('CanUploadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$canview		= 1;
				$canedit		= $canupload;
				$root_path		= $this->config->briefcase_folder;
				
				if (!$candownload) 
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_DOWNLOAD'), JRoute::_('index.php?option=com_rsfiles',false));
				
			} else {
				$checkPath		=  empty($this->currentRel) ? $this->path : $this->currentRel.rsfilesHelper::ds().$this->path;
				$candelete		= rsfilesHelper::permissions('CanDelete',$checkPath) || (rsfilesHelper::briefcase('deleteown') && $this->file->IdUser == $this->user->get('id'));
				$candownload	= rsfilesHelper::permissions('CanDownload',$checkPath);
				$canupload		= rsfilesHelper::permissions('CanUpload',$checkPath);
				$canview		= rsfilesHelper::permissions('CanView',$checkPath);
				$canedit		= rsfilesHelper::permissions('CanEdit',$checkPath) || (rsfilesHelper::briefcase('editown') && $this->file->IdUser == $this->user->get('id'));
				$root_path		= $this->dld_fld;
				
				// Check for view permissions
				if (!$canview) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_VIEW'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			}
			
			// Do we have a valid file
			if (empty($this->file)) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_FIND_FILE'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Check for publishing permission
			$published = rsfilesHelper::published($this->file->FileType ? $this->path : $root_path.$this->ds.$this->path);
			
			if (!$this->briefcase) {
				if ($this->hash) {
					if (!$published) {
						if (rsfilesHelper::checkHash()) {
							$published = true;
						}
					}
				}
			}
			
			if (!$published) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_FILE_UNPUBLISHED'), JRoute::_('index.php?option=com_rsfiles',false));
			}
		
			// Outside of the folder root
			$current_path = $this->current.$this->ds.$this->path;
			
			if (!$this->file->FileType && strpos(realpath($current_path), realpath($root_path)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$ext = explode($this->ds,$this->path);
			
			if (!$this->file->FileType && JFile::stripExt(end($ext)) == '') {
				rsfilesHelper::errors(JText::_('COM_RSFILES_NO_EXTENSION'), JRoute::_('index.php?option=com_rsfiles',false));
			}
				
			if (!empty($this->file->publish_down) && $this->file->publish_down != $this->db->getNullDate() && $this->date->toUnix() > JFactory::getDate($this->file->publish_down)->toUnix()) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_VIEW'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Set metadata
			rsfilesHelper::metadata($this->file);
			
			// Update hits
			rsfilesHelper::hits($root_path.$this->ds.$this->path, $this->file->FilePath);
		
			$this->candelete	= $candelete;
			$this->candownload	= $candownload;
			$this->canupload	= $canupload;
			$this->canview		= $canview;
			$this->canedit		= $canedit;
			
			$instance = RSFiles::getInstance($this->file->FileType ? $this->file->IdFile : $this->file->fullpath);
			$element = $instance->info;
			
			// Create the download link
			$this->download		= rsfilesHelper::downloadlink($this->file, $this->path);
			$this->mirrors		= $instance->mirrors;
			$this->screenshots	= $instance->screenshots;
			
			// Set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_DOWNLOAD'), '');
			
			$previous 			= $this->file->FileType ? $this->file->FileParent : rsfilesHelper::getPreviousPath();
			$previous 			= str_replace($root_path,'',$previous);
			$previous 			= ltrim($previous,$this->ds);
			$this->previous		= empty($previous) ? '' : '&folder='.$previous;
			$this->from			= $this->briefcase ? '&from=briefcase' : '';
		
		} else if ($layout == 'details') {
			// Get path
			$this->path	= rsfilesHelper::getPath();
			
			// Get file
			$this->file	= $this->get('File');
			
			if ($this->briefcase) {
				$canview		= 1;
				$canedit		= rsfilesHelper::briefcase('CanUploadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$candownload	= rsfilesHelper::briefcase('CanDownloadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$candelete		= rsfilesHelper::briefcase('CanDeleteBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
				$root_path		= $this->config->briefcase_folder;
				
				if (!$candownload) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_VIEW'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			} else {
				$checkPath		=  empty($this->currentRel) ? $this->path : $this->currentRel.rsfilesHelper::ds().$this->path;
				$canview		= rsfilesHelper::permissions('CanView',$checkPath);
				$canedit		= rsfilesHelper::permissions('CanEdit',$checkPath) || (rsfilesHelper::briefcase('editown') && $this->file->IdUser == $this->user->get('id'));
				$candelete		= rsfilesHelper::permissions('CanDelete',$checkPath) || (rsfilesHelper::briefcase('deleteown') && $this->file->IdUser == $this->user->get('id'));
				$root_path		= $this->dld_fld;				
				
				// Check for view permissions
				if (!$canview) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_VIEW'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			}
			
			// Do we have a valid file
			if (empty($this->file)) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_FIND_FILE'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Check for publishing permission
			$published = rsfilesHelper::published($this->file->FileType ? $this->path : $root_path.$this->ds.$this->path);
			
			if (!$this->briefcase) {
				if ($this->hash) {
					if (!$published) {
						if (rsfilesHelper::checkHash()) {
							$published = true;
						}
					}
				}
			}
			
			if (!$published) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_FILE_UNPUBLISHED'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Outside of the folder root
			$current_path = $this->current.$this->ds.$this->path;
			
			if (!$this->file->FileType && strpos(realpath($current_path), realpath($root_path)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$ext = explode($this->ds,$this->path);
			
			if (!$this->file->FileType && JFile::stripExt(end($ext)) == '') {
				rsfilesHelper::errors(JText::_('COM_RSFILES_NO_EXTENSION'), JRoute::_('index.php?option=com_rsfiles',false));
			}
				
			if (!empty($this->file->publish_down) && $this->file->publish_down != $this->db->getNullDate() && $this->date->toUnix() > JFactory::getDate($this->file->publish_down)->toUnix()) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_VIEW'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Set metadata
			rsfilesHelper::metadata($this->file);
			
			// Update hits
			rsfilesHelper::hits($root_path.$this->ds.$this->path, $this->file->FilePath);
			
			$this->candelete	= $candelete;
			$this->canedit		= $canedit;
			
			// Set pathway
			$pathway->addItem(JText::_('COM_RSFILES_BC_DETAILS'), '');
			
			$previous 			= $this->file->FileType ? $this->file->FileParent : rsfilesHelper::getPreviousPath();
			$previous 			= str_replace($root_path,'',$previous);
			$previous 			= ltrim($previous,$this->ds);
			$this->previous		= empty($previous) ? '' : '&folder='.$previous;
			$this->from			= $this->briefcase ? '&from=briefcase' : '';
			
		} else if ($layout == 'preview') {
			
			$path = rsfilesHelper::getPath();
			
			if ($this->briefcase) {
				$maintenance	= rsfilesHelper::briefcase('CanMaintainBriefcase');
				$candownload	= $maintenance ? $maintenance : rsfilesHelper::briefcase('CanDownloadBriefcase');
				$root			= $this->config->briefcase_folder;
				$canview		= false;
				
				if (!$maintenance) {
					$path = $this->user->get('id').$this->ds.$path;
				}
				
				if (!empty($path)) {
					$thepath = explode($this->ds,$path);
					if ($thepath[0] == $this->user->get('id'))
						$canview = true;
				}
				
				if ($maintenance) {
					$canview = true;
				}
				
				if (strpos(realpath($root.$this->ds.$path), realpath($root)) !== 0 || !$canview) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				if (!$candownload) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_NO_DOWNLOAD'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			} else {
				$checkPath = empty($this->currentRel) ? $path : $this->currentRel.rsfilesHelper::ds().$path;
				if (!rsfilesHelper::permissions('CanDownload',$checkPath)) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_NO_DOWNLOAD'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				if (!rsfilesHelper::external($path) && strpos(realpath($this->dld_fld.$this->ds.$path), realpath($this->dld_fld)) === FALSE) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
				}
				
				if (!rsfilesHelper::published(realpath($this->current.$this->ds.$path))) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_AN_UNPUBLISHED_FILE'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			}
			
			$this->file = $this->get('File');
			if (!empty($this->file->publish_down) && $this->file->publish_down != $this->db->getNullDate() && $this->date->toUnix() > JFactory::getDate($this->file->publish_down)->toUnix())
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_PUBLISHED_DOWN'), JRoute::_('index.php?option=com_rsfiles',false));
			
			if (!$this->file->show_preview) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_PREVIEW_NO_PREVIEW'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			$this->get('Preview');
		} else if ($layout == 'plugin') {
			// Plugin layout
		} else {
			$this->current_folder = rsfilesHelper::getFolder();
			
			// Check for publishing permission
			if (!empty($this->current_folder)) {
				$published = rsfilesHelper::published(realpath($this->dld_fld.$this->ds.$this->current_folder));

				if (!$published) {
					rsfilesHelper::errors(JText::_('COM_RSFILES_FOLDER_NOT_FOUND'), JRoute::_('index.php?option=com_rsfiles',false));
				}
			}
			
			// Check if we can view this content
			$checkPath =  empty($this->currentRel) ? $this->current_folder : $this->currentRel;
			if (!rsfilesHelper::permissions('CanView',$checkPath)) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_VIEW_PERMISSION_ERROR'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Check if the user is outside the root
			if (strpos(realpath($this->current), realpath($this->dld_fld)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
			}

			// Check to see if the current path is a folder
			if (!is_dir(realpath($this->dld_fld.$this->ds.$this->current_folder))) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_NOT_A_DIRECTORY'), JRoute::_('index.php?option=com_rsfiles',false));
			}
			
			// Set pathway
			if (!$menu) {
				$pathway->addItem(JText::_('COM_RSFILES_BC_FILES'));
			}
			
			// Add feed links
			if ($this->config->enable_rss == 1) {
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				$this->doc->addHeadLink(JRoute::_('&format=feed&type=rss'), 'alternate', 'rel', $attribs);
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				$this->doc->addHeadLink(JRoute::_('&format=feed&type=atom'), 'alternate', 'rel', $attribs);
			}
			
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');
			$this->fdescription	= $this->get('FolderDescription');
			$this->navigation	= rsfilesHelper::getNavigation();
			$this->previous		= rsfilesHelper::getPrevious(true);
		}
	
		parent::display($tpl);
	}
}