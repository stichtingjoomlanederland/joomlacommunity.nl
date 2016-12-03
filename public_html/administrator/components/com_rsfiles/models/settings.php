<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelSettings extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_RSFILES';
	
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
		$jinput = JFactory::getApplication()->input;
		
		// Get the form.
		$form = $this->loadForm('com_rsfiles.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		$data = (array) $this->getConfig();
		$data['download_cancreate'] = explode(',',$data['download_cancreate']);
		$data['download_canupload'] = explode(',',$data['download_canupload']);
		
		return $data;
	}
	
	/**
	 * Method to get Tabs
	 *
	 * @return	mixed	The Joomla! Tabs.
	 * @since	1.6
	 */
	public function getTabs() {
		$tabs = new RSTabs('settings');
		return $tabs;
	}
	
	/**
	 * Method to get the configuration data.
	 *
	 * @return	mixed	The data for the configuration.
	 * @since	1.6
	 */
	public function getConfig() {
		return rsfilesHelper::getConfig();
	}
	
	/**
	 * Method to get the available layouts.
	 *
	 * @return	mixed	The available layouts.
	 * @since	1.6
	 */
	public function getLayouts() {
		$fields = array('general', 'files', 'captcha', 'frontend', 'emails');
		
		if (rsfilesHelper::isRsmail()) {
			$fields[] = 'rsmail';
		}
		
		return $fields;
	}
	
	
	/**
	 * Method to save configuration.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function save($data) {
		jimport('joomla.filesystem.file');
		
		if (isset($data['download_cancreate']) && is_array($data['download_cancreate']))
			$data['download_cancreate'] = implode(',',$data['download_cancreate']);
		else $data['download_cancreate'] = '';
		
		if (isset($data['download_canupload']) && is_array($data['download_canupload']))
			$data['download_canupload'] = implode(',',$data['download_canupload']);
		else $data['download_canupload'] = '';
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsfiles_config'));
		
		$db->setQuery($query);
		if ($configuration = $db->loadObjectList()) {
			foreach($configuration as $config) {
				if (isset($data[$config->ConfigName])) {
					$query->clear()
						->update($db->qn('#__rsfiles_config'))
						->set($db->qn('ConfigValue').' = '.$db->q($data[$config->ConfigName]))
						->where($db->qn('ConfigName').' = '.$db->q($config->ConfigName));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		$config  = $this->getConfig();
		$dsecure = JFactory::getApplication()->input->getInt('rsfl_htaccess',0);
		$bsecure = JFactory::getApplication()->input->getInt('rsfl_htaccess_briefcase',0);
		$secure  = 'deny from all';
		
		if ($dsecure) {
			if (!file_exists($config->download_folder.'/.htaccess')) {
				JFile::write($config->download_folder.'/.htaccess',$secure);
			}
		}
		
		if ($bsecure) {
			if (!file_exists($config->briefcase_folder.'/.htaccess')) {
				JFile::write($config->briefcase_folder.'/.htaccess',$secure);
			}
		}
		
		return true;
	}
	
	/**
	 * Method to save download folders.
	 *
	 * @return	boolean		True if success.
	 */
	public function savepath() {
		jimport('joomla.filesystem.folder');
		
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$folder		= $input->getString('thefolder','');
		$type		= $input->getString('type','');
		$config		= $this->getConfig();
		
		if ($type == 'download') {
			if ($config->briefcase_folder == urldecode($folder)) {
				$this->setError(JText::_('COM_RSFILES_SAME_DOWNLOAD_FOLDERS'));
				return false;
			}
		} else if ($type == 'briefcase') {
			if ($config->download_folder == urldecode($folder)) {
				$this->setError(JText::_('COM_RSFILES_SAME_DOWNLOAD_FOLDERS'));
				return false;
			}
		}
		
		$query->clear()
			->update($db->qn('#__rsfiles_config'))
			->set($db->qn('ConfigValue').' = '.$db->q(urldecode($folder)))
			->where($db->qn('ConfigName').' = '.$db->q($type.'_folder'));
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	
	/**
	 * Method to get the current folder.
	 *
	 * @return	string		The folder path.
	 */
	public function getCurrentFolder() {
		$type	= JFactory::getApplication()->input->getString('type','');
		$config	= $this->getConfig();
		$ds		= rsfilesHelper::ds();
		
		if (!empty($config->{$type.'_folder'})) {
			$default = explode($ds,$config->{$type.'_folder'});
			if (count($default) > 1)
				array_pop($default);
			$default = implode($ds,$default);
		} else $default = '';
		
		$folder = JFactory::getApplication()->input->getString('folder',$default);
		
		if (is_dir($folder)) {
			$folder = realpath($folder);
		} else {
			$folder = realpath(JPATH_SITE);
		}
		
		if (DIRECTORY_SEPARATOR == '\\') {
			$folder = rtrim($folder, DIRECTORY_SEPARATOR);
		}
		
		return $folder;
	}
	
	/**
	 * Method to get the current folder elements.
	 *
	 * @return	array		The folder elements.
	 */
	public function getElements() {
		$folder = $this->getCurrentFolder();
		$ds		= rsfilesHelper::ds();
		
		$elements = explode($ds, $folder);
		$navigation_path = '';
		
		if (!empty($elements)) {
			foreach($elements as $i=>$element) {
				$navigation_path .= $element;
				$newelement = new stdClass();
				$newelement->name = $element;
				$newelement->fullpath = urlencode($navigation_path);
				$elements[$i] = $newelement;
				$navigation_path .= $ds;
			}
		}
		
		return $elements;
	}
	
	/**
	 * Method to get the folders.
	 *
	 * @return	array		The folders.
	 */
	public function getFolders() {
		jimport('joomla.filesystem.folder');
		$folders	= array();
		$current	= $this->getCurrentFolder();
		$ds			= rsfilesHelper::ds();
		
		$all_folders = JFolder::folders($current);
		foreach ($all_folders as $folder) {
			$element = new stdClass();
			$element->name = $folder;
			$element->fullpath = urlencode($current.$ds.$folder);
			$folders[] = $element;
		}
		
		return $folders;
	}
	
	/**
	 * Method to get the previous folder.
	 *
	 * @return	string		The folder path.
	 */
	public function getPrevious() {
		$current	= $this->getCurrentFolder();
		$ds			= rsfilesHelper::ds();
		$elements	= explode($ds, $current);
		
		if (count($elements) > 1)
			array_pop($elements);
		return urlencode(implode($ds, $elements));
	}
	
	/**
	 * Method to get RSMail list fields
	 *
	 * @return	array		The fields.
	 */
	public function fields() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$id			= JFactory::getApplication()->input->getInt('id',0);
		
		$query->select($db->qn('FieldName'))
			->from($db->qn('#__rsmail_list_fields'))
			->where($db->qn('IdList').' = '.$id);
		$db->setQuery($query);
		$fields = $db->loadColumn();
		
		return json_encode($fields);
	}
}