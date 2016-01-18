<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

class rsfilesModelRsfiles extends JModelLegacy
{
	protected $_total 		= null;
	protected $_pagination 	= null;
	protected $_folder 		= null;
	protected $input 		= null;
	protected $ds 			= null;

	// Main controller
	public function __construct() {
		parent::__construct();
		
		$this->ds		= rsfilesHelper::ds();
		$this->input	= JFactory::getApplication()->input;
		
		// Set the root
		$this->setRoot();
		
		// Set the folder
		$this->setFolder();
		
		// Set statistics
		rsfilesHelper::statistics($this->absoluteFolder,$this->relativeFolder);
		
		// Set limit and limitstart
		$limit		= rsfilesHelper::getConfig('nr_per_page');
		$limitstart	= $this->input->getInt('limitstart',0);
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('com_rsfiles.'.$this->input->get('layout').'.limit', $limit);
		$this->setState('com_rsfiles.'.$this->input->get('layout').'.limitstart', $limitstart);
	}
	
	// Get the folders / files list
	public function getItems() {
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
		
		$class		= new RSFilesFiles($this->absoluteFolder, 'site', rsfilesHelper::getItemid(),0,$this->getOrder(), $this->getOrderDir());
		$files		= $class->getFiles();
		$folders	= $class->getFolders();
		$external	= $class->getExternal();
		
		$data = array_merge($folders,$files,$external);
		$this->_total = count($data);

		// Adjust the size of the list
		if ($this->input->get('format','') != 'feed') {
			$data = array_slice($data,$this->getState('com_rsfiles.'.$this->input->get('layout').'.limitstart'),$this->getState('com_rsfiles.'.$this->input->get('layout').'.limit'));
		}

		return $data;
	}
	
	// Get search results
	public function getResults() {
		$search		= $this->input->getString('filter_search');
		$itemid		= rsfilesHelper::getItemid();
		$dld_fld	= JFactory::getSession()->get('rsfilesdownloadfolder');
		
		if (empty($search)) 
			return;
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
		
		$theclass	= new RSFilesFiles($dld_fld,'site',$itemid);
		$files		= $theclass->getFiles();
		$folders	= $theclass->getFolders();
		$external	= $theclass->getExternal();
		
		return array_merge($folders,$files,$external);
	}
	
	// Get total number of files
	public function getTotal() {
		return $this->_total;
	}
	
	// Get the pagination
	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsfiles.'.$this->input->get('layout').'.limitstart'), $this->getState('com_rsfiles.'.$this->input->get('layout').'.limit'));
		}
		return $this->_pagination;
	}
	
	public function getOrder() {
		$params	= rsfilesHelper::getParams();

		return JFactory::getApplication()->getUserStateFromRequest('com_rsfiles.filter_order', 'filter_order', $params->get('order','name'));
	}
	
	public function getOrderDir() {
		$params		= rsfilesHelper::getParams();
		$direction	= strtoupper($params->get('order_way','desc'));
		
		return strtoupper(JFactory::getApplication()->getUserStateFromRequest('com_rsfiles.filter_order_Dir', 'filter_order_Dir', $direction));
	}
	
	// Get the current absolute folder path
	public function getCurrent() {
		return $this->absoluteFolder;
	}
	
	// Get the current relative folder path
	public function getCurrentRelative() {
		return $this->relativeFolder;
	}
	
	// Method to get the edit form info.
	public function getForm() {
		jimport('joomla.form.form');
		
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_rsfiles/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_rsfiles/models/fields');
		
		// Get a new instance of the edit form
		$form = JForm::getInstance('com_rsfiles.file', 'file', array('control' => 'jform'));
		
		// Get data
		$data = $this->getFile();
		
		// Bind data
		$form->bind($data);
		
		if (isset($data->FileType)) {
			$form->setFieldAttribute('FilePath','required','true');
			$form->setFieldAttribute('FilePath','readonly','false');
		}
		
		if (empty($data->publish_down) || $data->publish_down == JFactory::getDbo()->getNullDate())
			$form->setValue('publish_down',null,'');
		
		return $form;
	}
	
	// Save the file
	public function save($data) {
		// Initialise variables;
		$table	= JTable::getInstance('File','rsfilesTable');
		$user 	= JFactory::getUser();
		$pk		= (!empty($data['IdFile'])) ? $data['IdFile'] : (int) $this->getState($this->getName() . '.id');
		$isNew	= true;
		
		// Load the row if saving an existing tag.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		
		if (rsfilesHelper::isBriefcase()) {
			$canedit = rsfilesHelper::briefcase('CanUploadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
		} else {
			$canedit = rsfilesHelper::permissions('CanEdit',$table->FilePath) || (rsfilesHelper::briefcase('editown') && $table->IdUser == $user->get('id'));
		}
		
		if (!$canedit) {
			$this->setError(JText::_('COM_RSFILES_CANNOT_SAVE'));
			return false;
		}
		
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		rsfilesHelper::upload($table->IdFile);
		
		$this->setState($this->getName() . '.id', $table->IdFile);
		
		return true;
	}
	
	// Get file details
	public function getFile() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$now		= JFactory::getDate()->toSql();
		$path		= rsfilesHelper::getPath();
		$config		= rsfilesHelper::getConfig();
		$briefcase	= rsfilesHelper::isBriefcase();
		$fullpath	= $this->absoluteRoot.$this->ds.$path;
		
		$query->select($db->qn('f').'.*')->select($db->qn('l.LicenseName'))
			->select($db->qn('u.username'))->select($db->qn('u.name'))
			->from($db->qn('#__rsfiles_files','f'))
			->join('LEFT',$db->qn('#__rsfiles_licenses','l').' ON '.$db->qn('f.IdLicense').' = '.$db->qn('l.IdLicense'))
			->join('LEFT',$db->qn('#__users','u').' ON '.$db->qn('f.IdUser').' = '.$db->qn('u.id'));
		
		if (rsfilesHelper::external($path)) {
			$query->where($db->qn('f.IdFile').' = '.(int) $path);
		} else {
			$filePath = $briefcase ? str_replace($config->briefcase_folder, '', $fullpath) : str_replace($config->download_folder, '', $fullpath);
			$filePath = trim($filePath,$this->ds);
			$query->where($db->qn('f.FilePath').' = '.$db->q($filePath));
		}
		
		$db->setQuery($query);
		$file = $db->loadObject();
		
		if (empty($file) && is_file($fullpath)) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rsfiles/tables');
		
			$file					= JTable::getInstance('File', 'rsfilesTable');
			$file->FilePath 		= $path;
			$file->FileSize			= rsfilesHelper::filesize($fullpath);
			$file->FileStatistics	= 0;
			$file->DownloadMethod	= 0;
			$file->DateAdded		= filemtime($fullpath);
			$file->hash				= md5_file($fullpath);
			$file->CanDownload		= 0;
			$file->CanView			= 0;
			$file->published		= 1;
			$file->FileType			= 0;
			$file->hits				= 0;
			$file->show_preview		= 1;
		}
		
		if (empty($file)) {
			return false;
		}
		
		if (empty($file->DateAdded) || $file->DateAdded == $db->getNullDate()) {
			$dateadded = $file->FileType ? '' : rsfilesHelper::showDate(filemtime($fullpath));
		} else {
			$dateadded = rsfilesHelper::showDate($file->DateAdded);
		}
		
		if (empty($file->ModifiedDate) || $file->ModifiedDate == $db->getNullDate()) {
			$lastmodified = $file->FileType ? '' : rsfilesHelper::showDate(filemtime($fullpath));
		} else {
			$lastmodified = rsfilesHelper::showDate($file->ModifiedDate);
		}
		
		$extension	= $file->FileType ? rsfilesHelper::getExt($file->FilePath) : rsfilesHelper::getExt($fullpath);
		$mimetype	= rsfilesHelper::mimetype(strtolower($extension));
		$object = new stdClass();
		
		$object->fname				= !empty($file->FileName) ? $file->FileName : rsfilesHelper::getName((rsfilesHelper::external($path) ? $file->FilePath : $fullpath));
		$object->filedescription	= !empty($file->FileDescription) ? $file->FileDescription : JText::_('COM_RSFILES_NO_DESCRIPTION');
		$object->filelicense		= !empty($file->IdLicense) ? JRoute::_('index.php?option=com_rsfiles&layout=license&tmpl=component&id='.rsfilesHelper::sef($file->IdLicense,$file->LicenseName).rsfilesHelper::getItemid()) : '';
		$object->filename			= $file->FileType ? rsfilesHelper::getName($file->FilePath) : rsfilesHelper::getName($fullpath);
		$object->fileversion		= $file->FileVersion;
		$object->filesize			= $file->FileType ? ' - ' : rsfilesHelper::formatBytes(rsfilesHelper::filesize($fullpath));
		$object->filetype			= $extension ? $extension. ($mimetype ? ' ('.JText::_('COM_RSFILES_MIMETYPE').' '.rsfilesHelper::mimetype(strtolower($extension)).')' : '') : '';
		$object->owner				= empty($file->IdUser) ? JText::_('COM_RSFILES_GUEST') : $file->name;
		$object->dateadded			= $dateadded;
		$object->hits				= (int) $file->hits;
		$object->downloads			= rsfilesHelper::downloads($file->FilePath);
		$object->lastmodified		= $lastmodified;
		$object->checksum			= empty($file->hash) ? JText::_('COM_RSFILES_NO_CHECKSUM') : $file->hash;
		$object->thumb				= empty($file->FileThumb) ? '' : JURI::root().'components/com_rsfiles/images/thumbs/files/'.$file->FileThumb;
		$object->fullpath			= $file->FileType ? $file->FilePath : $fullpath;
		
		return (object) array_merge((array) $file, (array) $object);
	}
	
	// Create a new folder
	public function create() {
		$db 		= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$uid 		= JFactory::getUser()->get('id');
		$session 	= JFactory::getSession();
		$jform		= $this->input->get('jform',array(),'array');
		$config		= rsfilesHelper::getConfig();
		$briefcase	= rsfilesHelper::isBriefcase();
		$folder		= rsfilesHelper::makeSafe($jform['folder']);
		$parent		= $jform['parent'];
		$fullpath	= $briefcase ? rsfilesHelper::getBriefcase($parent) : $session->get('rsfilesdownloadfolder').$this->ds.$parent;
		
		if ($briefcase && !rsfilesHelper::briefcase('CanMaintainBriefcase')) {
			$parent = empty($parent) ? $uid : $uid.$this->ds.$parent;
		}
		
		// Check to see if the user has permission to create a new folder
		if ($briefcase) {
			if (!rsfilesHelper::briefcase('CanUploadBriefcase') && !rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				$this->setError(JText::_('COM_RSFILES_ERROR_1'));
				return false;
			}
		} else {
			$checkpath = str_replace($config->download_folder, '', $fullpath);
			$checkpath = trim($checkpath,$this->ds);
			$checkpath = empty($checkpath) ? 'root_rs_files' : $checkpath;
			
			if (!rsfilesHelper::permissions('CanCreate',$checkpath)) {
				$this->setError(JText::_('COM_RSFILES_ERROR_1'));
				return false;
			}
		}
		
		if (JFolder::exists($fullpath.$this->ds.$folder)) {
			$this->setError(JText::_('COM_RSFILES_FOLDER_ALREADY_EXISTS',true));
			return false;
		}
		
		if (strlen($folder) > 1) {
			if (JFolder::create($fullpath.$this->ds.$folder)) {
				$filePath = $briefcase ? str_replace($config->briefcase_folder, '', $fullpath) : str_replace($config->download_folder, '', $fullpath);
				$filePath = trim($filePath,$this->ds);
				$filePath = empty($filePath) ? $folder : $filePath.$this->ds.$folder;
				
				$query->clear()
					->insert($db->qn('#__rsfiles_files'))
					->set($db->qn('FilePath').' = '.$db->q($filePath))
					->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
					->set($db->qn('DownloadMethod').' = 0')
					->set($db->qn('briefcase').' = '.(int) $briefcase)
					->set($db->qn('published').' = 1');
					
					if ($uid && !$briefcase) {
						$parts = explode($this->ds,$filePath);
						array_pop($parts);
						if (!empty($parts)) {
							$parts = implode($this->ds,$parts);
							
							$thequery = $db->getQuery(true)
								->select($db->qn('CanCreate'))->select($db->qn('CanUpload'))
								->select($db->qn('CanDelete'))->select($db->qn('CanView'))
								->select($db->qn('CanEdit'))->select($db->qn('CanDownload'))
								->from($db->qn('#__rsfiles_files'))
								->where($db->qn('FilePath').' = '.$db->q($parts));
							
							$db->setQuery($thequery);
							if ($permissions = $db->loadObject()) {
								$query->set($db->qn('CanCreate').' = '.$db->q($permissions->CanCreate));
								$query->set($db->qn('CanUpload').' = '.$db->q($permissions->CanUpload));
								$query->set($db->qn('CanDelete').' = '.$db->q($permissions->CanDelete));
								$query->set($db->qn('CanView').' = '.$db->q($permissions->CanView));
								$query->set($db->qn('CanDownload').' = '.$db->q($permissions->CanDownload));
								$query->set($db->qn('CanEdit').' = '.$db->q($permissions->CanEdit));
							}
						} else {
							$fullpath = trim($fullpath,$this->ds);
							if ($fullpath == $config->download_folder) {
								$query->set($db->qn('CanCreate').' = '.$db->q($config->download_cancreate));
								$query->set($db->qn('CanUpload').' = '.$db->q($config->download_canupload));
							}
						}
					}
					
					$db->setQuery($query);
					$db->execute();
					
					return true;
			} else {
				$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_ERROR',true));
				return false;
			}
		} else {
			$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_LENGTH_ERROR',true));
			return false;
		}
	}
	
	public function checkupload() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		$user		= JFactory::getUser();
		$briefcase	= rsfilesHelper::isBriefcase();
		$folder		= rsfilesHelper::getFolder();
		$overwrite	= $this->input->getInt('overwrite',0);
		$app		= JFactory::getApplication();
		$moderate	= rsfilesHelper::briefcase('moderate');
		$iOS		= rsfilesHelper::isiOS();
		$file		= $this->input->getString('file');
		$size		= $this->input->getInt('size');
		
		// Check to see if upload is enabled
		if ($config->enable_upload == 0) {
			$this->setError(JText::_('COM_RSFILES_UPLOAD_DENIED'));
			return false;
		}
		
		// Do we have a valid file
		if (empty($file)) {
			$this->setError(JText::_('COM_RSFILES_NO_UPLOAD_REQUESTED'));
			return false;
		}
		
		// On some browsers for iOS when selecting MOV videos the size is 0
		if ($iOS && $size == 0 && strtolower(rsfilesHelper::getExt($file)) == 'mov') {
			$this->setError(JText::_('COM_RSFILES_IOS_RESTRICTION'));
			return false;
		}
		
		if ($briefcase) {
			$root		 	= $config->briefcase_folder;
			$maintenance	= rsfilesHelper::briefcase('CanMaintainBriefcase');
			$path 			= $folder;
			$path 			= !empty($path) ? $path : ($maintenance ? $user->get('id') : '');
			$fullpath 		= $maintenance ? $root.$this->ds.$path : $root.$this->ds.$user->get('id').$this->ds.$path;
			$size_limit		= (rsfilesHelper::getMaxFileSize() * 1048576);
			
			if (!rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				$path = empty($path) ? $user->get('id') : $user->get('id').$this->ds.$path;
			}
			
			if (!empty($folder)) {
				if ($maintenance) {
					$parts = explode($this->ds,$folder);
					$user_folder = $parts[0];
					$briefcase_root = $root.$this->ds.$user_folder;
				} else {
					$briefcase_root = $root.$this->ds.$user->get('id');
				}
			} else {
				$briefcase_root = $maintenance ? $root : $root.$this->ds.$user->get('id');
			}
			
			$currentQuota		= $this->input->getFloat('quota');
			$maxQuota 			= rsfilesHelper::getMaxFilesSize() * 1048576;
			$no_of_max_files	= rsfilesHelper::getMaxFilesNo();
			$current_no_of_files= $this->input->getInt('number');
		} else {
			$root		 = JFactory::getSession()->get('rsfilesdownloadfolder');
			$path 		 = $folder;
			$path 		 = empty($path) ? '' : $this->ds.$path;
			$fullpath 	 = $root.$path;
			$size_limit	 = ($config->max_upl_size * 1024);
			
			$current_no_of_files 	= 1;
			$no_of_max_files 		= 2;
		}
		
		if ($briefcase) {
			// Check to see if user has permission to upload
			if (!rsfilesHelper::briefcase('CanUploadBriefcase') && !rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				$this->setError(JText::_('COM_RSFILES_UPLOAD_DENIED'));
				return false;
			}
			
			// Check to see if the user has reached his maximum files
			if ($current_no_of_files >= $no_of_max_files) {
				$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_ERROR_3',$file));
				return false;
			}
			
			// Check not to exceed the maximum upload quota
			if ($currentQuota + $size >= $maxQuota) {
				$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_ERROR_5',$file));
				return false;
			}
			
			$config->briefcase_allowed_files 	= strtolower($config->briefcase_allowed_files);
			$config->briefcase_allowed_files 	= str_replace("\r",'',$config->briefcase_allowed_files);
			$allowed_extensions					= explode("\n",$config->briefcase_allowed_files);
			
		} else {
			// Check to see if the user has permission to upload
			$checkpath = str_replace($config->download_folder, '', $fullpath);
			$checkpath = trim($checkpath,$this->ds);
			$checkpath = empty($checkpath) ? 'root_rs_files' : $checkpath;
			if (!rsfilesHelper::permissions('CanUpload',$checkpath)) {
				$this->setError(JText::_('COM_RSFILES_UPLOAD_DENIED'));
				return false;
			}
			
			$config->allowed_files 	= strtolower($config->allowed_files);
			$config->allowed_files 	= str_replace("\r",'',$config->allowed_files);
			$allowed_extensions		= explode("\n",$config->allowed_files);
		}
		
		// Upload file
		$thefile = rsfilesHelper::makeSafe(rsfilesHelper::getName($file));
		if ($iOS) {
			$filenoextension = JFile::stripExt($thefile);
			if (in_array($filenoextension, array('image','capturedvideo'))) {
				$thefile = JFactory::getDate()->format('U').'_'.$thefile;
			}
		}
		
		$thepath = urldecode($fullpath.$this->ds.$thefile);
		$performInsert = file_exists($thepath);
		
		$canUpload = true;
		if ($overwrite) {
			$canUpload = true;
		} else {
			if (file_exists($thepath)) {
				$canUpload = false;
			} else {
				$canUpload = true;
			}
		}
		
		// The file already exists on the server and the overwrite option is disabled
		if (!$canUpload) {
			$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_ERROR_4',$file));
			return false;
		}
		
		// Check for allowed file extension
		if (!in_array(strtolower(rsfilesHelper::getExt($thefile)),$allowed_extensions)) {
			$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_ERROR_1',$file));
			return false;
		}
		
		// Check for allowed file size
		if ($size > $size_limit) {
			$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_ERROR_2',$file));
			return false;
		}
		
		$this->setState('perform.insert',$performInsert);
		$this->setState('file.name',$thefile);
		return true;
	}
	
	// Upload files using the form method
	public function upload() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		$user		= JFactory::getUser();
		$briefcase	= rsfilesHelper::isBriefcase();
		$folder		= rsfilesHelper::getFolder();
		$moderate	= rsfilesHelper::briefcase('moderate');
		$iOS		= rsfilesHelper::isiOS();
		$filename	= $this->input->getString('filename');
		$exists		= $this->input->getInt('exists');
		
		if ($briefcase) {
			$root		 	= $config->briefcase_folder;
			$maintenance	= rsfilesHelper::briefcase('CanMaintainBriefcase');
			$path 			= $folder;
			$path 			= !empty($path) ? $path : ($maintenance ? $user->get('id') : '');
			$fullpath 		= $maintenance ? $root.$this->ds.$path : $root.$this->ds.$user->get('id').$this->ds.$path;
			
			if (!rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				$path = empty($path) ? $user->get('id') : $user->get('id').$this->ds.$path;
			}
			
			if (!empty($folder)) {
				if ($maintenance) {
					$parts = explode($this->ds,$folder);
					$user_folder = $parts[0];
					$briefcase_root = $root.$this->ds.$user_folder;
				} else {
					$briefcase_root = $root.$this->ds.$user->get('id');
				}
			} else {
				$briefcase_root = $maintenance ? $root : $root.$this->ds.$user->get('id');
			}
			
		} else {
			$root		 = JFactory::getSession()->get('rsfilesdownloadfolder');
			$path 		 = $folder;
			$path 		 = empty($path) ? '' : $this->ds.$path;
			$fullpath 	 = $root.$path;
		}
		
		$cleanpath = ltrim($path, $this->ds);
		
		// Upload file
		$thefile = $filename;
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/upload.php';
		$options = array('upload_dir' => $fullpath.$this->ds, 'param_name' => 'file', 'filename' => $thefile);
		
		$upload = new UploadHandler($options);
		$response = $upload->response;
		$finished = isset($response['file'][0]->insert) ? true : false;
		
		if ($finished) {
			if (!$exists) {
				$filePath = $briefcase ? str_replace($config->briefcase_folder, '', $fullpath) : str_replace($config->download_folder, '', $fullpath);
				$filePath = trim($filePath,$this->ds);
				$filePath = empty($filePath) ? $thefile : $filePath.$this->ds.$thefile;
				
				$query->clear()
					->select($db->qn('IdFile'))
					->from($db->qn('#__rsfiles_files'))
					->where($db->qn('FilePath').' = '.$db->q($filePath));
				$db->setQuery($query);
				$fileID = (int) $db->loadResult();
				
				if (!$fileID) {
					$query->clear()
						->insert($db->qn('#__rsfiles_files'))
						->set($db->qn('FilePath').' = '.$db->q($filePath))
						->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
						->set($db->qn('IdUser').' = '.(int) $user->get('id'))
						->set($db->qn('DownloadMethod').' = 0')
						->set($db->qn('show_preview').' = 1')
						->set($db->qn('briefcase').' = '.$db->q((int) $briefcase))
						->set($db->qn('hash').' = '.$db->q(md5_file($fullpath.$this->ds.$thefile)));
					
					if ($briefcase) {
						$query->set($db->qn('published').' = 1');
					} else {
						if ($moderate) {
							$query->set($db->qn('published').' = 0');
						} else {
							$query->set($db->qn('published').' = 1');
						}
					}
					
					if ($user->get('id') != 0 && !$briefcase) {
						$parts = explode($this->ds,$filePath);
						array_pop($parts);
						if (!empty($parts)) {
							$parts = implode($this->ds,$parts);
						
							$thequery = $db->getQuery(true)
								->select($db->qn('CanDownload'))->select($db->qn('CanView'))
								->select($db->qn('CanEdit'))->select($db->qn('CanDelete'))
								->from($db->qn('#__rsfiles_files'))
								->where($db->qn('FilePath').' = '.$db->q($parts));
							
							$db->setQuery($thequery);
							$permissions = $db->loadObject();
							
							if (isset($permissions)) {
								$query->set($db->qn('CanDownload').' = '.$db->q($permissions->CanDownload));
								$query->set($db->qn('CanView').' = '.$db->q($permissions->CanView));
								$query->set($db->qn('CanEdit').' = '.$db->q($permissions->CanEdit));
								$query->set($db->qn('CanDelete').' = '.$db->q($permissions->CanDelete));							
							}
						}
					}
					
					$db->setQuery($query);
					$db->execute();
					$fileID = $db->insertid();
				} else {
					if (!$briefcase) {
						$query->clear()->update($db->qn('#__rsfiles_files'));
						
						if ($moderate) {
							$query->set($db->qn('published').' = 0');
						} else {
							$query->set($db->qn('published').' = 1');
						}
						
						$query->where($db->qn('IdFile').' = '.$db->q($fileID));
					}
				}
				
				if (!$briefcase) {
					if ($moderate) {
						// Send moderation email
						if ($moderation_email = rsfilesHelper::getMessage('moderate')) {
							if (!empty($moderation_email->to)) {
								$cc		= !empty($config->email_cc) ? $config->email_cc : null;
								$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
								
								$subject	= $moderation_email->subject;
								$body		= $moderation_email->message;
								
								if ($emails = explode(',',$moderation_email->to)) {
									foreach ($emails as $email) {
										$email	= trim($email);
										
										if (empty($email)) {
											continue;
										}
										
										$hash		= md5($email.$fileID);
										$fileurl	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($cleanpath.$this->ds.$thefile).'&hash='.$hash,false);
										$approveurl	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&task=approve&hash='.$hash,false);
										
										$bad	= array('{file}','{approve}');
										$good	= array($fileurl, $approveurl);
										$body	= str_replace($bad, $good, $body);
										
										$mailer	= JFactory::getMailer();
										$mailer->sendMail($config->email_from, $config->email_from_name, $email, $subject, $body, $moderation_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
									}
								}
							}
						}
					}
				}
			}
			
			// Send emails
			$url		= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&layout=download'.($briefcase ? '&from=briefcase' : '').'&path='.rsfilesHelper::encode($cleanpath.$this->ds.$thefile));
			$anchor		= '<a href="'.$url.'">'.$url.'</a>';
			
			if (!$moderate && !$briefcase) {
				// Send upload email
				if ($upload_email = rsfilesHelper::getMessage('upload')) {
					if ($upload_email->enable && !empty($upload_email->to)) {
						
						$cc		= !empty($config->email_cc) ? $config->email_cc : null;
						$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
						
						$subject	= $upload_email->subject;
						$body		= $upload_email->message;
						
						$bad	= array('{name}','{username}', '{files}');
						$good	= array($user->get('name'), $user->get('username'), $anchor);
						$body	= str_replace($bad, $good, $body);
						
						$mailer	= JFactory::getMailer();
						$mailer->sendMail($config->email_from, $config->email_from_name, $upload_email->to, $subject, $body, $upload_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
					}
				}
			}
			
			// Send briefcase notification to the owner of the briefcase
			if ($briefcase && $maintenance) {
				$userID = (int) str_replace($config->briefcase_folder.$this->ds,'',$briefcase_root);
				$currentUserID = (int) $user->get('id');
				
				if ($userID != $currentUserID) {
					if ($briefcaseupload_email = rsfilesHelper::getMessage('briefcaseupload')) {
						if ($briefcaseupload_email->enable) {
							
							$owner	= JFactory::getUser($userID);
							$to 	= $owner->get('email');
							$cc		= !empty($config->email_cc) ? $config->email_cc : null;
							$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
							
							$subject	= $briefcaseupload_email->subject;
							$body		= $briefcaseupload_email->message;
							
							$bad	= array('{name}','{uploader}', '{files}');
							$good	= array($owner->get('name'), $user->get('name'), $anchor);
							$body	= str_replace($bad, $good, $body);
							
							$mailer	= JFactory::getMailer();
							$mailer->sendMail($config->email_from, $config->email_from_name, $to, $subject, $body, $briefcaseupload_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
						}
					}
				}
			}
			
			if ($moderate) {
				$this->setState('success.message',JText::sprintf('COM_RSFILES_IMAGE_SUCCESSFULLY_UPLOADED_MODERATION', $thefile));
			} else {
				$this->setState('success.message',JText::sprintf('COM_RSFILES_IMAGE_SUCCESSFULLY_UPLOADED', $thefile));
			}
		}
		
		return true;
	}
	
	// Cancel uploads
	public function cancelupload() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		$user		= JFactory::getUser();
		$briefcase	= rsfilesHelper::isBriefcase();
		$folder		= rsfilesHelper::getFolder();
		$file		= $this->input->getString('file');
		$file		= rsfilesHelper::makeSafe(rsfilesHelper::getName($file));
		
		if ($briefcase) {
			// Check to see if user has permission to upload
			if (!rsfilesHelper::briefcase('CanUploadBriefcase') && !rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				return false;
			}
			
			$root		 	= $config->briefcase_folder;
			$maintenance	= rsfilesHelper::briefcase('CanMaintainBriefcase');
			$path 			= $folder;
			$path 			= !empty($path) ? $path : ($maintenance ? $user->get('id') : '');
			$fullpath 		= $maintenance ? $root.$this->ds.$path : $root.$this->ds.$user->get('id').$this->ds.$path;
		} else {
			// Check to see if the user has permission to upload
			$root		= JFactory::getSession()->get('rsfilesdownloadfolder');
			$path 		= $folder;
			$path 		= empty($path) ? '' : $this->ds.$path;
			$fullpath 	= $root.$path;
			$checkpath	= str_replace($config->download_folder, '', $fullpath);
			$checkpath	= trim($checkpath,$this->ds);
			$checkpath	= empty($checkpath) ? 'root_rs_files' : $checkpath;
			
			if (!rsfilesHelper::permissions('CanUpload',$checkpath)) {
				return false;
			}
		}
		
		if (file_exists($fullpath.$this->ds.$file)) {
			jimport('joomla.filesystem.file');
			JFile::delete($fullpath.$this->ds.$file);
			
			$query->clear()->delete($db->qn('#__rsfiles_files'));
			
			if ($briefcase) {
				$filepath = str_replace($config->briefcase_folder.$this->ds, '', $fullpath.$this->ds.$file);
				$query->where($db->qn('briefcase').' = 1');
			} else {
				$filepath = str_replace($config->download_folder.$this->ds, '', $fullpath.$this->ds.$file);
				$query->where($db->qn('briefcase').' = 0');
			}
			
			$query->where($db->qn('FilePath').' = '.$db->q($filepath));
			$db->setQuery($query);
			$db->execute();
		} else {
			return false;
		}
		
		return true;
	}
	
	// Upload external files
	public function uploadexternal() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= rsfilesHelper::getConfig();
		$folder		= rsfilesHelper::getFolder();
		$moderate	= rsfilesHelper::briefcase('moderate');
		$filepaths	= array();
		$uploads	= 0;
		$externals	= $this->input->get('external', array(), 'array');
		
		// Insert external files
		if (!empty($externals)) {
			$root		= JFactory::getSession()->get('rsfilesdownloadfolder');
			$path 		= $folder;
			$path 		= empty($path) ? '' : $this->ds.$path;
			$fullpath	= $root.$path;
			$fileParent = str_replace($config->download_folder, '', $fullpath);
			$fileParent = trim($fileParent,$this->ds);
			$fileParent = empty($fileParent) ? 'root' : $fileParent;
			
			if ($user->get('id') != 0) {
				$thequery = $db->getQuery(true)
					->select($db->qn('CanDownload'))->select($db->qn('CanView'))
					->select($db->qn('CanEdit'))->select($db->qn('CanDelete'))
					->from($db->qn('#__rsfiles_files'))
					->where($db->qn('FilePath').' = '.$db->q($fileParent));
				
				$db->setQuery($thequery);
				$permissions = $db->loadObject();
			}
			
			foreach ($externals as $external) {
				if (empty($external) || rsfilesHelper::getExt($external) == '') {
					continue;
				}
				
				$query->clear()
					->select($db->qn('IdFile'))
					->from($db->qn('#__rsfiles_files'))
					->where($db->qn('FilePath').' = '.$db->q($external))
					->where($db->qn('FileType').' = 1')
					->where($db->qn('FileParent').' = '.$db->q($fileParent));
				$db->setQuery($query);
				if ((int) $db->loadResult()) {
					continue;
				}
				
				$query->clear()
					->insert($db->qn('#__rsfiles_files'))
					->set($db->qn('FilePath').' = '.$db->q($external))
					->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
					->set($db->qn('IdUser').' = '.(int) $user->get('id'))
					->set($db->qn('FileType').' = 1')
					->set($db->qn('DownloadMethod').' = 0')
					->set($db->qn('FileParent').' = '.$db->q($fileParent));
				
				if ($moderate) {
					$query->set($db->qn('published').' = 0');
				} else {
					$query->set($db->qn('published').' = 1');
				}
				
				if (isset($permissions)) {
					$query->set($db->qn('CanDownload').' = '.$db->q($permissions->CanDownload));
					$query->set($db->qn('CanView').' = '.$db->q($permissions->CanView));
					$query->set($db->qn('CanEdit').' = '.$db->q($permissions->CanEdit));
					$query->set($db->qn('CanDelete').' = '.$db->q($permissions->CanDelete));							
				}
				
				$db->setQuery($query);
				$db->execute();
				$externalID = $db->insertid();
				
				if ($moderate) {
					if ($moderation_email = rsfilesHelper::getMessage('moderate')) {
						if (!empty($moderation_email->to)) {
							$cc		= !empty($config->email_cc) ? $config->email_cc : null;
							$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
							
							$subject	= $moderation_email->subject;
							$body		= $moderation_email->message;
							
							if ($emails = explode(',',$moderation_email->to)) {
								foreach ($emails as $email) {
									$email		= trim($email);
									
									if (empty($email)) {
										continue;
									}
									
									$hash		= md5($email.$externalID);
									$fileurl	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&layout=download&path='.$externalID.'&hash='.$hash,false);
									$approveurl	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&task=approve&hash='.$hash,false);
									
									$bad	= array('{file}','{approve}');
									$good	= array($fileurl, $approveurl);
									$body	= str_replace($bad, $good, $body);
									
									$mailer	= JFactory::getMailer();
									$mailer->sendMail($config->email_from, $config->email_from_name, $email, $subject, $body, $moderation_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
								}
							}
						}
					}
				}
				
				$url 		 = rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&layout=download&path='.$externalID,false);
				$filepaths[] = '<a href="'.$url.'">'.$url.'</a>';
				$uploads++;
			}
			
			if (!$moderate && !empty($uploads)) {
				// Send upload email
				if ($upload_email = rsfilesHelper::getMessage('upload')) {
					if ($upload_email->enable && !empty($upload_email->to)) {
						
						$cc		= !empty($config->email_cc) ? $config->email_cc : null;
						$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
						
						$subject	= $upload_email->subject;
						$body		= $upload_email->message;
						$filepaths	= implode('<br />', $filepaths);
						
						$bad	= array('{name}','{username}', '{files}');
						$good	= array($user->get('name'), $user->get('username'), $filepaths);
						$body	= str_replace($bad, $good, $body);
						
						$mailer	= JFactory::getMailer();
						$mailer->sendMail($config->email_from, $config->email_from_name, $upload_email->to, $subject, $body, $upload_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
					}
				}
			}
			
			if ($moderate) {
				$this->setState('success.message',JText::plural('COM_RSFILES_UPLOAD_SUCCESS_WITH_MODERATION', $uploads));
			} else {
				if (empty($uploads)) {
					$this->setError(JText::_('COM_RSFILES_NO_VALID_EXTERNAL_FILES'));
					return false;
				} else {
					$this->setState('success.message',JText::plural('COM_RSFILES_UPLOAD_SUCCESS', $uploads));
				}
			}
			return true;
		} else {
			$this->setError(JText::_('COM_RSFILES_NO_EXTERNAL_FILES'));
			return false;
		}
	}
	
	// Delete folder / files
	public function delete() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		$user		= JFactory::getUser();
		$briefcase	= rsfilesHelper::isBriefcase();
		$path		= rsfilesHelper::getPath();
		$folder		= rsfilesHelper::getFolder();
		$root		= $briefcase ? $config->briefcase_folder : JFactory::getSession()->get('rsfilesdownloadfolder');
		$fullpath	= '';
		$external	= rsfilesHelper::external($path);
		$source		= '';
		
		if (!empty($folder)) {
			if ($briefcase) {
				if (rsfilesHelper::briefcase('CanMaintainBriefcase')) {
					$fullpath = $root.$this->ds.$folder;
				} else {
					$fullpath = $root.$this->ds.$user->get('id').$this->ds.$folder;
				}
			} else {
				$fullpath = $root.$this->ds.$folder;
			}
		} else if (!empty($path)) {
			if ($external) {
				$fullpath = (int) $path;
			} else {
				if ($briefcase) {
					if (rsfilesHelper::briefcase('CanMaintainBriefcase')) {
						$fullpath = $root.$this->ds.$path;
					} else {
						$fullpath = $root.$this->ds.$user->get('id').$this->ds.$path;
					}
				} else {
					$fullpath = $root.$this->ds.$path;
				}
			}
		}
		
		// Get the redirect to path
		if (is_dir($fullpath) && !$external) {
			$return = explode($this->ds,$folder);
			array_pop($return);
			$return = implode($this->ds,$return);
			$this->setState('return.path',$return);
		}
		
		if (is_file($fullpath) && !$external) {
			$return = explode($this->ds,$path);
			array_pop($return);
			$return = implode($this->ds,$return);
			$this->setState('return.path', $return);
		}
		
		if ($external) {
			$query->clear()->select($db->qn('FileParent'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $fullpath);
			$db->setQuery($query);
			$thepath = $db->loadResult();
			
			$return = $thepath == $root ? '' : str_replace($root.$this->ds,'',$thepath);
			$this->setState('return.path',$return);
		}
		
		if (empty($fullpath)) {
			$this->setError(JText::_('COM_RSFILES_CANNOT_DELETE'));
			return false;
		}
		
		if (!$external) {
			// Check if folder is outside of the root
			if (strpos(realpath($fullpath), realpath($root)) !== 0) {
				$this->setError(JText::_('COM_RSFILES_DELETE_OUTSIDE_ROOT_FOLDER'));
				return false;
			}
			
			$parts	= explode($this->ds,$fullpath);
			$ext	= end($parts);
			if (JFile::stripExt($ext) == '') {
				$this->setError(JText_('COM_RSFILES_CANNOT_DELETE'));
				return false;
			}
			
			// Check for permissions
			if ($briefcase) {
				if (!rsfilesHelper::briefcase('CanMaintainBriefcase') && !rsfilesHelper::briefcase('CanDeleteBriefcase')) {
					$this->setError(JText::_('COM_RSFILES_CANNOT_DELETE'));
					return false;
				}
			} else {
				$checkpath = str_replace($config->download_folder, '', $fullpath);
				$checkpath = trim($checkpath,$this->ds);
				$checkpath = empty($checkpath) ? 'root_rs_files' : $checkpath;
				
				$query->clear()->select($db->qn('IdUser'))->from($db->qn('#__rsfiles_files'))->where($db->qn('FilePath').' = '.$db->q($checkpath));
				$db->setQuery($query);
				$iduser = $db->loadResult();
				
				if (!rsfilesHelper::permissions('CanDelete',$checkpath) && !(rsfilesHelper::briefcase('deleteown') && $iduser == $user->get('id'))) {
					$this->setError(JText::_('COM_RSFILES_CANNOT_DELETE'));
					return false;
				}
			}
		}
		
		// Are we trying to delete a folder ?
		if (is_dir($fullpath) && !$external) {
			// Do not delete the root folder
			if ($fullpath == $root.$this->ds || $fullpath == $root) {
				$this->setError(JText::_('COM_RSFILES_DELETE_ROOT_FOLDER'));
				return false;
			}
			
			$subfolders		= JFolder::folders($fullpath,'.',true,true);
			$subfiles		= JFolder::files($fullpath,'.',true,true);
			$elements		= array_merge(array($fullpath),$subfolders,$subfiles);
			
			if (!empty($elements)) {
				foreach ($elements as $element) {
					$element = realpath($element);
					$element = $briefcase ? str_replace($config->briefcase_folder, '', $element) : str_replace($config->download_folder, '', $element);
					$element = trim($element,$this->ds);
				
					$query->clear()
						->select($db->qn('IdFile'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('FilePath').' = '.$db->q($element));
					$db->setQuery($query);
					if ($id = (int) $db->loadResult()) {
						rsfilesHelper::remove($id);
						
						$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$db->q($id));
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			
			JFolder::delete($fullpath);
			$this->setState('return.message',JText::_('COM_RSFILES_FOLDER_REMOVED'));
		}
		
		// Are we trying to delete a single file ?
		if (is_file($fullpath) && !$external) {
			$thepath = $briefcase ? str_replace($config->briefcase_folder, '', $fullpath) : str_replace($config->download_folder, '', $fullpath);
			$thepath = trim($thepath,$this->ds);
			
			$query->clear()
				->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('FilePath').' = '.$db->q($thepath));
			$db->setQuery($query);
			if ($id = (int) $db->loadResult()) {
				rsfilesHelper::remove($id);
				
				$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$db->q($id));
				$db->setQuery($query);
				$db->execute();
			}
			
			JFile::delete($fullpath);
			$this->setState('return.message',JText::_('COM_RSFILES_FILE_REMOVED'));
		}
		
		// Are we trying to remove an external file ?
		if ($external) {
			$theid = (int) $fullpath;
			rsfilesHelper::remove($theid);
			
			$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$theid);
			$db->setQuery($query);
			$db->execute();
			$this->setState('return.message',JText::_('COM_RSFILES_FILE_REMOVED'));
		}
		
		return true;
	}
	
	// Download file
	public function download() {
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$config			= rsfilesHelper::getConfig();
		$itemid			= rsfilesHelper::getItemid();
		$fromemail		= $this->input->getString('email');
		$briefcase		= rsfilesHelper::isBriefcase();
		$task			= $this->input->get('task');
		$session		= JFactory::getSession();
		$dld_fld		= $session->get('rsfilesdownloadfolder');
		$brf_fld		= $session->get('rsfilesbriefcasefolder');
		$file			= rsfilesHelper::getPath();
		$file			= $fromemail ? urldecode(base64_decode($file)) : $file;
		$ip				= rsfilesHelper::getIP(true);
		$isExternal 	= rsfilesHelper::external($file);
		$hash			= $app->input->getString('hash','');
		
		if ($briefcase) {
			$fullpath	= $this->absoluteFolder.$this->ds.$file;
		} else {
			$fullpath	= $isExternal ? ((int) $file) : $this->absoluteFolder.$this->ds.$file;
		}
		
		$published		= rsfilesHelper::published($fullpath);
		$correct_hash	= false;
		
		if (!$briefcase) {
			if ($hash) {
				if (!$published) {
					if (rsfilesHelper::checkHash()) {
						$published = true;
					}
				}
			}
		}
		
		if ($config->captcha_enabled && !$briefcase) {
			if ($task != 'validate') {
				if ($this->verifyEmailHash())
					$correct_hash = true;
				
				if (!$correct_hash){
					$app->redirect(JRoute::_('index.php?option=com_rsfiles&layout=validate&tmpl=component&path='.rsfilesHelper::encode($file).$itemid,false));
				}
			}
		}
		
		if ($briefcase) {
			if (strpos(realpath($fullpath), realpath($brf_fld)) !== 0) 
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));
			
			$candownload = rsfilesHelper::briefcase('CanDownloadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
			
			if(!$candownload)
				$app->redirect(JRoute::_('index.php?option=com_rsfiles&layout=download&from=briefcase&path='.$file, false), JText::_('COM_RSFILES_CANNOT_DOWNLOAD'));
		} else {
			//if the users get out of the root 
			if (empty($isExternal) && strpos(realpath($fullpath), realpath($dld_fld)) !== 0)
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), JRoute::_('index.php?option=com_rsfiles',false));

			$thepath = str_replace($config->download_folder, '', $fullpath);
			$thepath = trim($thepath,$this->ds);
			
			//check first if the user can download the file
			if (!rsfilesHelper::permissions('CanDownload',$thepath))
				$app->redirect(JRoute::_('index.php?option=com_rsfiles&layout=download&path='.$file, false), JText::_('COM_RSFILES_CANNOT_DOWNLOAD'));
		}
		
		// Check if it the file is published
		if (!$published && !is_null($published)) {
			rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_DOWNLOAD'), JRoute::_('index.php?option=com_rsfiles',false));
		}
		
		$parts	= explode($this->ds,$fullpath);
		$ext	= end($parts);
		
		if (empty($isExternal) && JFile::stripExt($ext) == '') {
			rsfilesHelper::errors(JText::_('COM_RSFILES_CANNOT_DOWNLOAD'), JRoute::_('index.php?option=com_rsfiles',false));
		}

		if (is_file($fullpath) || $isExternal) {
			$thepath = str_replace(($briefcase ? $config->briefcase_folder : $config->download_folder), '', $fullpath);
			$thepath = trim($thepath,$this->ds);
			
			// Check to see if the file can be downloaded.
			$query->clear()->select($db->qn('DownloadLimit'))->select($db->qn('FilePath'))->select($db->qn('Downloads'))->from($db->qn('#__rsfiles_files'));
			
			if ($isExternal)
				$query->where($db->qn('IdFile').' = '.(int) $file);
			else 
				$query->where($db->qn('FilePath').' = '.$db->q($thepath));
			
			$db->setQuery($query);
			$info = $db->loadObject();
			
			if (!empty($info->DownloadLimit) && $info->Downloads >= $info->DownloadLimit)
				rsfilesHelper::errors(JText::_('COM_RSFILES_DOWNLOADS_LIMIT_REACHED'), JRoute::_('index.php?option=com_rsfiles',false));
			
			$relative = str_replace($config->download_folder.$this->ds,'',$fullpath);
			rsfilesHelper::statistics($fullpath, $relative, $isExternal ? (int) $file : false);
			
			rsfilesHelper::hits($fullpath, $relative);
			
			if (headers_sent($fname,$line)) {
				throw new Exception(JText::sprintf('COM_RSFILES_HEADERS_SENT',$fname,$line));
			}
			
			@ob_end_clean();
			@set_time_limit(0);
			$filename = $isExternal ? rsfilesHelper::getName(parse_url($info->FilePath, PHP_URL_PATH)) : basename($fullpath);
			header("Cache-Control: public, must-revalidate");
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header("Pragma: no-cache");
			header("Expires: 0"); 
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");

			if (!$isExternal) {
				$filesize = rsfilesHelper::filesize($fullpath);
				header("Content-Length: ".(string) $filesize);
			}
			
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary\n");
			rsfilesHelper::readfile_chunked($isExternal ? $info->FilePath : $fullpath);
			
			if (!empty($info->DownloadLimit)) {
				$query->clear()->update($db->qn('#__rsfiles_files'))->set($db->qn('Downloads').' = '.$db->qn('Downloads').' + 1');
				
				if ($isExternal)
					$query->where($db->qn('IdFile').' = '.(int) $file);
				else 
					$query->where($db->qn('FilePath').' = '.$db->q($thepath));
				
				$db->setQuery($query);
				$db->execute();
			}

			// Send email to admin
			if ($admin_email = rsfilesHelper::getMessage('admin')) {
				if ($admin_email->enable && !empty($admin_email->to)) {
					
					$cc		= !empty($config->email_cc) ? $config->email_cc : null;
					$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
					
					$subject	= $admin_email->subject;
					$body		= $admin_email->message;
					$email 		= $this->getEmailFromHash();
					$name 		= $this->getNameFromHash();
					
					$username = $user->get('username');
					$username = empty($username) ? JText::_('COM_RSFILES_GUEST') : $username;
					$filepath = $isExternal ? $info->FilePath : $fullpath;
					$bad 	= array('{filename}','{filepath}','{ip}','{username}', '{email}','{name}');
					$good 	= array($filename, $filepath , $ip, $username, $email, $name);
					$body	= str_replace($bad, $good, $body);
					
					$mailer	= JFactory::getMailer();
					$mailer->sendMail($config->email_from, $config->email_from_name, $admin_email->to, $subject, $body, $admin_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
				}
			}
			exit();
		}
	}
	
	// Email download
	public function emaildownload() {
		$db 			= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$config			= rsfilesHelper::getConfig();
		$path			= rsfilesHelper::getPath();
		$session 		= JFactory::getSession();
		$briefcase		= rsfilesHelper::isBriefcase();
		$dfolder 		= $briefcase ? $session->get('rsfilesbriefcasefolder') : $session->get('rsfilesdownloadfolder');
		$fullpath 		= $dfolder.$this->ds.$path;
		$itemid 		= rsfilesHelper::getItemid();
		$to 			= $this->input->getString('email','');
		$toname 		= $this->input->getString('name','');
		$user 			= JFactory::getUser();
		$captcha_valid	= true;
		$root			= $briefcase ? $config->briefcase_folder : $config->download_folder;
		$thepath		= str_replace($root.$this->ds,'',$fullpath);
		$mod_hash		= $this->input->getString('hash','');
		
		// Validate captcha
		if ($config->captcha_enabled) {
			$captcha_valid = $this->validate();
		}
		
		if (!$captcha_valid) {
			$session->set('rsfiles_email', $to);
			$session->set('rsfiles_name',  $toname);
			$this->setError(JText::_('COM_RSFILES_INVALID_CAPTCHA'));
			return false;
		}
		
		// Check and validate the email address
		if (empty($to) || !JMailHelper::isEmailAddress($to)) {
			$this->setError(JText::_('COM_RSFILES_INVALID_EMAIL_ADDRESS'));
			$session->set('rsfiles_email', $to);
			$session->set('rsfiles_name',  $toname);
			return false;
		}
		
		$query->clear()->select($db->qn('IdLicense'))->from($db->qn('#__rsfiles_files'))->where($db->qn('FilePath').' = '.$db->q($thepath));
		$db->setQuery($query);
		if ($license = (int) $db->loadResult()) {
			$agreement = $this->input->getInt('agreement',0);
			if (!$agreement) {
				$session->set('rsfiles_email', $to);
				$session->set('rsfiles_name',  $toname);
				$this->setError(JText::_('COM_RSFILES_CHECK_AGREEMENT'));
				return false;
			}
		}
		
		// Check if its published or not
		$published = rsfilesHelper::external($path) ? rsfilesHelper::published($path) : rsfilesHelper::published($fullpath);
		
		if (!$briefcase) {
			if ($mod_hash) {
				if (!$published) {
					if (rsfilesHelper::checkHash()) {
						$published = true;
					}
				}
			}
		}
		
		if (!$published) {
			$this->setError(JText::_('COM_RSFILES_FILE_UNPUBLISHED'));
			return false;
		}
		
		// RSMail! integration
		if ($config->rsmail_integration && !empty($config->rsmail_list_id) && rsfilesHelper::isRsmail()) {
			require_once JPATH_SITE.'/components/com_rsmail/helpers/actions.php';
			
			// Get a new instance of the RSMail! helper
			$rsmail = new rsmHelper();
			
			// Get the state
			$state = $rsmail->getState();
			
			$vars = array($config->rsmail_field_name => $toname);
			
			// Prepare list
			$list = $rsmail->setList($config->rsmail_list_id, $vars);
			
			// Subscribe user
			$idsubscriber = $rsmail->subscribe($to, $list, $state, null, true);
			
			if ($idsubscriber) {
				// The user must confirm his subscription
				if (!$state) {
					$hash = md5($config->rsmail_list_id.$idsubscriber.$to);
					$rsmail->confirmation($config->rsmail_list_id, $to, $hash);
				}
				
				//send notifications
				$rsmail->notifications($config->rsmail_list_id, $to, $vars);
			}
		}
		
		// Send the download email
		if ($download_email = rsfilesHelper::getMessage('download')) {
			
			$session->clear('rsfiles_email');
			$session->clear('rsfiles_name');
			
			$ip		= rsfilesHelper::getIP(true);
			$hash 	= md5(uniqid($ip));
			$dpath 	= base64_encode($path);
			$url	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&task=rsfiles.download'.($briefcase ? '&from=briefcase' : '').'&path='.$dpath.'&email=1&emailhash='.$hash.($mod_hash ? '&hash='.$mod_hash : '').$itemid);
			
			if (rsfilesHelper::external($path)) {
				$query->clear()->select($db->qn('FilePath'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $path);
				$db->setQuery($query);
				$fname = $db->loadResult();
				$url	= '<a target="_blank" href="'.$url.'">'.rsfilesHelper::getName($fname).'</a>';
			} else {
				$url	= '<a target="_blank" href="'.$url.'">'.rsfilesHelper::getName($path).'</a>';
			}
			
			$query->clear()
				->insert($db->qn('#__rsfiles_email_downloads'))
				->set($db->qn('hash').' = '.$db->q($hash))
				->set($db->qn('date').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('email').' = '.$db->q($to))
				->set($db->qn('name').' = '.$db->q($toname));
			
			$db->setQuery($query);
			$db->execute();
			
			$nr_days = (int) $config->remove_days;
			
			if ($nr_days) {				
				$query->clear()
					->delete()->from($db->qn('#__rsfiles_email_downloads'))
					->where($db->qn('date').' < DATE_SUB('.$db->q(JFactory::getDate()->toSql()).',INTERVAL '.$nr_days.' DAY)');
				
				$db->setQuery($query);
				$db->execute();
			}
			
			$cc		= !empty($config->email_cc) ? $config->email_cc : null;
			$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
			
			$subject	= $download_email->subject;
			$body		= $download_email->message;
			
			$bad 	= array('{email}','{downloadurl}','{name}');
			$good 	= array($to , $url, $toname);
			$body	= str_replace($bad, $good, $body);
			
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($config->email_from, $config->email_from_name, $to, $subject, $body, $download_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
			
			return true;
		} else {
			$session->set('rsfiles_email', $to);
			$session->set('rsfiles_name',  $toname);
			return false;
		}
	}
	
	// Create a new user briefcase folder
	public function newbriefcase() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$id			= $this->input->getInt('id');
		$root		= rsfilesHelper::getConfig('briefcase_folder');
		$maintain	= rsfilesHelper::briefcase('CanMaintainBriefcase');
		
		// Can create new user briefcase
		if (!$maintain) {
			$this->setError(JText::_('COM_RSFILES_CANNOT_CREATE_BRIEFCASE_USERS'));
			return false;
		}
		
		// Do we have an empty id
		if (empty($id)) {
			$this->setError(JText::_('COM_RSFILES_INVALID_ID'));
			return false;
		}
		
		// Do we have a valid user
		$query->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__users'))
			->where($db->qn('id').' = '.$id);
		$db->setQuery($query);
		if (!$db->loadResult()) {
			$this->setError(JText::_('COM_RSFILES_INVALID_USER'));
			return false;
		}
		
		// Check if already exists
		if (JFolder::exists($root.$this->ds.$id)) {
			$this->setError(JText::_('COM_RSFILES_BRIEFCASE_ALREADY_EXISTS'));
			return false;
		}
		
		if (JFolder::create($root.$this->ds.$id)) {
			$user = JFactory::getUser($id);
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('FileName').' = '.$db->q(JText::sprintf('COM_RSFILES_BRIEFCASE_FOLDER_LABEL', $user->get('username'))))
				->set($db->qn('FilePath').' = '.$db->q($id))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('DownloadMethod').' = 0')
				->set($db->qn('briefcase').' = 1')
				->set($db->qn('published').' = 1');
			
			$db->setQuery($query);
			$db->execute();
			return true;
		} else {
			$this->setError(JText::_('COM_RSFILES_CANNOT_CREATE_BRIEFCASE'));
			return false;
		}
	}
	
	// Get briefcase files
	public function getBriefcaseFiles() {
		$db 				= JFactory::getDbo();
		$query				= $db->getQuery(true);
		$user 				= JFactory::getUser();
		$itemid				= rsfilesHelper::getItemid();
		$config				= rsfilesHelper::getConfig();
		$briefcase_folder	= rsfilesHelper::getBriefcase();
		
		// Keep guests out of the briefcase folder
		if ($user->get('id') == 0) {
			rsfilesHelper::errors(JText::_('COM_RSFILES_BRIEFCASE_ERROR_1'), 'index.php');
			return false;
		}
		
		$canupload  	  = rsfilesHelper::briefcase('CanUploadBriefcase');
		$canmaintain 	  = rsfilesHelper::briefcase('CanMaintainBriefcase');
		$candelete  	  = rsfilesHelper::briefcase('CanDeleteBriefcase');
		$candownload 	  = rsfilesHelper::briefcase('CanDownloadBriefcase');
		
		if ($canmaintain) {
			$current = $this->getCurrent();
			if (strpos(realpath($current), realpath($briefcase_folder)) !== 0) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_OUTSIDE_OF_BRIEFCASE'), 'index.php');
				return false;
			}
		} else {
			// Check if the user has any permission
			if (!$candownload && !$canupload && !$candelete) {
				rsfilesHelper::errors(JText::_('COM_RSFILES_BRIEFCASE_ERROR_2'), 'index.php');
				return false;
			}
			
			//check to see if the user folder exists
			$user_folder = $config->briefcase_folder.$this->ds.$user->get('id');
			
			if (!JFolder::exists($user_folder)) {
				if (JFolder::create($user_folder)) {
					$query->clear()
						->insert($db->qn('#__rsfiles_files'))
						->set($db->qn('FileName').' = '.$db->q(JText::sprintf('COM_RSFILES_BRIEFCASE_FOLDER_LABEL', $user->get('username'))))
						->set($db->qn('FilePath').' = '.$db->q($user->get('id')))
						->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
						->set($db->qn('DownloadMethod').' = 0')
						->set($db->qn('briefcase').' = 1')
						->set($db->qn('published').' = 1');
					
					$db->setQuery($query);
					$db->execute();
				} else {
					return false;
				}
			}
			
			$folder = rsfilesHelper::getFolder();
			if (!empty($folder)) {
				$briefcase_folder = $user_folder.$this->ds.$folder;
			} else {
				$briefcase_folder = $user_folder;
			}

			if (strpos($briefcase_folder, realpath($config->briefcase_folder)) !== 0) {
				return false;
			}
		}
		
		if (!empty($briefcase_folder) && JFolder::exists($briefcase_folder)) {
			require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
			
			$theclass	= new RSFilesFiles($briefcase_folder,'site',$itemid);
			$files		= $theclass->getFiles();
			$folders	= $theclass->getFolders();

			$data = array_merge($folders,$files);
			$this->_total = count($data);
			
			// Pagination
			$data = array_slice($data,$this->getState('com_rsfiles.'.$this->input->get('layout').'.limitstart'),$this->getState('com_rsfiles.'.$this->input->get('layout').'.limit'));
			return $data;
		}
		
		return false;
	}
	
	// Get bookmarked files
	public function getBookmarks() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$session	= JFactory::getSession();
		$config		= rsfilesHelper::getConfig();
		$data		= $session->get('rsfl_bookmarks');
		$dfolder	= $session->get('rsfilesdownloadfolder');
		$bfolder	= $session->get('rsfilesbriefcasefolder');
		$params		= rsfilesHelper::getParams();
		$folder		= $params->get('folder','');
		$files		= array();
		
		if (empty($data)) {
			return;
		}
		
		if (!empty($data['download_folder'][$folder])) {
			$data['download_folder'][$folder] = array_unique($data['download_folder'][$folder]);
			
			foreach ($data['download_folder'][$folder] as $file) {
				if (!rsfilesHelper::permissions('CanView',urldecode($file))) {
					continue;
				}
				
				$query->clear()
					->select($db->qn('FileName'))
					->from($db->qn('#__rsfiles_files'))
					->where($db->qn('briefcase').' = 0')
					->where($db->qn('FilePath').' = '.$db->q($file));
				
				$db->setQuery($query);
				$filename = $db->loadResult();
				$file = str_replace($dfolder.$this->ds,'',$file);
				$class = new stdClass();
				$class->name = !empty($filename) ? $filename : rsfilesHelper::getName($file);
				$class->path = $file;
				$class->root = 'download_folder';
				
				$files[] = $class;
			}
		}
		
		if (!empty($data['briefcase_folder'])) {
			$data['briefcase_folder'] = array_unique($data['briefcase_folder']);
			$candownload = rsfilesHelper::briefcase('CanDownloadBriefcase') || rsfilesHelper::briefcase('CanMaintainBriefcase') ? 1 : 0;
			foreach ($data['briefcase_folder'] as $file) {
				if (!$candownload) {
					continue;
				}
				
				$query->clear()
					->select($db->qn('FileName'))
					->from($db->qn('#__rsfiles_files'))
					->where($db->qn('briefcase').' = 1')
					->where($db->qn('FilePath').' = '.$db->q($file));
				
				$db->setQuery($query);
				$filename = $db->loadResult();
				$file = rsfilesHelper::briefcase('CanMaintainBriefcase') ? str_replace($bfolder.$this->ds,'',$file) : str_replace($bfolder.$this->ds.JFactory::getUser()->get('id').$this->ds,'',$file);
				$class = new stdClass();
				$class->name = !empty($filename) ? $filename : rsfilesHelper::getName($file);
				$class->path = $file;
				$class->root = 'briefcase_folder';

				$files[] = $class;
			}
		}

		return $files;
	}
	
	// Bookmark a file
	public function bookmark() {
		$db			= JFactory::getDbo();
		$session	= JFactory::getSession();
		$config		= rsfilesHelper::getConfig();
		$briefcase	= rsfilesHelper::isBriefcase();
		$path		= rsfilesHelper::getPath();
		$fullpath	= $this->absoluteRoot.$this->ds.$path;
		$params		= rsfilesHelper::getParams();
		$folder		= $params->get('folder','');
		
		if (!$config->show_bookmark) {
			$this->setError(JText::_('COM_RSFILES_BOOKMARK_DISABLE',true));
			return false;
		}
		
		if (rsfilesHelper::external($path)) {
			$this->setError(JText::_('COM_RSFILES_FILE_BOOKMARKED_ERROR',true));
			return false;
		}

		if (!file_exists($fullpath)) {
			$this->setError(JText::_('COM_RSFILES_NO_SUCH_FILE'));
			return false;
		}
		
		if (strpos(realpath($fullpath), realpath($this->absoluteRoot)) !== 0) { 
			$this->setError(JText::_('COM_RSFILES_FILE_UNREACHABLE'));
			return false;
		}
		
		// Get bookmarks
		$bookmarks = $session->get('rsfl_bookmarks');

		if (empty($bookmarks)) {
			$bookmarks['briefcase_folder'] = array();
			$bookmarks['download_folder'][$folder] = array();
		}

		if ($briefcase) {
			if (!in_array($fullpath,$bookmarks['briefcase_folder'])) {
				$bookmarks['briefcase_folder'][] = $fullpath;
			}
		} else {
			if (!in_array($fullpath,$bookmarks['download_folder'])) {
				$bookmarks['download_folder'][$folder][] = $fullpath;
			}
		}

		$session->set('rsfl_bookmarks',$bookmarks);
		return true;
	}
	
	// Remove bookmark
	public function removebookmark() {
		$session	= JFactory::getSession();
		$dfolder	= $session->get('rsfilesdownloadfolder');
		$bfolder	= $session->get('rsfilesbriefcasefolder');
		$path		= rsfilesHelper::getPath();
		$bookmarks	= $session->get('rsfl_bookmarks');
		$params		= rsfilesHelper::getParams();
		$folder		= $params->get('folder','');
		
		if (!empty($bookmarks['briefcase_folder'])) {
			foreach($bookmarks['briefcase_folder'] as $i => $bookmark) {
				$current = $bfolder.$this->ds.$path;
				if ($bookmark == $current) {
					unset($bookmarks['briefcase_folder'][$i]);
				}
			}
		}
		
		if (!empty($bookmarks['download_folder'][$folder])) {
			foreach($bookmarks['download_folder'][$folder] as $j => $bookmark) {
				if ($bookmark == $dfolder.$this->ds.$path) {
					unset($bookmarks['download_folder'][$folder][$j]);
				}
			}
		}

		$session->clear('rsfl_bookmarks');
		$session->set('rsfl_bookmarks',$bookmarks);
		return true;
	}
	
	// Download bookmarks
	public function downloadbookmarks() {
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.file');
		
		$config		= rsfilesHelper::getConfig();
		$adapter	= JArchive::getAdapter('zip');
		$cids		= $this->input->get('cid',array(),'array');
		$session	= JFactory::getSession();
		$tmp		= JFactory::getConfig()->get('tmp_path');
		$uid		= JFactory::getUser()->get('id');
		$files		= array();
		
		if (!$config->show_bookmark || empty($cids)) {
			return false;
		}
		
		foreach($cids as $cid) {
			$cid	= urldecode($cid);
			
			if (JFile::exists($session->get('rsfilesdownloadfolder').$this->ds.$cid)) {
				$file = $session->get('rsfilesdownloadfolder').$this->ds.$cid;
			} else {
				if (rsfilesHelper::briefcase('CanMaintainBriefcase')) {
					$file = $config->briefcase_folder.$this->ds.$cid;
				} else {
					$file = $config->briefcase_folder.$this->ds.$uid.$this->ds.$cid;
				}
			}
			
			$data	= JFile::read($file);
			$files[] = array('name' => rsfilesHelper::getName($file), 'data' => $data);
		}
		
		if (!empty($files)) {
			$filename = 'download_package_'.time().'.zip';
			$zip = $adapter->create($tmp.$this->ds.$filename,$files);
				
			if ($zip) {
				@ob_end_clean();
				$filename = basename($filename);
				header("Cache-Control: public, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header("Pragma: no-cache");
				header("Expires: 0"); 
				header("Content-Description: File Transfer");
				header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
				header("Content-Type: application/octet-stream");
				header("Content-Length: ".(string) filesize($tmp.$this->ds.$filename));
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary\n");
				rsfilesHelper::readfile_chunked($tmp.$this->ds.$filename);
				exit();
			}
		}
	}
	
	// Get folder description
	public function getFolderDescription() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		
		if (!$config->show_folder_desc) {
			return false;
		}
		
		if ($this->absoluteFolder == $config->download_folder) {
			return $config->download_description;
		}
		
		if (is_dir($this->absoluteFolder)) {
			$query->clear()
				->select($db->qn('FileDescription'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('FilePath').' = '.$db->q($this->relativeFolder));
			
			$db->setQuery($query);
			return $db->loadResult();
		}
		
		return false;
	}
	
	// Get the preview
	public function getPreview() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$path		= rsfilesHelper::getPath();
		$root		= rsfilesHelper::getConfig('download_folder');

		if (rsfilesHelper::external($path)) {
			$id = (int) $path;
		} else {
			$fullpath	= $this->absoluteRoot.$this->ds.$path;
			$path		= str_replace($root.$this->ds,'',$fullpath);
			
			$query->clear()
				->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('FilePath').' = '.$db->q($path));
			$db->setQuery($query);
			$id = (int) $db->loadResult();
		}
		
		rsfilesHelper::preview($id);
	}
	
	// Report file
	public function report() {
		$db 		= JFactory::getDbo();
		$query 		= $db->getQuery(true);
		$user 		= JFactory::getUser();
		$config		= rsfilesHelper::getConfig();
		$jform		= $this->input->get('jform',array(),'array');
		$ip			= rsfilesHelper::getIp(true);
		$path 		= urldecode($jform['path']);
		$report		= $jform['report'];
		
		if (!$config->show_report) {
			return false;
		}
		
		if (rsfilesHelper::external($path)) {
			$idfile = (int) $path;
		} else {
			$fullpath = $this->absoluteRoot.$this->ds.$path;
			
			$query->select($db->qn('IdFile'))->from($db->qn('#__rsfiles_files'))->where($db->qn('FilePath').' = '.$db->q($path));
			$db->setQuery($query);
			if (!$idfile = $db->loadResult()) {
				$query->clear()
					->insert($db->qn('#__rsfiles_files'))
					->set($db->qn('FilePath').' = '.$db->q($path))
					->set($db->qn('briefcase').' = '.$db->q(0))
					->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()));
				
				if (is_file($fullpath)) {
					$query->set($db->qn('hash').' = '.$db->q(md5_file($fullpath)));
				}
				
				$db->setQuery($query);
				$db->execute();
				$idfile = $db->insertid();
			}
		}
		
		if ($idfile) {
			$query->clear()
				->insert($db->qn('#__rsfiles_reports'))
				->set($db->qn('IdFile').' = '.(int) $idfile)
				->set($db->qn('ReportMessage').' = '.$db->q($report))
				->set($db->qn('uid').' = '.$db->q($user->get('id')))
				->set($db->qn('ip').' = '.$db->q($ip))
				->set($db->qn('date').' = '.$db->q(JFactory::getDate()->toSql()));
			
			$db->setQuery($query);
			$db->execute();
			
			if ($report_email = rsfilesHelper::getMessage('report')) {
				if ($report_email->enable && !empty($report_email->to)) {
					
					$cc		= !empty($config->email_cc) ? $config->email_cc : null;
					$bcc	= !empty($config->email_bcc) ? $config->email_bcc : null;
					
					$subject	= $report_email->subject;
					$body		= $report_email->message;
					
					$url	= rsfilesHelper::getBase().JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($path));
					$file	= '<a href="'.$url.'">'.$url.'</a>';
					$bad	= array('{username}', '{ip}', '{report}', '{filename}');
					$good	= array($user->get('username'), $ip, $report, $file);
					$body	= str_replace($bad, $good, $body);
					
					$mailer	= JFactory::getMailer();
					$mailer->sendMail($config->email_from, $config->email_from_name, $report_email->to, $subject, $body, $report_email->mode, $cc, $bcc, null, $config->email_reply, $config->email_reply_name);
				}
			}
			
			return true;
		}
		
		return false;
	}	
	
	// Load license details
	public function getLicense() {
		$db		= JFactory::getDbo();
		$id		= $this->input->getInt('id',0);
		$query	=  $db->getQuery(true)->select('*')->from($db->qn('#__rsfiles_licenses'))->where($db->qn('IdLicense').' = '.$id);
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	// Delete file thumb
	public function deletethumb($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)->select($db->qn('FileThumb'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $id);
		
		$db->setQuery($query);
		$thumb = $db->loadResult();
		
		if (!empty($thumb)) {
			if (JFile::exists(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$thumb)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$thumb)) {
					$query	= $db->getQuery(true)->update($db->qn('#__rsfiles_files'))->set($db->qn('FileThumb').' = '.$db->q(''))->where($db->qn('IdFile').' = '.(int) $id);
					$db->setQuery($query);
					$db->execute();
					
					return true;
				}
			}
		}
		
		$this->setError(JText::_('COM_RSFILES_THUMB_DELETE_ERROR'));
		return false;
	}
	
	// Delete file preview
	public function deletepreview($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)->select($db->qn('preview'))->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.(int) $id);
		
		$db->setQuery($query);
		$preview = $db->loadResult();
		
		if (!empty($preview)) {
			if (JFile::exists(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$preview)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$preview)) {
					$query	= $db->getQuery(true)->update($db->qn('#__rsfiles_files'))->set($db->qn('preview').' = '.$db->q(''))->where($db->qn('IdFile').' = '.(int) $id);
					$db->setQuery($query);
					$db->execute();
					
					return true;
				}
			}
		}
		
		$this->setError(JText::_('COM_RSFILES_PREVIEW_DELETE_ERROR'));
		return false;
	}
	
	// Get email from hash
	public function getEmailFromHash() {
		$db 	= JFactory::getDbo();
		$hash 	= $this->input->getCmd('emailhash');
		$query	= $db->getQuery(true)->select($db->qn('email'))->from($db->qn('#__rsfiles_email_downloads'))->where($db->qn('hash').' = '.$db->q($hash));
		
		$db->setQuery($query,0,1);
		return $db->loadResult();
	}
	
	// Get name from hash
	public function getNameFromHash() {
		$db 	= JFactory::getDbo();
		$hash 	= $this->input->getCmd('emailhash');
		$query	= $db->getQuery(true)->select($db->qn('name'))->from($db->qn('#__rsfiles_email_downloads'))->where($db->qn('hash').' = '.$db->q($hash));
		
		$db->setQuery($query,0,1);
		return $db->loadResult();
	}
	
	// Check hash
	public function verifyEmailHash() {
		$db 	= JFactory::getDbo();
		$hash 	= $this->input->getCmd('emailhash');
		$query	= $db->getQuery(true)->select($db->qn('id'))->from($db->qn('#__rsfiles_email_downloads'))->where($db->qn('hash').' = '.$db->q($hash));
		
		$db->setQuery($query,0,1);
		return $db->loadResult();
	}
	
	// Validate captcha
	public function validate() {
		$config		= rsfilesHelper::getConfig();
		$response	= true;
		
		if ($config->captcha_enabled == 1) {
			$string		= $this->input->getString('captcha','');
			$captcha	= new JSecurImage();
			$response	= $captcha->check($string);
		} else if ($config->captcha_enabled == 2) {
			$recaptcha_challenge	= $this->input->getString('recaptcha_challenge_field','');
			$recaptcha_response 	= $this->input->getString('recaptcha_response_field','');
			$re_response			= RSFilesJReCAPTCHA::checkAnswer($config->recaptcha_private_key, @$_SERVER['REMOTE_ADDR'], $recaptcha_challenge, $recaptcha_response);
			
			$response = ($re_response === false || !$re_response->is_valid) ? false : true;
		} else if ($config->captcha_enabled == 3) {
			try {
				$response = $this->input->get('g-recaptcha-response', '', 'raw');
				$ip		  = $_SERVER['REMOTE_ADDR'];
				$secretKey= $config->recaptcha_new_secret_key;
				
				jimport('joomla.http.factory');
				$http = JHttpFactory::getHttp();
				if ($request = $http->get('https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretKey).'&response='.urlencode($response).'&remoteip='.urlencode($ip))) {
					$json = json_decode($request->body);
				}
			} catch (Exception $e) {
				$response = false;
			}
			
			if (!$json->success) {
				$response = false;
			}
		}
		
		return $response;
	}
	
	// Get State options
	public function getStates() {
		return array(JHtml::_('select.option',0,JText::_('JUNPUBLISHED')), JHtml::_('select.option',1,JText::_('JPUBLISHED')));
	}
	
	// Get Yes/No options
	public function getYesNo() {
		return array(JHtml::_('select.option',0,JText::_('JNO')), JHtml::_('select.option',1,JText::_('JYES')));
	}
	
	// Get return page
	public function getReturnPage() {
		return $this->input->get('return', null, 'base64');
	}
	
	// Set the root folder
	protected function setRoot() {
		$session	= JFactory::getSession();
		$config		= rsfilesHelper::getConfig();
		$uid		= JFactory::getUser()->get('id');
		$params		= rsfilesHelper::getParams();
		$briefcase 	= $this->input->get('layout') == 'briefcase' || $this->input->get('from') == 'briefcase';
		$d_root		= realpath($config->download_folder);
		$b_root		= realpath($config->briefcase_folder);
		
		$this->d_root = $d_root;
		$this->b_root = $b_root;
		
		// Set Root path
		if ($briefcase) {
			if (!rsfilesHelper::briefcase('CanMaintainBriefcase')) {
				$b_root	.= $this->ds.$uid;
			}
			
			$aRoot = realpath($b_root);
			$aRoot = rtrim($aRoot,$this->ds);
			$rRoot = str_replace($b_root,'',$aRoot);
			$rRoot = ltrim($rRoot,$this->ds);
			
			$session->set('rsfilesbriefcasefolder', $aRoot);
			$session->set('rsf_absolute_root', $aRoot);
			$session->set('rsf_relative_root', $rRoot);
		} else {
			$folder = $params->get('folder','');
			
			if (!empty($folder) && is_dir($d_root.$this->ds.$folder)) {
				$aRoot = realpath($d_root.$this->ds.$folder);
			} else {
				$aRoot = $d_root;
			}
			
			$aRoot = rtrim($aRoot,$this->ds);
			$rRoot = str_replace($d_root,'',$aRoot);
			$rRoot = ltrim($rRoot,$this->ds);
			
			$session->set('rsfilesdownloadfolder',$aRoot);
			$session->set('rsf_absolute_root', $aRoot);
			$session->set('rsf_relative_root', $rRoot);
		}
		
		$this->absoluteRoot = $aRoot;
		$this->relativeRoot = $rRoot;
	}
	
	// Set the current folder
	protected function setFolder() {
		$session	= JFactory::getSession();
		$folder		= rsfilesHelper::getFolder();
		
		if (!empty($folder) && is_dir(realpath($this->absoluteRoot.$this->ds.$folder))) {
			$this->absoluteFolder = realpath($this->absoluteRoot.$this->ds.$folder);
			$this->relativeFolder = str_replace((rsfilesHelper::isBriefcase() ? $this->b_root : $this->d_root),'',$this->absoluteFolder);
			$this->relativeFolder = ltrim($this->relativeFolder,$this->ds);
		} else {
			$this->absoluteFolder = $this->absoluteRoot;
			$this->relativeFolder = $this->relativeRoot;
		}
		
		$session->set('rsf_absolute_folder', $this->absoluteFolder);
		$session->set('rsf_relative_folder', $this->relativeFolder);
	}
}