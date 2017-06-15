<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');

if (!function_exists('gzopen') && function_exists('gzopen64')) {
	function gzopen($filename, $mode, $use_include_path = 0) {
		return gzopen64($filename, $mode, $use_include_path);
	}
}

class RSFormProRestore
{
	// JDatabase instance
	protected $db;
	
	// Path to the Joomla! temporary folder
	protected $tmp;
	
	// Path to the backup
	protected $path;
	
	// This is the MD5 key that's used to identify the backup.
	protected $key;
	
	// The option for overwriting the existing forms.
	protected $overwrite;
	
	// The form that is parsed.
	protected $form;
	
	// The number of the submissions file
	protected $file;
	
	// Holds the FormId for the proper restore of the submissions form
	protected $formId;
	
	// Holds the setting for keeping the form's ids from the backup
	protected $keepId;
	
	public function __construct($options = array()) {
		$this->db 			= JFactory::getDbo();
		$this->tmp 			= JFactory::getConfig()->get('tmp_path');
		$this->key 			= isset($options['key']) ? $options['key'] : null;
		$this->overwrite 	= isset($options['overwrite']) ? $options['overwrite'] : null;
		$this->form 		= isset($options['form']) ? $options['form'] : null;
		$this->file			= isset($options['file']) ? (int) $options['file'] : 0;
		$this->formId		= isset($options['formId']) ? (int) $options['formId'] : 0;
		$this->keepId		= !empty($options['keepId']) ? true : false;
		
		// Check if the temporary folder is writable.
		if (!is_writable($this->tmp)) {
			throw new Exception(sprintf('The temporary folder "%s" is not writable!', $this->tmp));
		}
		
		// Generate a path where we will copy the backup.
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
	
	public function upload($file) {
		jimport('joomla.filesystem.file');
		
		// Upload it to the temp location.
		if (!JFile::upload($file['tmp_name'], $this->getPath(), false, true)) {
			throw new Exception(sprintf('Could not copy "%s" to "%s"!', $file['name'], $this->path));
		}
	}
	
	public function decompress() {
		$tgzFilePath = $this->getPath();
		
		if (!file_exists($tgzFilePath)) {
			throw new Exception(sprintf('File %s does not exist!', $tgzFilePath));
		}
		
		if (!is_readable($tgzFilePath)) {
			throw new Exception(sprintf('File %s is not readable!', $tgzFilePath));
		}
		
		// Open .tgz file for reading
		$gzHandle = @gzopen($tgzFilePath, 'rb');
		if (!$gzHandle) {
			throw new Exception(sprintf('Could not open %s for reading!', $tgzFilePath));
		}

		jimport('joomla.filesystem.file');
		
		while (!gzeof($gzHandle)) {
			if ($block = gzread($gzHandle, 512)) {				
				$meta['filename']  	= trim(substr($block, 0, 99));
				$meta['filesize']  	= octdec(substr($block, 124, 12));
				if ($bytes = ($meta['filesize'] % 512)) {
					$meta['nullbytes'] = 512 - $bytes;
				} else {
					$meta['nullbytes'] = 0;
				}
				
				if ($meta['filesize']) {
					// Make sure our extension is .xml
					if (($ext = JFile::getExt($meta['filename'])) != 'xml') {
						throw new Exception(sprintf('Attempted to extract a file with an invalid extension (%s) - archive might be damaged.', preg_replace('#[^a-z0-9]#is', '', $ext)));
					}
					
					// Let's see if somebody edited the archive manually and archived a folder...
					$meta['filename'] = str_replace('\\', '/', $meta['filename']);
					if (strpos($meta['filename'], '/') !== false)
					{
						$parts = explode('/', $meta['filename']);
						$meta['filename'] = end($parts);
					}
					
					// Make sure file does not contain invalid characters
					if (preg_match('/[^a-z_\-\.0-9]/i', JFile::stripExt($meta['filename']))) {
						throw new Exception('Attempted to extract a file with invalid characters in its name.');
					}
				
					$chunk	 = 1024*1024;
					$left	 = $meta['filesize'];
					$fHandle = @fopen($this->path.'/'.$meta['filename'], 'wb');
					
					if (!$fHandle) {
						throw new Exception(sprintf('Could not write data to file %s!', htmlentities($meta['filename'], ENT_COMPAT, 'utf-8')));
					}
					
					do {
						$left = $left - $chunk;
						if ($left < 0) {
							$chunk = $left + $chunk;
						}
						$data = gzread($gzHandle, $chunk);
						
						fwrite($fHandle, $data);
						
					} while ($left > 0);
					 
					fclose($fHandle);
				}
				
				if ($meta['nullbytes'] > 0) {
					gzread($gzHandle, $meta['nullbytes']);
				}
			}
		}
		gzclose($gzHandle);
	}
	
	protected function getMetadata() {
		$metadataFile = $this->path.'/metadata.xml';
		
		// Check if the metadata.xml exists
		if (!file_exists($metadataFile)) {
			throw new Exception(sprintf('The file %s does not exist!', $metadataFile));
		}
		// Check if the metadata.xml can be opened
		if (!is_readable($metadataFile)) {
			throw new Exception(sprintf('File %s is not readable!', $metadataFile));
		}
		
		// Attempt to load the XML data
		libxml_use_internal_errors(true);
		
		if ($data = simplexml_load_file($metadataFile)) {
			return $data;
		} else {
			$errors = array();
			foreach (libxml_get_errors() as $error) {
				$errors[] = 'Message: '.$error->message.'; Line: '.$error->line.'; Column: '.$error->column;
			}
			throw new Exception(sprintf('Error while parsing XML: %s<br/>', implode('<br />', $errors)));
		}
	}
	
	public function getInfo() {
		$metadata = $this->getMetadata();
		
		$info 	  = array();
		$metaInfo = array();
		
		foreach($metadata->children() as $property => $value) {
			if ($property == 'forms') {
				continue;
			}
			$metaInfo[$property] = (string) $value;
		}
		
		if (isset($metadata->forms)) {
			foreach ($metadata->forms->form as $form) {
				$info[] = array(
					'id' 			=> (string) $form->id,
					'name' 			=> (string) $form->name,
					'title' 		=> (string) $form->title,
					'submissions' 	=> (string) $form->submissions
				);
			}
		}
		
		if (!$info) {
			throw new Exception(sprintf('No forms were found in %s!', $this->path.'/metadata.xml'));
		}
		
		return (object) array(
			'info' 		=> $info,
			'metaInfo' 	=> $metaInfo
		);
	}
	
	public function overwriteForms() {
		if (!is_null($this->overwrite) && $this->overwrite == 1) {
			$db 	= JFactory::getDbo();
			$tables = array(
				// Form fields
				'#__rsform_forms',
				'#__rsform_components',
				'#__rsform_properties',
				// Submissions
				'#__rsform_submissions',
				'#__rsform_submission_columns',
				'#__rsform_submission_values',
				// Translations
				'#__rsform_translations',
				// Mappings
				'#__rsform_mappings',
				// Post to Location
				'#__rsform_posts',
				// Conditions
				'#__rsform_conditions',
				'#__rsform_condition_details',
				// Calculations
				'#__rsform_calculations',
				// Directory
				'#__rsform_directory',
				'#__rsform_directory_fields',
				// Additional Emails
				'#__rsform_emails'
			);
			
			foreach ($tables as $table) {
				$db->truncateTable($table);
			}
			
			// Allow plugins to clear their tables as well
			JFactory::getApplication()->triggerEvent('rsfp_bk_onFormRestoreTruncate');
		}
	}
	
	public function parseForm() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/restore/form.php';
		
		$info = $this->getInfo();
		
		$form = new RSFormProRestoreForm(array(
			'path' 		=> $this->path.'/'.$this->form.'.xml',
			'keepId' 	=> $this->keepId,
			'metaData'	=> $info->metaInfo
		));
		
		$form->restore();
		
		return $form->getFormId();
	}
	
	public function parseSubmissions() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/restore/submissions.php';
		
		$submissions = new RSFormProRestoreSubmissions(array(
			'path' => $this->path.'/'.$this->form.'-data-'.$this->file.'.xml',
			'formId' => $this->formId
		));
		
		$submissions->restore();
	}
	
	public function checkNextFile($number) {
		if (file_exists($this->path.'/'.$this->form.'-data-'.$number.'.xml')) {
			return $number;
		} else {
			return 0;
		}
	}
	
	public function getKey() {
		if (empty($this->key)) {
			$this->key = md5(mt_rand());
		}
		
		return $this->key;
	}
	
	public function getPath() {
		return $this->path.'/backup.tgz';
	}
}