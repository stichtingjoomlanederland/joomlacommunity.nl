<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelFix extends JModelLegacy
{
	const DS = DIRECTORY_SEPARATOR;
	
	public function __construct() {
		parent::__construct();		
	}
	
	public function renameAdminUser($id) {
		$input 		= RSInput::create();
		$user  		= JFactory::getUser($id);
		$username 	= $input->get('username', '', 'none');
		
		if ($username == 'admin' || $username == '') {
			$this->setError(JText::_('COM_RSFIREWALL_PLEASE_CHOOSE_ANOTHER_USERNAME'));
			return false;
		}
		
		$user->set('username', $username);
		if (!$user->save(true)) {
			$this->setError($user->getError());
			return false;
		}
		
		return true;
	}
	
	public function saveConfiguration($data) {
		if (!$this->saveFile(JPATH_CONFIGURATION.'/configuration.php', $data)) {
			$this->setError(JText::_('COM_RSFIREWALL_CONFIGURATION_SAVED_ERROR'));
			return false;
		}
		
		return true;
	}
	
	public function savePHPini($data) {
		if (!$this->saveFile(JPATH_SITE.'/php.ini', $data)) {
			$this->setError(JText::sprintf('COM_RSFIREWALL_PHP_INI_SAVED_ERROR', JPATH_SITE));
			return false;
		}
		
		return true;
	}
	
	public function saveFile($filename, $data) {
		jimport('joomla.filesystem.file');
		// using @ because file_put_contents outputs a warning when unsuccessful
		return @JFile::write($filename, $data);
	}
	
	public function loadFile($filename) {
		jimport('joomla.filesystem.file');
		return file_get_contents($filename);
	}
	
	public function ignoreFiles() {
		$input 	= JFactory::getApplication()->input;
		$files 	= $input->get('files', array(), 'array');
		$flags 	= $input->get('flags', array(), 'array');
		
		$db	   = $this->getDbo();
		$query = $db->getQuery(true);
		
		try {
			foreach ($files as $i => $file) {
				$table = JTable::getInstance('Hashes', 'RSFirewallTable', array());
				// 'M' => missing
				// ''  => no flag, default
				// for missing files we add a M flag so that we don't check the hash
				$flag = $flags[$i];
				
				// full path to the file
				$file_path 	= JPATH_SITE.DIRECTORY_SEPARATOR.$file;
				
				if (file_exists($file_path) && !is_readable($file_path)) {
					$this->setError(JText::sprintf('COM_RSFIREWALL_COULD_NOT_READ_HASH_FILE', $file_path));
					return false;
				}
				
				if ($flag == 'M') {
					// no hash for a missing file
					$file_hash = '';
				} else {
					// this check is done only when the file exists and has the wrong hash
					if (!is_file($file_path)) {
						$this->setError(JText::sprintf('COM_RSFIREWALL_FILE_NOT_FOUND', $file_path));
						return false;
					}
					// calculate the hash
					$file_hash = md5_file($file_path);
				}
				
				$query->select($db->qn('id'))
					  ->from($db->qn('#__rsfirewall_hashes'))
					  ->where($db->qn('file').'='.$db->q($file))
					  ->where($db->qn('type').'='.$db->q('ignore'));
				
				$db->setQuery($query);
				if ($id = $db->loadResult()) {
					$table->load($id);
					$table->bind(array(
						'hash' => $file_hash,
						'flag' => $flag,
						'date' => JFactory::getDate()->toSql()
					));
				} else {
					$table->bind(array(
						'file' => $file,
						'hash' => $file_hash,
						'type' => 'ignore',
						'flag' => $flag,
						'date' => JFactory::getDate()->toSql()
					));
				}
				
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
				
				$query->clear();
			}
			
			return true;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	public function setPermissions($paths, $perms) {
		$success = array();
		if (!is_array($paths))
			$paths = (array) $paths;
		
		foreach ($paths as $path) {
			$success[] = (int) @chmod(JPATH_SITE.self::DS.$path, octdec($perms));
		}
		
		// the result will be an array with the same length as the input array
		// 0 for failure, 1 for success
		return $success;
	}
}