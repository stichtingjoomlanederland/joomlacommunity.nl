<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesTableFile extends JTable
{

	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rsfiles_files', 'IdFile', $db);
	}
	
	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @link    http://docs.joomla.org/JTable/check
	 * @since   11.1
	 */
	public function check() {
		if (JFactory::getApplication()->isAdmin()) {			
			if ($this->FileType == 1) {
				$extension = rsfilesHelper::getExt($this->FilePath);
				
				if (empty($extension)) {
					$this->setError(JText::_('COM_RSFILES_EXTERNAL_WITH_NO_EXTENSION'));
					return false;
				}
			}
			
			if (isset($this->ScreenshotsTags) && is_array($this->ScreenshotsTags)) {
				$this->ScreenshotsTags = implode(',',$this->ScreenshotsTags);
			} else $this->ScreenshotsTags = '';
			
			if (isset($this->CanCreate) && is_array($this->CanCreate)) {
				$this->CanCreate = implode(',',$this->CanCreate);
			} else $this->CanCreate = '';
			
			if (isset($this->CanUpload) && is_array($this->CanUpload)) {
				$this->CanUpload = implode(',',$this->CanUpload);
			} else $this->CanUpload = '';
			
			if (isset($this->CanDelete) && is_array($this->CanDelete)) {
				$this->CanDelete = implode(',',$this->CanDelete);
			} else $this->CanDelete = '';
			
			if (isset($this->CanEdit) && is_array($this->CanEdit)) {
				$this->CanEdit = implode(',',$this->CanEdit);
			} else $this->CanEdit = '';
			
			if (isset($this->CanDownload) && is_array($this->CanDownload)) {
				$this->CanDownload = implode(',',$this->CanDownload);
			} else $this->CanDownload = '';
			
			if (isset($this->CanView) && is_array($this->CanView)) {
				$this->CanView = implode(',',$this->CanView);
			} else $this->CanView = '';
		}
		
		if (!empty($this->IdFile)) {
			$this->ModifiedDate = JFactory::getDate()->toSql();
		}
		
		if ($this->FileType && (empty($this->DateAdded) || $this->DateAdded == JFactory::getDbo()->getNullDate()))
			$this->DateAdded = JFactory::getDate()->toSql();
		
		if (is_file($this->FilePath) && empty($this->IdFile)) {
			$this->hash = md5_file($this->FilePath);
			$this->FileSize = rsfilesHelper::formatBytes(rsfilesHelper::filesize($this->FilePath));
		}
		
		return true;
	}
}