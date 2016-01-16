<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class rseventsproModelMedia extends JModelLegacy
{
	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Optional parameter name
	 * @param   mixed   $default   Optional default value
	 *
	 * @return  object  The property where specified, the state object where omitted
	 *
	 * @since   1.5
	 */
	public function getState($property = null, $default = null) {
		static $set;

		if (!$set) {
			$input  = JFactory::getApplication()->input;
			$folder = $input->get('folder', '', 'path');
			$this->setState('folder', $folder);
			$set = true;
		}

		return parent::getState($property, $default);
	}
	
	/**
	 * Get the images on the current folder
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function getImages() {
		$list = $this->getList();

		return $list['images'];
	}

	/**
	 * Get the folders on the current folder
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function getFolders() {
		$list = $this->getList();

		return $list['folders'];
	}
	
	/**
	 * Build imagelist
	 *
	 * @return  array
	 *
	 * @since 1.5
	 */
	public function getList() {
		static $list;

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = (string) $this->getState('folder');
		
		$params = JComponentHelper::getParams('com_media');
		$media	= JPATH_ROOT . '/' . $params->get('image_path', 'images');

		$basePath = $media . ((strlen($current) > 0) ? '/' . $current : '');

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', $media . '/');

		$images  = array ();
		$folders = array ();

		$fileList = false;
		$folderList = false;

		if (file_exists($basePath)) {
			// Get the list of files and folders from the given folder
			$fileList	= JFolder::files($basePath);
			$folderList = JFolder::folders($basePath);
		}

		// Iterate over the files if they exist
		if ($fileList !== false) {
			foreach ($fileList as $file) {
				if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {
					$tmp = new JObject;
					$tmp->name = $file;
					$tmp->title = $file;
					$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);

					$ext = strtolower(JFile::getExt($file));

					switch ($ext) {
						// Image
						case 'jpg':
						case 'png':
						case 'jpeg':
							$images[] = $tmp;
						break;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false) {
			foreach ($folderList as $folder) {
				$tmp = new JObject;
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'images' => $images);

		return $list;
	}
	
	public function getUrl() {
		$params = JComponentHelper::getParams('com_media');
		return JUri::root() . $params->get('image_path', 'images');
	}
	
	public function getPrevious() {
		// Get current path from request
		$current = (string) $this->getState('folder');
		
		if ($current) {
			$current = str_replace(DIRECTORY_SEPARATOR, '/', $current);
			$paths	 = explode('/', $current);
			array_pop($paths);
			return  implode('/',$paths);
		}
		
		return '';
	}
}