<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelFile extends JModelAdmin
{
	protected $text_prefix = 'COM_RSFILES';
	
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'File', $prefix = 'rsfilesTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem($pk)) {
			
			if ($item->publish_down == JFactory::getDbo()->getNullDate()) {
				$item->publish_down = '';
			}
			
			if (!empty($item->ScreenshotsTags))
				$item->ScreenshotsTags = explode(',',$item->ScreenshotsTags);
			
			if (!empty($item->CanCreate))
				$item->CanCreate = explode(',',$item->CanCreate);
			
			if (!empty($item->CanUpload))
				$item->CanUpload = explode(',',$item->CanUpload);
			
			if (!empty($item->CanDelete))
				$item->CanDelete = explode(',',$item->CanDelete);
			
			if (!empty($item->CanEdit))
				$item->CanEdit = explode(',',$item->CanEdit);
			
			if (!empty($item->CanDownload))
				$item->CanDownload = explode(',',$item->CanDownload);
			
			if (!empty($item->CanView))
				$item->CanView = explode(',',$item->CanView);
			
			if (!empty($item->IdFile)) {
				if ($item->FileType) {
					$item->type = 'remote';
				} else {
					$item->type = 'local';
				}
			} else $item->type = 'remote';
			
			if ($item->DownloadLimit == 0)
				$item->DownloadLimit = '';
			
			if (empty($item->IdFile)) {
				$item->FileParent = urldecode(JFactory::getApplication()->input->getString('parent',''));
			}
			
		}
		return $item;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_rsfiles.file', 'file', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		
		$type = rsfilesHelper::getType($form->getValue('IdFile',null,0));
		if ($type == 'external') {
			$form->setFieldAttribute('FilePath','required','true');
			$form->setFieldAttribute('FilePath','readonly','false');
			$form->setValue('FileType',null,1);
		}
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_rsfiles.edit.file.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	/**
	 * Method to get Tabs
	 *
	 * @return	mixed	The Joomla! Tabs.
	 * @since	1.6
	 */
	public function getTabs() {
		$tabs = new RSTabs('file');
		return $tabs;
	}
	
	/**
	 * Method to get the available layouts.
	 *
	 * @return	mixed	The available layouts.
	 * @since	1.6
	 */
	public function getLayouts() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('IdFile',0);
		$root	= rsfilesHelper::getConfig(rsfilesHelper::getRoot().'_folder');
		$ds		= rsfilesHelper::ds();
		
		$query->clear()
			->select($db->qn('FilePath'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('IdFile').' = '.$id);
		$db->setQuery($query);
		$path = $db->loadResult();
		
		if (rsfilesHelper::getRoot() == 'briefcase') {
			if (!empty($path) && is_dir($root.$ds.$path)) {
				$fields = array('general');
			} else {
				$fields = array('general', 'metadata', 'mirrors', 'screenshots');
			}
		} else {
			if (!empty($path) && is_dir($root.$ds.$path)) {
				$fields = array('general', 'permissions');
			} else {
				$fields = array('general', 'permissions', 'metadata', 'mirrors', 'screenshots');
			}
		}
		
		return $fields;
	}
	
	/**
	 * Method to get file mirrors.
	 *
	 * @return	mixed	The available mirrors.
	 * @since	1.6
	 */
	public function getMirrors() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('IdFile',0);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsfiles_mirrors'))
			->where($db->qn('IdFile').' = '.$id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Method to save file mirrors.
	 */
	public function mirror() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$id		= $input->getInt('id',0);
		$name	= $input->getString('name','');
		$url	= $input->getString('url','');
		$type	= $input->get('type','');
		$data	= new stdClass();
		
		
		if ($type == 'update') {
			$query->clear()
				->update($db->qn('#__rsfiles_mirrors'))
				->set($db->qn('MirrorName').' = '.$db->q($name))
				->set($db->qn('MirrorURL').' = '.$db->q($url))
				->where($db->qn('IdMirror').' = '.$id);
			
			$db->setQuery($query);
			$db->execute();
			
			return $id;
		} else {
			$query->clear()
				->insert($db->qn('#__rsfiles_mirrors'))
				->set($db->qn('MirrorName').' = '.$db->q($name))
				->set($db->qn('MirrorURL').' = '.$db->q($url))
				->set($db->qn('IdFile').' = '.$id);
			
			$db->setQuery($query);
			$db->execute();
			
			return $db->insertid();
		}
	}
	
	/**
	 * Method to delete file mirrors.
	 */
	public function deletemirror($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rsfiles_mirrors'))
			->where($db->qn('IdMirror').' = '.$id);
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * Method to get a file mirror.
	 */
	public function getmirror($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsfiles_mirrors'))
			->where($db->qn('IdMirror').' = '.$id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Method to get screenshots.
	 */
	public function getScreenshots() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('IdFile',0);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsfiles_screenshots'))
			->where($db->qn('IdFile').' = '.$id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 *	Method to delete a file screenshot
	 */
	public function deletescreenshot($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('Path'))
			->from($db->qn('#__rsfiles_screenshots'))
			->where($db->qn('IdScreenshot').' = '.$id);
		$db->setQuery($query);
		if ($path = $db->loadResult()) {
			if (JFile::exists(JPATH_SITE.'/components/com_rsfiles/images/screenshots/'.$path)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/screenshots/'.$path)) {
					$query->clear()->delete()->from($db->qn('#__rsfiles_screenshots'))->where($db->qn('IdScreenshot').' = '.$id);
					$db->setQuery($query);
					$db->execute();
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 *	Method to delete the file thumb
	 */
	public function deletethumb($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('FileThumb'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('IdFile').' = '.(int) $id);
		
		$db->setQuery($query);
		$thumb = $db->loadResult();
		
		if (!empty($thumb)) {
			if (JFile::exists(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$thumb)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/thumbs/files/'.$thumb)) {
					$query->clear()
						->update($db->qn('#__rsfiles_files'))
						->set($db->qn('FileThumb').' = '.$db->q(''))
						->where($db->qn('IdFile').' = '.(int) $id);
					$db->setQuery($query);
					$db->execute();
					
					return true;
				}
			}
		}
		
		$this->setError(JText::_('COM_RSFILES_THUMB_DELETE_ERROR'));
		return false;
	}
	
	/**
	 *	Method to delete the file preview
	 */
	public function deletepreview($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('preview'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('IdFile').' = '.(int) $id);
		
		$db->setQuery($query);
		$preview = $db->loadResult();
		
		if (!empty($preview)) {
			if (JFile::exists(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$preview)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rsfiles/images/preview/'.$preview)) {
					$query->clear()
						->update($db->qn('#__rsfiles_files'))
						->set($db->qn('preview').' = '.$db->q(''))
						->where($db->qn('IdFile').' = '.(int) $id);
					$db->setQuery($query);
					$db->execute();
					
					return true;
				}
			}
		}
		
		$this->setError(JText::_('COM_RSFILES_PREVIEW_DELETE_ERROR'));
		return false;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data) {
		// Initialise variables;
		$table = $this->getTable();
		$pk = (!empty($data['IdFile'])) ? $data['IdFile'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		
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
		
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		rsfilesHelper::upload($table->IdFile);
		
		$this->setState($this->getName() . '.id', $table->IdFile);
		
		return true;
	}
}