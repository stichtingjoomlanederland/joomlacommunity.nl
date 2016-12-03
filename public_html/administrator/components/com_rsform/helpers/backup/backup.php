<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/helper.php';
require_once dirname(__FILE__).'/xml.php';
require_once dirname(__FILE__).'/tar.php';

jimport('joomla.filesystem.folder');

class RSFormProBackup
{
	// JDatabase instance
	protected $db;
	
	// Path to the Joomla! temporary folder
	protected $tmp;
	
	// Path to the backup
	protected $path;
	
	// Path to metadata file
	protected $metadata;
	
	// Holds an array of form IDs.
	protected $formIds;
	
	// Holds an array of forms.
	protected $forms;
	
	// True to export submissions as well.
	protected $submissions;
	
	// This is the MD5 key that's used to identify the backup.
	protected $key;
	
	// set the desired name for the archive
	protected $name;
	
	public function __construct($options = array()) {
		$this->db 			= JFactory::getDbo();
		$this->tmp 			= JFactory::getConfig()->get('tmp_path');
		$this->formIds 		= isset($options['forms']) ? $options['forms'] : array();
		$this->submissions 	= isset($options['submissions']) ? $options['submissions'] : 0;
		$this->key 			= isset($options['key']) ? $options['key'] : null;
		$this->name 		= isset($options['name']) ? $options['name'] : null;
		
		// Check if the temporary folder is writable.
		if (!is_writable($this->tmp)) {
			throw new Exception(sprintf('The temporary folder "%s" is not writable!', $this->tmp));
		}
		
		// Generate a path where we will store the backup contents.
		$this->path = $this->tmp.'/rsform_backup_'.$this->getKey();
		
		// Let's create our folder if it doesn't exist.
		if (!is_dir($this->path) && !JFolder::create($this->path)) {
			throw new Exception(sprintf('Could not create temporary path "%s"!', $this->path));
		}
		
		// Check if the newly created path (or supplied one) is writable.
		if (!is_writable($this->path)) {
			throw new Exception(sprintf('Path "%s" is not writable!', $this->path));
		}
	}
	
	// Store metadata.xml
	public function storeMetaData() {
		// Load forms from the database
		$db	   = &$this->db;
		$query = $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_forms'))
			  ->where($db->qn('FormId').' IN ('.RSFormProBackupHelper::qi($this->formIds).')');
		$db->setQuery($query);
		$this->forms = $db->loadObjectList('FormId');
		
		// Count the number of submissions as well, if requested.
		if ($this->submissions) {
			$query->clear()
				  ->select('COUNT('.$db->qn('SubmissionId').') AS '.$db->qn('count'))
				  ->select($db->qn('FormId'))
				  ->from($db->qn('#__rsform_submissions'))
				  ->where($db->qn('FormId').' IN ('.RSFormProBackupHelper::qi($this->formIds).')')
				  ->group($db->qn('FormId'));
			$db->setQuery($query);
			if ($submissions = $db->loadObjectList()) {
				foreach ($submissions as $submission) {
					if (isset($this->forms[$submission->FormId])) {
						$this->forms[$submission->FormId]->SubmissionsCount = $submission->count;
					}
				}
			}
		}
		
		// Initialize XML writer.
		$xml = new RSFormProBackupXML;
		
		$xml->addHeader()
			->add('rsform')
				// Software environment
				->add('version', 	(string) new RSFormProVersion)
				->add('cms',		JVERSION)
				->add('php', 		phpversion())
				->add('os',			PHP_OS)
				// Website information
				->add('url', 		JUri::root())
				->add('root',		JPATH_ROOT)
				->add('author', 	JFactory::getUser()->get('email'))
				->add('date', 		JFactory::getDate()->toSql());
		
		// Start adding form information
		if ($this->forms) {
			$xml->add('forms');
			foreach ($this->forms as $form) {
				$xml->add('form')
					->add('id',			 RSFormProBackupHelper::getHash($form->FormId))
					->add('name', 		 $form->FormName)
					->add('title', 		 $form->FormTitle)
					->add('submissions', !empty($form->SubmissionsCount) ? $form->SubmissionsCount : 0)
				->add('/form');
			}
			$xml->add('/forms');
		}
		
		// Finishup
		$xml->add('/rsform');
		
		$archive = new RSFormProTar($this->getPath());
		$buffer = (string) $xml;
		$size	= strlen($buffer);
		
		$archive->addHeader($size, 'metadata.xml');
		$archive->add($buffer);
		$archive->addPadding($size);
		
		$archive->close();
	}
	
	// Store form structure
	public function storeForms() {
		require_once dirname(__FILE__).'/form.php';
		
		foreach ($this->formIds as $form) {
			$part = new RSFormProBackupForm(array(
				'path'	=> $this->getPath(),
				'form'	=> $form
			));
			
			$part->store();
		}
	}
	
	// Store submissions
	public function storeSubmissions($start = 0, $limit = 100, $header = 0) {
		require_once dirname(__FILE__).'/submissions.php';
		
		$backupSubmission = new RSFormProBackupSubmissions(array(
			'path'			=> $this->getPath(),
			'form' 			=> reset($this->formIds),
			'start' 		=> $start,
			'limit' 		=> $limit,
			'header'		=> $header
		));
		return $backupSubmission->store();
	}
	
	// Download backup contents.
	public function download($clean = true) {
		$tar  = $this->getPath();
		$gzip = substr($tar, 0, -3).'tgz';
		
		// If there's a .TAR archive, we no longer need it, remove it.
		if ($clean && file_exists($tar)) {
			@unlink($tar);
		}
		
		if (!file_exists($gzip)) {
			throw new Exception(sprintf('File %s does not exist!', $gzip));
		}
		
		if (!is_readable($gzip)) {
			throw new Exception(sprintf('File %s is not readable!', $gzip));
		}
		if (!is_null($this->name)) {
			$name = $this->prepareName($this->name);
		} else {
			$name = 'backup';
		}
		RSFormProHelper::readFile($gzip, $name.'.tgz');
	}
	
	public function getPath() {
		return $this->path.'/backup.tar';
	}
	
	protected function prepareName($name) {
		$domain = JUri::getInstance()->getHost();
		$date   = JHtml::_('date', 'now', 'Y-m-d_H-i');
		
		return str_replace(array('{domain}', '{date}'), array($domain, $date), $name);
	}
	
	public function getKey() {
		if (empty($this->key)) {
			$this->key = md5(mt_rand());
		}
		
		return $this->key;
	}
}