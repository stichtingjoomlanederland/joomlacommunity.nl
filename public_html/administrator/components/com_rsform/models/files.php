<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelFiles extends JModelLegacy
{
	protected $_folder = null;
	
	public function __construct() {
		parent::__construct();
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		
		$this->_folder = JPATH_SITE;

		$folder = JFactory::getApplication()->input->getString('folder');
		if (is_dir($folder)) {
			$folder = rtrim($folder, '\\/');
			$this->_folder = $folder;
		}
	}
	
	public function getFolders() {
		$return = array();
		
		$folders = JFolder::folders($this->_folder);
		foreach ($folders as $folder) {
			$return[] = (object) array(
				'name' 		=> $folder,
				'fullpath' 	=> $this->_folder . DIRECTORY_SEPARATOR . $folder
			);
		}
		
		return $return;
	}
	
	public function getFiles() {
		$return = array();
		
		$files = JFolder::files($this->_folder);
		foreach ($files as $file) {
			$return[] = (object) array(
				'name' 		=> $file,
				'fullpath' 	=> $this->_folder . DIRECTORY_SEPARATOR . $file
			);
		}
		
		return $return;
	}
	
	public function getElements()
	{
		$elements = explode(DIRECTORY_SEPARATOR, $this->_folder);
		$navigation_path = '';
		
		if(!empty($elements))
			foreach($elements as $i=>$element)
			{
				$navigation_path .= $element;
				$newelement = new stdClass();
				$newelement->name = $element;
				$newelement->fullpath = $navigation_path;
				$elements[$i] = $newelement;
				$navigation_path .= DIRECTORY_SEPARATOR;
			}
		
		return $elements;
	}
	
	public function getCurrent() {
		return $this->_folder;
	}
	
	public function getPrevious() {
		$elements = explode(DIRECTORY_SEPARATOR, $this->_folder);
		if (count($elements) > 1)
			array_pop($elements);
		return implode(DIRECTORY_SEPARATOR, $elements);
	}
	
	public function upload() {
	    $upload = JFactory::getApplication()->input->files->get('upload');
		if (!$upload['error'])
		{
            return JFile::upload($upload['tmp_name'], $this->getCurrent() . '/' . JFile::getName($upload['name']), false, true);
        }

		return false;
	}
	
	public function getCanUpload() {
		return is_writable($this->_folder);
	}
	
	public function getUploadFile() {
        $upload = JFactory::getApplication()->input->files->get('upload');
		
		return JFile::getName($upload['name']);
	}
}