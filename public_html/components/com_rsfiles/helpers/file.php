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

class RSFiles {
	protected $fullpath		= null;
	protected $relativePath	= null;
	protected $options		= null;
	protected $id			= 0;
	protected $admin			= null;
	
	public function __construct($fullpath, $options = null) {
		$this->admin			= JFactory::getApplication()->isAdmin();
		$this->fullpath			= $this->setFullpath($fullpath);
		$this->relativePath		= $this->setRelative();
		$this->id				= $this->setId();
		$this->options			= $options;
	}
	
	/**
	 * Method to get an instance of a RSFiles class.
	 *
	 * @param   string  $fullpath	The full path of the file/folder.
	 *
	 * @return  object  RSFiles instance.
	 *
	 */
	public static function getInstance($fullpath, $options = null) {
		static $instances = array();
		
		$className	= 'RSFiles';
		$hash		= md5($fullpath);
		
		if (!array_key_exists($hash, $instances)) {
			$instances[$hash] = new $className($fullpath, $options);
		}
		
		return $instances[$hash];
	}
	
	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 */
	public function __get($name) {
		$hash = md5($this->fullpath);
		
		switch ($name) {
			case 'info':
				static $info = array();
				if (!array_key_exists($hash, $info)) {
					$info[$hash] = $this->getInfo();
				}
				
				return $info[$hash];
			break;
			
			case 'mirrors':
				static $mirrors = array();
				if (!array_key_exists($hash,$mirrors)) {
					$mirrors[$hash] = $this->getMirrors();
				}
				
				return $mirrors[$hash];
			break;
			
			case 'screenshots':
				static $screenshots = array();
				if (!array_key_exists($hash,$screenshots)) {
					$screenshots[$hash] = $this->getScreenshots();
				}
				
				return $screenshots[$hash];
			break;
		}
		
		return null;
	}
	
	/**
	 *	Get folder/file specific information
	 */
	protected function getInfo() {
		$object		= new stdClass();
		$config		= rsfilesHelper::getConfig();
		$type		= $this->type();
		$entry		= $this->getEntry();
		$isPopular	= (int) $config->popular;
		$isNew		= (int) $config->new;
		$days		= $isNew - 2 * $isNew;
		$itemid		= isset($this->options['itemid']) ? $this->options['itemid'] : '';
		$root		= $this->admin ? rsfilesHelper::root() : JFactory::getSession()->get('rsf_absolute_root');
		$root		= isset($this->options['dld_fld']) ? $this->options['dld_fld'] : $root;
		$fullpath	= str_replace($root,'',$this->fullpath);
		$fullpath	= ltrim($fullpath,rsfilesHelper::ds());
		
		if ($this->admin) {
			if (isset($entry->FilePath)) {
				$fullpath = $entry->FilePath;
			}
		}
		
		$object->id					= !empty($entry->IdFile) ? $entry->IdFile : 0;
		$object->filename			= !empty($entry->FileName) ? $entry->FileName : '';
		$object->name				= $type == 'external' ? rsfilesHelper::getName($entry->FilePath) : rsfilesHelper::getName($this->fullpath);
		$object->type				= $type;
		$object->fullpath			= $type == 'external' ? (int) $entry->IdFile : urlencode($fullpath);
		$object->time				= !empty($entry->DateAdded) ? $entry->DateAdded : ($type == 'external' ? JFactory::getDate()->toSql() : JFactory::getDate(filemtime($this->fullpath))->toSql());
		$object->thumb				= !empty($entry->FileThumb) ? JURI::root().'components/com_rsfiles/images/thumbs/files/'.$entry->FileThumb : '';
		$object->dateadded 			= rsfilesHelper::showDate($object->time);
		$object->filedescription	= $config->show_descriptions == 0 ? '' : (empty($entry->FileDescription) ? JText::_('COM_RSFILES_NO_DESCRIPTION') : $entry->FileDescription);
		$object->published			= isset($entry->published) ? $entry->published : 1;
		
		if ($type == 'file' || $type == 'external') {
			$object->DownloadMethod		= !empty($entry->IdFile) ? $entry->DownloadMethod : 0;
			$object->IdLicense			= !empty($entry->IdFile) ? (int) $entry->IdLicense : 0;
			$object->LicenseName		= !empty($entry->LicenseName) ? $entry->LicenseName : '';
			$object->extension			= $type == 'external' ? strtolower(rsfilesHelper::getExt($entry->FilePath)) : strtolower(rsfilesHelper::getExt($this->fullpath));
			$mimetype					= rsfilesHelper::mimetype($object->extension);
			$object->size				= $type == 'external' ? '-' : (!empty($entry->FileSize) ? $entry->FileSize : rsfilesHelper::formatBytes(rsfilesHelper::filesize($this->fullpath)));
			$object->hits				= isset($entry->hits) ? (int) $entry->hits : 0;
			$object->downloads			= rsfilesHelper::downloads($type == 'external' ? (int) $entry->IdFile : $this->relativePath);
			$object->filetype			= $object->extension.($mimetype ? ' ('.JText::_('COM_RSFILES_MIMETYPE').' '.rsfilesHelper::mimetype($object->extension).')' : '');
			$object->checksum			= !empty($entry->hash) ? $entry->hash : JText::_('COM_RSFILES_NO_CHECKSUM');
			$object->stats				= isset($entry->FileStatistics) ? $entry->FileStatistics : '';
			$object->FileType			= !empty($entry->IdFile) ? $entry->FileType : 0;
			$object->owner				= !empty($entry->IdUser) ? JFactory::getUser($entry->IdUser)->get('username') : JText::_('COM_RSFILES_GUEST');
			$object->fileversion 		= !empty($entry->FileVersion) ? $entry->FileVersion : '';
			$object->Downloads 			= !empty($entry->Downloads) ? $entry->Downloads : 0;
			$object->DownloadLimit 		= !empty($entry->DownloadLimit) ? $entry->DownloadLimit : '';
			$object->popular			= (!empty($isPopular) && $object->hits >= $isPopular) ? true : false;
			$object->isnew				= (!empty($isNew) && ($this->timediff($object->time) > $days) && ($this->timediff($object->time) <= 0)) ? true : false;
			$object->lastmodified 		= $type == 'external' ? rsfilesHelper::showDate($object->time) : rsfilesHelper::showDate(filemtime($this->fullpath));
			$object->reports			= $this->getReport();
			$object->show_preview		= isset($entry->show_preview) ? $entry->show_preview : 1;
			$object->publish_down		= isset($entry->publish_down) ? $entry->publish_down : '';
			
			if (JFactory::getApplication()->isSite()) {
				$object->filelicense 	= empty($entry->IdLicense) ? '' : '<a class="rs_modal" rel="{handler: \'iframe\'}" href="'.JRoute::_('index.php?option=com_rsfiles&layout=license&tmpl=component&id='.rsfilesHelper::sef($entry->IdLicense,$entry->LicenseName).$itemid,false).'">'.$entry->LicenseName.'</a>';
			} else {
				$object->filelicense 	= empty($entry->IdLicense) ? '' : '<a href="'.JRoute::_('index.php?option=com_rsfiles&task=license.edit&IdLicense='.$entry->IdLicense.$itemid,false).'">'.$entry->LicenseName.'</a>';
			}
		}
		
		if ($type == 'folder') {
			$object->hits				= empty($entry->hits) ? 0 : (int) $entry->hits;
			$object->filesnumber		= $this->admin ? 0 : $this->countFiles();
		}
		
		return $object;
	}
	
	/**
	 *	Get the file/folder entry from the database
	 */
	protected function getEntry() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)->select($db->qn('f').'.*')->select($db->qn('l.LicenseName'))->from($db->qn('#__rsfiles_files','f'))->where($db->qn('f.IdFile').' = '.$this->id);
		$query->join('LEFT',$db->qn('#__rsfiles_licenses','l').' ON '.$db->qn('l.IdLicense').' = '.$db->qn('f.IdLicense'));
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 *	Get file mirrors
	 */
	protected function getMirrors() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)->select('*')->from($db->qn('#__rsfiles_mirrors'))->where($db->qn('IdFile').' = '.$this->id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 *	Get file screenshots
	 */
	protected function getScreenshots() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)->select('Path')->from($db->qn('#__rsfiles_screenshots'))->where($db->qn('IdFile').' = '.$this->id);
		$db->setQuery($query);
		$result = $db->loadColumn();
		return $result ? array_chunk($result, 4) : array();
	}
	
	/**
	 *	Set the file/folder ID
	 */
	protected function setId() {
		static $ids = array();
		
		$hash = md5($this->fullpath);
		
		if (!array_key_exists($hash, $ids)) {
			$db = JFactory::getDbo();
			
			if (rsfilesHelper::external($this->fullpath)) {
				$ids[$hash] = (int) $this->fullpath;
			} else {
				$query = $db->getQuery(true)->select('IdFile')->from($db->qn('#__rsfiles_files'))->where($db->qn('FilePath').' = '.$db->q($this->relativePath));
				$db->setQuery($query);
				$ids[$hash] = (int) $db->loadResult();
			}
		}
		
		return $ids[$hash];
	}
	
	/**
	 *	Set the fullpath of the file/folder
	 */
	protected function setFullpath($fullpath) {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$fullpath = str_replace('/',"\\",$fullpath);
		}
		
		return $fullpath;
	}
	
	/**
	 *	Get the relative path of the file/folder
	 */
	protected function setRelative() {
		$config		= rsfilesHelper::getConfig();
		$briefcase 	= $this->admin ? rsfilesHelper::getRoot() == 'briefcase' : rsfilesHelper::isBriefcase();
		$d_root		= realpath($config->download_folder);
		$b_root		= realpath($config->briefcase_folder);
		$root		= $briefcase ? $b_root : $d_root;
		$relative	= str_replace($root,'',$this->fullpath);
		$relative	= ltrim($relative,rsfilesHelper::ds());
		
		return $relative;
	}
	
	/**
	 *	Get the type of the current path
	 */
	protected function type() {
		if (rsfilesHelper::external($this->fullpath)) {
			return 'external';
		} else if (is_dir(realpath($this->fullpath))) {
			return 'folder';
		} else if (is_file(realpath($this->fullpath))) {
			return 'file';
		}
	}
	
	/**
	 *	Get the number of days between two dates
	 */
	protected function timediff($date1, $date2 = null) {
		$date1	= JFactory::getDate($date1)->toUnix();
		$date2	= is_null($date2) ? JFactory::getDate()->toUnix() : $date2;
		$diff	= ($date1 - $date2) / 86400;
		
		return (abs($diff) == $diff) ? ceil($diff) : floor($diff);
	}
	
	/**
	 *	Get all reports
	 */
	protected function getReports() {
		$db	= JFactory::getDbo();
		
		$query = $db->getQuery(true)->select('COUNT('.$db->qn('IdReport').') AS ReportsCount')
			->select($db->qn('IdFile'))
			->from($db->qn('#__rsfiles_reports'))
			->group($db->qn('IdFile'));
		
		$db->setQuery($query);
		return $db->loadObjectList('IdFile');
	}
	
	/**
	 *	Get the number of reports a file has
	 */
	protected function getReport() {
		$reports	= $this->getReports();
		$report		= !empty($reports[$this->id]->ReportsCount) ? $reports[$this->id]->ReportsCount : 0;
		
		if ($report <= 0) {
			return JText::_('COM_RSFILES_NO_REPORT');
		} elseif ($report == 1) {
			return JText::_('COM_RSFILES_ONE_REPORT');
		} else {
			return JText::sprintf('COM_RSFILES_MANY_REPORT',$report);
		}
	}
	
	protected function countFiles() {
		$db		= JFactory::getDbo();
		$files	= JFolder::files($this->fullpath,'.',false,true);
		$root 	= rsfilesHelper::root(true);
		
		static $cache;
		if (empty($cache)) {
			$db->setQuery("SELECT FilePath, FileType, published, FileParent FROM #__rsfiles_files");
			$cache = $db->loadObjectList('FilePath');
		}
		
		$externals = 0;
		foreach ($cache as $cache_item) {
			if ($cache_item->FileType && $cache_item->published && $cache_item->FileParent == $this->relativePath)
				$externals++;
		}
		
		if (!empty($files)) {
			foreach ($files as $i => $file) {
				$file = realpath($file);
				$file = str_replace($root,'',$file);
				
				$published = !empty($cache[$file]) ? (int) $cache[$file]->published : 1;
				
				//remove the unpublished files
				if ($published === 0) unset($files[$i]);
				if (!rsfilesHelper::permissions('CanView',$file)) { unset($files[$i]); }
			}
		}
		
		$total = count($files) + $externals;
		return $total;
	}
}