<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class rsfilesModelFiles extends JModelLegacy
{
	protected $_total		= null;
	protected $_totaldata	= null;
	protected $_pagination	= null;
	protected $_folder		= null;
	protected $_app;
	protected $ds;
	
	protected $folders 		= array();
	protected $files		= array();
	protected $limit 		= 0;
	
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();		
		
		$this->ds		= rsfilesHelper::ds();
		$this->_app 	= JFactory::getApplication();
		$config			= rsfilesHelper::getConfig();
		$root			= $config->{rsfilesHelper::getRoot().'_folder'};
		$folder			= $this->_app->input->getString('folder','');
		
		// Remove trailing slash
		if (substr($root, -1) == $this->ds) {
			$root = substr($root, 0, -1);
		}
		
		// Remove leading slash
		if (!empty($folder) && substr($folder,0,1) == $this->ds) {
			$folder = ltrim($folder, $this->ds);
		}
		
		// Check if we have the root configured
		if (empty($root)) {
			$this->_app->redirect('index.php?option=com_rsfiles&view=settings', JText::sprintf('COM_RSFILES_NO_FOLDER_DEFINED',ucwords(str_replace('_',' ',rsfilesHelper::getRoot().'_folder'))), 'error');
		}
		
		// Set default folder as the root
		$this->_folder = $root;
		
		// Create the folder from which we get files and folders
		if (!empty($folder)) {
			if (is_dir($root.$this->ds.$folder)) {
				$this->_folder = $root.$this->ds.$folder;
			}
		}
		
		// Check if the current folder is outside of our root
		if (!empty($folder) && strpos(realpath($this->_folder), realpath($root)) !== 0) {
			$this->_app->redirect('index.php?option=com_rsfiles&view=files', JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'), 'error');
		}
		
		// Compute hash
		$this->hash = md5($this->_folder);
		
		// Get pagination request variables
		$limit 		= $this->_app->getUserStateFromRequest('com_rsfiles.files_'.$this->hash.'.limit', 'limit', $this->_app->getCfg('list_limit'), 'int');
		$limitstart = $this->_app->getUserStateFromRequest('com_rsfiles.files_'.$this->hash.'.limitstart', 'limitstart', 0, 'int');
		
		//In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		// Set limit and limitstart
		$this->setState('com_rsfiles.files_'.$this->hash.'.limit', $limit);
		$this->setState('com_rsfiles.files_'.$this->hash.'.limitstart', $limitstart);
	}
	
	/**
	 *	Method to get the total number of elements
	 */
	public function getTotal() {
		return $this->_total ? $this->_total : 999;
	}
	
	/**
	 *	Method to get the total number of elements
	 */
	public function getTotalData() {
		return isset($this->_totaldata) ? $this->_totaldata : 999;
	}

	/**
	 *	Method to get the pagination
	 */
	public function getPagination() {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotalData(), $this->getState('com_rsfiles.files_'.$this->hash.'.limitstart'), $this->getState('com_rsfiles.files_'.$this->hash.'.limit'));
		return $this->_pagination;
	}
	
	/**
	 *	Method to get the elements
	 */
	public function getData() {
		$folders 	= array();
		$files 		= array();
		$layout		= $this->_app->input->get('layout','');
		$from		= $this->_app->input->get('from','');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		$external	= array();
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
		
		$theclass	= new RSFilesFiles($this->_folder,'admin');
		
		if ($layout == 'modal' && $from != 'editor') {
			// In this case we only load the folders
			$container	= $theclass->getFolders();
		} else {
			// Load folders, files and external files
			$files		= $theclass->getFiles();
			$folders	= $theclass->getFolders();
			
			if (!$briefcase) {
				$external	= $theclass->getExternal();
			}
			
			$container = array_merge($folders,$files,$external);
		}
		
		// Get the total number of files
		$this->_totaldata = count($container);
		
		if ($this->getState('com_rsfiles.files_'.$this->hash.'.limit') != 0)
			$container = array_slice($container,$this->getState('com_rsfiles.files_'.$this->hash.'.limitstart'),$this->getState('com_rsfiles.files_'.$this->hash.'.limit'));
		
		// Get total with pagination
		$this->_total = count($container);
		
		return $container;
	}
	
	/**
	 *	Method to get the current folder
	 */
	public function getCurrent() {
		return $this->_folder;
	}
	
	/**
	 *	Method to set sync limit.
	 */
	public function setOffsetLimit($limit) {
		$this->limit = $limit;
	}
	
	public function setStop($stop) {
		$this->stop = $stop;
	}
	
	/**
	 *	Method to get recurrsive folders.
	 */
	public function getFoldersRecursive($folder) {
		$result = $this->getFoldersLimit($folder);
		// something has gone wrong, tell the controller to throw an error message
		if ($result === false) {
			return false;
		}
		
		if ($this->folders) {
			// found folders...
			return $this->folders;
		} else {
			// this most likely means we've reached the end
			return true;
		}
	}
	
	/**
	 *	Method to get recurrsive files.
	 */
	public function getFilesRecursive($startfile) {
		$this->files = array();
		$result = $this->getFilesLimit($startfile);	
		
		// something has gone wrong, tell the controller to throw an error message
		if ($result === false) {
			return false;
		}
		
		// found files
		return $this->files;
	}
	
	public function getFoldersLimit($folder) {
		if (!is_dir($folder)) {
			$this->setError(JText::sprintf('COM_RSFILES_FOLDER_IS_NOT_A_VALID_FOLDER', $folder));
			return false;
		}
		
		try {
			$handle = @opendir($folder);
			if ($handle) {
				while (($file = readdir($handle)) !== false) {
					// check the limit
					if (count($this->folders) >= $this->limit) {
						return true;
					}
					$dir = $folder . rsfilesHelper::ds() . $file;
					if ($file != '.' && $file != '..' && is_dir($dir)) {
						$this->folders[] = $dir;
						$this->getFoldersLimit($dir);
						return true;
					}
				}
				closedir($handle);
				
				// try to find the next folder
				if (($dir = $this->getAdjacentFolder($folder)) !== false) {
					$this->folders[] = $dir;
					$this->getFoldersLimit($dir);
				}
			} else {
				$this->setError(JText::sprintf('COM_RSFILES_FOLDER_CANNOT_BE_OPENED', $folder));
				return false;
			}
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	public function getFilesLimit($startfile) {
		if (is_file($startfile)) {
			$folder = dirname($startfile);
			$scan_subdirs = false;
		} else {
			$folder = $startfile;
			$scan_subdirs = true;
		}
		
		try {
			$handle = @opendir($folder);
			if ($handle) {
				if ($scan_subdirs) {
					while (($file = readdir($handle)) !== false) {
						$path = $folder . rsfilesHelper::ds() . $file;
						if ($file != '.' && $file != '..' && is_dir($path)) {
							$this->getFilesLimit($path);
							return true;
						}
					}
				}
				closedir($handle);
				
				if (!$this->addFiles($folder, is_file($startfile) ? $startfile : false)) {
					return true;
				}
				
				// done here, try to find the next folder to parse
				if (($dir = $this->getAdjacentFolderFiles($folder)) !== false) {
					$this->getFilesLimit($dir);
				}
			} else {
				$this->setError(JText::sprintf('COM_RSFILES_FOLDER_CANNOT_BE_OPENED', $folder));
				return false;
			}
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	protected function addFiles($folder, $skip_until=false) {
		$handle = @opendir($folder);
		if ($handle) {
			$passed = false;
			
			// no more subdirectories here, search for files
			while (($file = readdir($handle)) !== false) {
				$path = $folder . rsfilesHelper::ds() . $file;
				if ($file != '.' && $file != '..' && is_file($path)) {
					if ($skip_until !== false) {
						if (!$passed && $path == $skip_until) {
							$passed = true;
							continue;
						}
						
						if (!$passed) {
							continue;
						}
					}
					
					if (count($this->files) >= $this->limit) {
						return false;
					}
					
					$this->files[] = $path;
				}
			}
			closedir($handle);
			
			return true;
		}
	}
	
	public function getFolders($folder, $recurse=false, $sort=true, $fullpath=true) {
		if (!is_dir($folder)) {
			$this->setError(JText::sprintf('COM_RSFILES_FOLDER_IS_NOT_A_VALID_FOLDER', $folder));
			return false;
		}
		
		$arr = array();
		
		try {
			$handle = @opendir($folder);
			if ($handle) {
				while (($file = readdir($handle)) !== false) {
					if ($file != '.' && $file != '..') {
						$dir = $folder . rsfilesHelper::ds() . $file;
						if (is_dir($dir)) {
							if ($fullpath) {
								$arr[] = $dir;
							} else {
								$arr[] = $file;
							}
							if ($recurse) {
								$arr = array_merge($arr, $this->getFolders($dir, $recurse, $sort, $fullpath));
							}
						}
					}
				}
				closedir($handle);
			} else {
				$this->setError(JText::sprintf('COM_RSFILES_FOLDER_CANNOT_BE_OPENED', $folder));
				return false;
			}
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		if ($sort) {
			asort($arr);
		}
		
		return $arr;
	}
	
	public function getFiles($folder, $recurse=false, $sort=true, $fullpath=true, $ignore=array()) {
		if (!is_dir($folder)) {
			$this->setError(JText::sprintf('COM_RSFILES_FOLDER_IS_NOT_A_VALID_FOLDER', $folder));
			return false;
		}
		
		$arr = array();
		
		try {
			$handle = @opendir($folder);
			while (($file = readdir($handle)) !== false) {
				if ($file != '.' && $file != '..' && !in_array($file, $ignore)) {
					$dir = $folder . rsfilesHelper::ds() . $file;
					if (is_file($dir)) {
						if ($fullpath) {
							$arr[] = $dir;
						} else {
							$arr[] = $file;
						}
					} elseif (is_dir($dir) && $recurse) {
						$arr = array_merge($arr, $this->getFiles($dir, $recurse, $sort, $fullpath, $ignore));
					}
				}
			}
			closedir($handle);
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		if ($sort) {
			asort($arr);
		}
		return $arr;
	}
	
	protected function getParent($path) {
		$ds		 = rsfilesHelper::ds();
		$parts   = explode($ds, $path);
		array_pop($parts);
		
		return implode($ds, $parts);
	}
	
	protected function getAdjacentFolder($folder) {
		// one level up
		$dfolder = isset($this->stop) && !empty($this->stop) ? urldecode($this->stop) : rsfilesHelper::getConfig('download_folder');
		$parent = $this->getParent($folder);
		
		if ($this->getParent($dfolder) == $parent) {
			return false;
		}
		
		$folders = $this->getFolders($parent, false, false, true);
		if ($folders !== false) {
			if (($pos = array_search($folder, $folders)) !== false) {
				if (isset($folders[$pos+1])) {
					return $folders[$pos+1];
				} else {
					if ($parent == $dfolder || $parent == '/') {
						// this means that there are no more folders left.
						// so we're done here
						return false;
					}
					
					// up again
					return $this->getAdjacentFolder($parent);
				}
			}
		} else {
			return false;
		}
	}
	
	protected function getAdjacentFolderFiles($folder) {
		// one level up
		$dfolder = isset($this->stop) && !empty($this->stop) ? urldecode($this->stop) : rsfilesHelper::getConfig('download_folder');
		$parent = $this->getParent($folder);
		
		if ($this->getParent($dfolder) == $parent) {
			return false;
		}
		
		$folders = $this->getFolders($parent, false, false, true);
		
		if ($folders !== false) {
			if (($pos = array_search($folder, $folders)) !== false) {
				if (isset($folders[$pos+1])) {
					return $folders[$pos+1];
				} else {
					
					if (!$this->addFiles($parent, false)) {
						return false;
					}
					
					if ($parent == $dfolder || $parent == '/') {
						// this means that there are no more folders left
						// so we're done here
						return false;
					}
					
					// up again
					return $this->getAdjacentFolderFiles($parent);
				}
			}
		} else {
			return false;
		}
	}
	
	public function syncFolder($folder) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::getConfig('download_folder');
		
		// Remove trailing slash
		if (substr($root, -1) == $this->ds) {
			$root = substr($root, 0, -1);
		}
		
		$folder = str_replace($root,'',$folder);
		
		// Remove leading slash
		if (!empty($folder) && substr($folder,0,1) == $this->ds) {
			$folder = ltrim($folder, $this->ds);
		}
		
		$folder = urldecode($folder);
		
		$query->clear()
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('FilePath').' = '.$db->q($folder))
			->where($db->qn('briefcase').' = '.$db->q(0));
		
		$db->setQuery($query);
		if ($exists = $db->loadResult()) {
			return false;
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('published').' = 1')
				->set($db->qn('briefcase').' = 0')
				->set($db->qn('FilePath').' = '.$db->q($folder));
			
			$db->setQuery($query);
			$db->execute();
			
			return true;
		}
	}
	
	public function syncFile($file) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::getConfig('download_folder');
		
		if (substr($file,-9,9) == '.htaccess')
			return;
		
		// Remove trailing slash
		if (substr($root, -1) == $this->ds) {
			$root = substr($root, 0, -1);
		}
		
		$file = str_replace($root,'',$file);
		
		// Remove leading slash
		if (!empty($file) && substr($file,0,1) == $this->ds) {
			$file = ltrim($file, $this->ds);
		}
		
		$file = urldecode($file);
		
		$query->clear()
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = '.$db->q(0))
			->where($db->qn('FilePath').' = '.$db->q($file));
		
		$db->setQuery($query);
		if ($exists = $db->loadResult()) {
			return false;
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('published').' = 1')
				->set($db->qn('briefcase').' = 0')
				->set($db->qn('hash').' = '.$db->q(md5_file($root.$this->ds.$file)))
				->set($db->qn('FilePath').' = '.$db->q($file));
			
			$db->setQuery($query);
			$db->execute();
			
			return true;
		}
	}
	
	public function extendFolder($folder) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$cancreate	= $input->getString('cancreate');
		$canupload	= $input->getString('canupload');
		$candelete	= $input->getString('candelete');
		$canedit	= $input->getString('canedit');
		$view		= $input->getString('view');
		$download	= $input->getString('download');
		$root		= rsfilesHelper::root(true);
		
		if (!isset($cancreate) && !isset($canupload) && !isset($candelete) && !isset($view) && !isset($download) && !isset($canedit))
			return false;
		
		$folder = str_replace($root,'',$folder);
		
		if (isset($cancreate) || isset($canupload) || isset($candelete) || isset($view)) {
			$query->clear()
				->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('briefcase').' = '.$db->q(0))
				->where($db->qn('FilePath').' = '.$db->q($folder));
			
			$db->setQuery($query);
			if ($exists = (int) $db->loadResult()) {
				$query->clear()
					->update($db->qn('#__rsfiles_files'))
					->where($db->qn('IdFile').' = '.$exists);
			} else {
				$query->clear()
					->insert($db->qn('#__rsfiles_files'))
					->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
					->set($db->qn('published').' = 1')
					->set($db->qn('briefcase').' = 0')
					->set($db->qn('FilePath').' = '.$db->q($folder));
			}
			
			if (isset($cancreate)) 	$query->set($db->qn('CanCreate').' = '.$db->q($cancreate));
			if (isset($canupload))	$query->set($db->qn('CanUpload').' = '.$db->q($canupload));
			if (isset($candelete)) 	$query->set($db->qn('CanDelete').' = '.$db->q($candelete));
			if (isset($download))	$query->set($db->qn('CanDownload').' = '.$db->q($download));
			if (isset($canedit)) 	$query->set($db->qn('CanEdit').' = '.$db->q($canedit));
			if (isset($view)) 		$query->set($db->qn('CanView').' = '.$db->q($view));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		if (isset($canedit) || isset($candelete) || isset($view) || isset($download)) {
			// Extend external files
			$query->clear()
				->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('briefcase').' = '.$db->q(0))
				->where($db->qn('FileParent').' = '.$db->q($folder));
			$db->setQuery($query);
			if ($externals = $db->loadColumn()) {
				foreach ($externals as $eid) {
					$query->clear()
						->update($db->qn('#__rsfiles_files'))
						->where($db->qn('IdFile').' = '.(int) $eid);
					
					if (isset($canedit)) 	$query->set($db->qn('CanEdit').' = '.$db->q($canedit));
					if (isset($candelete)) 	$query->set($db->qn('CanDelete').' = '.$db->q($candelete));
					if (isset($view)) 		$query->set($db->qn('CanView').' = '.$db->q($view));
					if (isset($download)) 	$query->set($db->qn('CanDownload').' = '.$db->q($download));
					
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		return true;
	}
	
	public function extendFile($file) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$canedit	= $input->getString('canedit');
		$candelete	= $input->getString('candelete');
		$view		= $input->getString('view');
		$download	= $input->getString('download');
		$root		= rsfilesHelper::root(true);
		
		if (!isset($candelete) && !isset($canedit) && !isset($view) && !isset($download))
			return false;
		
		$file = str_replace($root,'',$file);
		
		$query->clear()
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = '.$db->q(0))
			->where($db->qn('FilePath').' = '.$db->q($file));
		
		$db->setQuery($query);
		if ($exists = $db->loadResult()) {
			$query->clear()
				->update($db->qn('#__rsfiles_files'))
				->where($db->qn('IdFile').' = '.$exists);
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_files'))
				->set($db->qn('DateAdded').' = '.$db->q(JFactory::getDate()->toSql()))
				->set($db->qn('published').' = 1')
				->set($db->qn('briefcase').' = 0')
				->set($db->qn('hash').' = '.$db->q(md5_file($root.$file)))
				->set($db->qn('FilePath').' = '.$db->q($file));
		}
		
		if (isset($canedit)) 	$query->set($db->qn('CanEdit').' = '.$db->q($canedit));
		if (isset($candelete)) 	$query->set($db->qn('CanDelete').' = '.$db->q($candelete));
		if (isset($view)) 		$query->set($db->qn('CanView').' = '.$db->q($view));
		if (isset($download)) 	$query->set($db->qn('CanDownload').' = '.$db->q($download));
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	public function extendExternal($folder) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$canedit	= $input->getString('canedit');
		$candelete	= $input->getString('candelete');
		$view		= $input->getString('view');
		$download	= $input->getString('download');
		
		if (!isset($candelete) && !isset($canedit) && !isset($view) && !isset($download))
			return false;
		
		$query->clear()
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = '.$db->q(0))
			->where($db->qn('FileParent').' = '.$db->q($folder));
		$db->setQuery($query);
		if ($externals = $db->loadColumn()) {
			foreach ($externals as $eid) {
				$query->clear()
					->update($db->qn('#__rsfiles_files'))
					->where($db->qn('IdFile').' = '.(int) $eid);
				
				if (isset($canedit)) 	$query->set($db->qn('CanEdit').' = '.$db->q($canedit));
				if (isset($candelete)) 	$query->set($db->qn('CanDelete').' = '.$db->q($candelete));
				if (isset($view)) 		$query->set($db->qn('CanView').' = '.$db->q($view));
				if (isset($download)) 	$query->set($db->qn('CanDownload').' = '.$db->q($download));
				
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		return true;
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rsfilesHelper::isJ3()) {
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/**
	 * Method to set the filter bar.
	 */
	public function getFilterBar() {
		$options = array();
		$options['orderDir']  = false;
		$options['limitBox']  = $this->getPagination()->getLimitBox();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => JFactory::getApplication()->input->getString('filter_search','')
		);
		$options['root'] = array(
			JHtml::_('select.option','download',JText::_('COM_RSFILES_DOWNLOAD_FOLDER')),
			JHtml::_('select.option','briefcase',JText::_('COM_RSFILES_BRIEFCASE_FOLDER'))
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
	
	/**
	 * Method to get navigation.
	 */
	public function getNavigation() {
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/files.php';
		
		$current = $this->getCurrent();
		return RSFilesFiles::getPathNavigation($current);
	}
	
	/**
	 *	Method to enable statistics
	 */
	public function statistics($pks) {
		$db 		= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$now		= JFactory::getDate()->toSql();
		$ids		= array();
		$table		= JTable::getInstance('File', 'rsfilesTable');
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';

		if (!empty($pks)) {
			foreach($pks as $pk) {
				$path = urldecode($pk);
				if (is_dir($root.$this->ds.$path)) 
					continue;
				
				if (rsfilesHelper::external($path)) {
					$id = (int) $pk;
				} else {
					$query->clear()
						->select($db->qn('IdFile'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('briefcase').' = '.$db->q((int) $briefcase))
						->where($db->qn('FilePath').' = '.$db->q($path));
					$db->setQuery($query);
					$id = (int) $db->loadResult();
				}
				
				if (empty($id)) {
					$table->IdFile			= 0;
					$table->FilePath		= $path;
					$table->DateAdded		= $now;
					$table->published		= 1;
					$table->hits			= 0;
					$table->briefcase		= (int) $briefcase;
					$table->hash			= is_file($root.$this->ds.$path) ? md5_file($root.$this->ds.$path) : '';
					$table->FileStatistics	= 1;
					$table->store();
				} else {
					$ids[] = $id;
				}
				
				if (!empty($ids)) {
					JArrayHelper::toInteger($ids);
					$query->clear()
						->update($db->qn('#__rsfiles_files'))
						->set($db->qn('FileStatistics').' = 1')
						->where($db->qn('IdFile').' IN ('.implode(',',$ids).')');
							
					$db->setQuery($query);
					$db->execute();
				}
			}
			return true;
		} else {
			$this->setError(JText::_('COM_RSFILES_NO_OPTION_SELECTED'));
			return false;
		}
	}
	
	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.2
	 */
	public function publish(&$pks, $value = 1) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$now		= JFactory::getDate()->toSql();
		$ids		= array();
		$table		= JTable::getInstance('File', 'rsfilesTable');
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		if (!empty($pks)) {
			foreach ($pks as $pk) {
				$path = urldecode($pk);
				
				if (rsfilesHelper::external($path)) {
					$id = (int) $pk;
				} else {
					$query->clear()
						->select($db->qn('IdFile'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('briefcase').' = '.$db->q((int) $briefcase))
						->where($db->qn('FilePath').' = '.$db->q($path));
					
					$db->setQuery($query);
					$id = (int) $db->loadResult();
				}
				
				if (empty($id)) {
					$table->IdFile		= 0;
					$table->FilePath	= $path;
					$table->DateAdded	= $now;
					$table->published	= $value;
					$table->hits		= 0;
					$table->briefcase	= (int) $briefcase;
					$table->hash		= is_file($root.$this->ds.$path) ? md5_file($root.$this->ds.$path) : '';
					$table->store();
				} else {
					$ids[] = $id;
				}
			}
			
			if (!empty($ids)) {
				JArrayHelper::toInteger($ids);
				$query->clear()
					->update($db->qn('#__rsfiles_files'))
					->set($db->qn('published').' = '.(int) $value)
					->where($db->qn('IdFile').' IN ('.implode(',',$ids).')');
				
				$db->setQuery($query);
				$db->execute();
			}
			
			return true;
		} else {
			$this->setError(JText::_('COM_RSFILES_NO_OPTION_SELECTED'));
			return false;
		}
	}
	
	/**
	 *	Method to create a new folder.
	 */
	public function create($folder) {
		$dir		= rsfilesHelper::makeSafe($folder);
		$path		= JFactory::getApplication()->input->getString('path','');
		$path		= urldecode($path);
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		if (strlen($dir) > 1) {
			$fullpath = $path.rsfilesHelper::ds().$dir;
			
			if (strpos(realpath($path), realpath($root)) !== 0) {
				$this->setError(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'));
				return false;
			}
			
			if (JFolder::exists($fullpath)) {
				$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_ALREADY'));
				return false;
			}
			
			if (JFolder::create($fullpath)) {
				$table	= JTable::getInstance('File', 'rsfilesTable');
				
				$thefolder = str_replace($root,'',$fullpath);
				// Remove leading slash
				if (!empty($thefolder) && substr($thefolder,0,1) == $this->ds) {
					$thefolder = ltrim($thefolder, $this->ds);
				}
				
				$table->IdFile = null;
				$table->FilePath = $thefolder;
				$table->DateAdded = JFactory::getDate()->toSql();
				$table->published = 1;
				$table->briefcase = (int) $briefcase;
				$table->store();
				$this->_app->setUserState('com_rsfiles.files_'.$this->hash.'.limitstart', 0);
				
				return true;
			}
			
			$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_CREATION_ERROR'));
			return false;
		}
		
		$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_INVALID_LENGTH'));
		return false;
	}
	
	/**
	 *	Method to delete files and folders
	 */
	public function delete($pks) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		if (empty($pks)) {
			$this->setError(JText::_('COM_RSFILES_NO_OPTION_SELECTED'));
			return false;
		} else {
			foreach ($pks as $pk) {
				$path		= urldecode($pk);
				$fullpath	= $root.$this->ds.$path;
				
				if (strpos(realpath($fullpath), realpath($root)) !== 0 && !rsfilesHelper::external($path)) {
					$this->setError(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'));
					return false;
				}
				
				// Handle folders
				if(is_dir($fullpath)) {
					$subfolders		= JFolder::folders($fullpath,'.',true,true);
					$subfiles		= JFolder::files($fullpath,'.',true,true);
					$elements		= array_merge(array($fullpath),$subfolders,$subfiles);
					
					if (!empty($elements)) {
						foreach ($elements as $element) {
							$element	= realpath($element);
							$el			= str_replace($root.$this->ds,'',$element);
							
							// Delete external files
							$query->clear()
								->select($db->qn('IdFile'))
								->from($db->qn('#__rsfiles_files'))
								->where($db->qn('briefcase').' = '.$db->q((int) $briefcase))
								->where($db->qn('FileParent').' = '.$db->q($el));
							$db->setQuery($query);
							if ($externalIDS = $db->loadColumn()) {
								foreach ($externalIDS as $externalID) {
									rsfilesHelper::remove($externalID);
								
									$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$db->q($externalID));
									$db->setQuery($query);
									$db->execute();
								}
							}
							
							// Delete folder
							$query->clear()
								->select($db->qn('IdFile'))
								->from($db->qn('#__rsfiles_files'))
								->where($db->qn('briefcase').' = '.$db->q((int) $briefcase))
								->where($db->qn('FilePath').' = '.$db->q($el));
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
				}
				
				// Handle files
				if (is_file($fullpath)) {
					$query->clear()
						->select($db->qn('IdFile'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('briefcase').' = '.$db->q((int) $briefcase))
						->where($db->qn('FilePath').' = '.$db->q($path));
					$db->setQuery($query);
					if ($id = (int) $db->loadResult()) {
						rsfilesHelper::remove($id);
						
						$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$db->q($id));
						$db->setQuery($query);
						$db->execute();
					}
					
					JFile::delete($fullpath);
				}
				
				// Handle external files
				if (rsfilesHelper::external($path)) {
					$theid = (int) $path;
					rsfilesHelper::remove($theid);
					
					$query->clear()->delete()->from($db->qn('#__rsfiles_files'))->where($db->qn('IdFile').' = '.$theid);
					$db->setQuery($query);
					$db->execute();
				}
			}
			return true;
		}
	}
	
	/**
	 *	Method to purge database
	 */
	public function purge() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::getConfig('download_folder');
		
		$query->clear()
			->select($db->qn('IdFile'))->select($db->qn('FilePath'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = 0')
			->where($db->qn('FileType').' = 0');
		$db->setQuery($query);
		if ($elements = $db->loadObjectList()) {
			foreach ($elements as $element) {
				if (!is_dir($root.$this->ds.$element->FilePath) && !is_file($root.$this->ds.$element->FilePath)) {
					rsfilesHelper::remove($element->IdFile);
					$query->clear()
						->delete()
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('IdFile').' = '.(int) $element->IdFile);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		return true;
	}
	
	/**
	 *	Method to get the upload form info.
	 */
	public function getForm() {
		jimport('joomla.form.form');
		
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		
		$form = JForm::getInstance('com_rsfiles.form', 'form');
		return $form;
	}
	
	/**
	 *	Method to get the batch form info.
	 */
	public function getBatchForm() {
		jimport('joomla.form.form');
		
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		
		$form = JForm::getInstance('com_rsfiles.batch', 'batch', array('control' => 'batch'));
		return $form;
	}
	
	/**
	 *	Method to check files.
	 */
	public function checkupload() {
		$input		= JFactory::getApplication()->input;
		$file		= $input->getString('file');
		$path		= $input->getString('path','');
		$ovr		= $input->getInt('overwrite', 0);
		$size		= $input->getInt('size');
		$extension	= JFile::getExt($file);
		$config		= rsfilesHelper::getConfig();
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$iOS		= rsfilesHelper::isiOS();
		
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
		
		// Check for extension
		if (empty($extension)) {
			$this->setError(JText::sprintf('COM_RSFILES_NO_EXTENTION',$file));
			return false;
		}
		
		// Check for allowed file size
		if ($size > ($config->max_upl_size * 1024)) {
			$this->setError(JText::sprintf('COM_RSFILES_UPLOAD_MAX_UPLOAD',$file));
			return false;
		}
		
		if (strpos(realpath($path), realpath($root)) !== 0) {
			$this->setError(JText::_('COM_RSFILES_OUTSIDE_OF_ROOT'));
			return false;
		}
		
		$thefile 		= rsfilesHelper::makeSafe(JFile::getName($file));
		if ($iOS) {
			$filenoextension = JFile::stripExt($thefile);
			if (in_array($filenoextension, array('image','capturedvideo'))) {
				$thefile = JFactory::getDate()->format('U').'_'.$thefile;
			}
		}
		
		$thepath 		= urldecode($path.rsfilesHelper::ds().$thefile);
		$performInsert	= file_exists($thepath);
		
		// Check if overwrite is set
		if ($ovr) {
			$canUpload = true;
		} else {
			if (file_exists($path.rsfilesHelper::ds().$thefile)) {
				$canUpload = false;
			} else {
				$canUpload = true;
			}
		}
		
		// Do we have permission to upload ?
		if (!$canUpload) {
			$this->setError(JText::sprintf('COM_RSFILES_FILE_EXISTS',$file));
			return false;
		}
		
		$this->setState('perform.insert',$performInsert);
		$this->setState('file.name',$thefile);
		return true;
	}
	
	/**
	 *	Method to upload files.
	 */
	public function upload() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$table		= JTable::getInstance('File', 'rsfilesTable');
		$thefile	= $input->getString('filename');
		$path		= urldecode($input->getString('path',''));
		$root		= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		require_once JPATH_SITE.'/components/com_rsfiles/helpers/upload.php';
		$options = array('upload_dir' => $path.rsfilesHelper::ds(), 'param_name' => 'file', 'filename' => $thefile);
		
		$upload = new UploadHandler($options);
		$response = $upload->response;
		$finished = isset($response['file'][0]->insert) ? true : false;
		
		if ($finished) {
			$thepath = str_replace($root,'',$path.rsfilesHelper::ds().$thefile);
		
			// Remove leading slash
			if (!empty($thepath) && substr($thepath,0,1) == rsfilesHelper::ds()) {
				$thepath = ltrim($thepath, rsfilesHelper::ds());
			}
			
			$query->clear()
				->select($db->qn('IdFile'))
				->from($db->qn('#__rsfiles_files'))
				->where($db->qn('FilePath').' = '.$db->q($thepath));
			$db->setQuery($query);
			if ($fileID = (int) $db->loadResult()) {
				$table->IdFile = $fileID;
			}
			
			$table->FilePath 				= $thepath;
			$table->DateAdded	 			= JFactory::getDate($input->getString('DateAdded'))->toSql();
			$table->ModifiedDate 			= JFactory::getDate()->toSql();
			$table->FileStatistics 			= $input->getInt('FileStatistics',0);
			$table->FileVersion 			= $input->getString('FileVersion','');
			$table->IdLicense 				= $input->getInt('IdLicense',0);
			$table->DownloadMethod 			= $input->getInt('DownloadMethod',0);
			$table->DownloadLimit 			= $input->getInt('DownloadLimit',0);
			$table->show_preview 			= $input->getInt('show_preview',0);
			$table->CanDelete				= $input->getString('CanDelete','');
			$table->CanDownload 			= $input->getString('CanDownload','');
			$table->CanView 				= $input->getString('CanView','');
			$table->CanEdit 				= $input->getString('CanEdit','');
			$table->IdUser 					= JFactory::getUser()->get('id');
			$table->hits 					= 0;
			$table->published 				= $input->getInt('published',1);
			$table->hash					= md5_file($path.rsfilesHelper::ds().$thefile);
			$table->briefcase				= (int) $briefcase;

			$table->store();
			$this->_app->setUserState('com_rsfiles.files_'.$this->hash.'.limitstart', 0);
			
			$this->setState('success.message',JText::sprintf('COM_RSFILES_IMAGE_SUCCESSFULLY_UPLOADED', $thefile));
		}
		
		return true;
	}
	
	/**
	 *	Method to cancel upload
	 */
	public function cancelupload() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= rsfilesHelper::getConfig();
		$input		= JFactory::getApplication()->input;
		$path		= urldecode($input->getString('path',''));
		$file		= $input->getString('file');
		$file		= rsfilesHelper::makeSafe(rsfilesHelper::getName($file));
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		$ds			= rsfilesHelper::ds();
		
		if (file_exists($path.$ds.$file)) {
			jimport('joomla.filesystem.file');
			JFile::delete($path.$ds.$file);
			
			$query->clear()->delete($db->qn('#__rsfiles_files'));
			
			if ($briefcase) {
				$filepath = str_replace($config->briefcase_folder.$ds, '', $path.$ds.$file);
				$query->where($db->qn('briefcase').' = 1');
			} else {
				$filepath = str_replace($config->download_folder.$ds, '', $path.$ds.$file);
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
	
	/**
	 *	Method to create a new briefcase folder
	 */
	public function briefcase($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$root	= rsfilesHelper::getConfig('briefcase_folder');
		$path	= $root.$this->ds.$id;
		
		if (JFolder::exists($path)) {
			$this->setError(JText::_('COM_RSFILES_NEW_FOLDER_ALREADY'));
			return false;
		}
		
		if (JFolder::create($path)) {
			$table	= JTable::getInstance('File', 'rsfilesTable');
			
			$table->IdFile = null;
			$table->FilePath = $id;
			$table->FileName = JText::sprintf('COM_RSFILES_BRIECASE_FOLDER_LABEL', JFactory::getUser($id)->get('username'));
			$table->DateAdded = JFactory::getDate()->toSql();
			$table->briefcase = 1;
			$table->published = 1;
			$table->store();
			
			return true;
		}
	}
	
	public function batch($pks) {
		$pks	= (array) $pks;
		$input	= JFactory::getApplication()->input;
		$batch	= $input->get('batch',array(),'array');
		$ds		= rsfilesHelper::ds();
		$root	= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		
		$briefcase	= rsfilesHelper::getRoot() == 'briefcase';
		
		if (empty($pks) && !$all) {
			$this->setError(JText::_('JERROR_NO_ITEMS_SELECTED'));
			return false;
		}
		
		try {
			$db		 = $this->getDbo();
			$query	 = $db->getQuery(true);
			
			if (isset($batch['CanEdit']) && is_array($batch['CanEdit'])) {
				$batch['CanEdit'] = implode(',', $batch['CanEdit']);
			} else {
				$batch['CanEdit'] = '';
			}
			
			if (isset($batch['CanDelete']) && is_array($batch['CanDelete'])) {
				$batch['CanDelete'] = implode(',', $batch['CanDelete']);
			} else {
				$batch['CanDelete'] = '';
			}
			
			if (isset($batch['CanView']) && is_array($batch['CanView'])) {
				$batch['CanView'] = implode(',', $batch['CanView']);
			} else {
				$batch['CanView'] = '';
			}
			
			if (isset($batch['CanDownload']) && is_array($batch['CanDownload'])) {
				$batch['CanDownload'] = implode(',', $batch['CanDownload']);
			} else {
				$batch['CanDownload'] = '';
			}
			
			foreach ($pks as $pk) {
				$fullpath = $root.$ds.urldecode($pk);
				
				if (rsfilesHelper::external($pk)) {
					$id = (int) $pk;
				} else {
					$query->clear()
						->select($db->qn('IdFile'))
						->from($db->qn('#__rsfiles_files'))
						->where($db->qn('FilePath').' = '.$db->q(urldecode($pk)));
						
					if ($briefcase) {
						$query->where($db->qn('briefcase').' = 1');
					}
						
					$db->setQuery($query);
					$id = (int) $db->loadResult();
				}
				
				if (!empty($id)) {
					$query->clear()
						->update($db->qn('#__rsfiles_files'))
						->set($db->qn('CanEdit').' = '.$db->q($batch['CanEdit']))
						->set($db->qn('CanDelete').' = '.$db->q($batch['CanDelete']))
						->set($db->qn('CanView').' = '.$db->q($batch['CanView']))
						->set($db->qn('CanDownload').' = '.$db->q($batch['CanDownload']))
						->where($db->qn('IdFile').' = '.$db->q($id));
						
						if ($batch['published'] != '-') {
							$query->set($db->qn('published').' = '.$db->q($batch['published']));
						}
					
					if (!is_dir($fullpath)) {
						if ($batch['FileStatistics'] != '-') {
							$query->set($db->qn('FileStatistics').' = '.$db->q($batch['FileStatistics']));
						}
						
						if ($batch['IdLicense'] != '-') {
							$query->set($db->qn('IdLicense').' = '.$db->q($batch['IdLicense']));
						}
						
						if ($batch['DownloadMethod'] != '-') {
							$query->set($db->qn('DownloadMethod').' = '.$db->q($batch['DownloadMethod']));
						}
						
						if ($batch['show_preview'] != '-') {
							$query->set($db->qn('show_preview').' = '.$db->q($batch['show_preview']));
						}
						
						$query->set($db->qn('DownloadLimit').' = '.$db->q($batch['DownloadLimit']));
					}
					
					$db->setQuery($query);
					$db->execute();
				} else {
					$query->clear()
						->insert($db->qn('#__rsfiles_files'))
						->set($db->qn('CanEdit').' = '.$db->q($batch['CanEdit']))
						->set($db->qn('CanDelete').' = '.$db->q($batch['CanDelete']))
						->set($db->qn('CanView').' = '.$db->q($batch['CanView']))
						->set($db->qn('CanDownload').' = '.$db->q($batch['CanDownload']))
						->set($db->qn('FilePath').' = '.$db->q(urldecode($pk)));
						
						if ($batch['published'] != '-') {
							$query->set($db->qn('published').' = '.$db->q($batch['published']));
						}
					
					if (!is_dir($fullpath)) {
						if ($batch['FileStatistics'] != '-') {
							$query->set($db->qn('FileStatistics').' = '.$db->q($batch['FileStatistics']));
						}
						
						if ($batch['IdLicense'] != '-') {
							$query->set($db->qn('IdLicense').' = '.$db->q($batch['IdLicense']));
						}
						
						if ($batch['DownloadMethod'] != '-') {
							$query->set($db->qn('DownloadMethod').' = '.$db->q($batch['DownloadMethod']));
						}
						
						if ($batch['show_preview'] != '-') {
							$query->set($db->qn('show_preview').' = '.$db->q($batch['show_preview']));
						}
						
						$query->set($db->qn('DownloadLimit').' = '.$db->q($batch['DownloadLimit']));
					}
					
					$db->setQuery($query);
					$db->execute();
				}
			}
			
			return true;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
}