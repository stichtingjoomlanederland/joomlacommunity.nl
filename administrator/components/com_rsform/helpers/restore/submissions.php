<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProRestoreSubmissions
{
	// JDatabase instance
	protected $db;

	// Holds an array of the XML data.
	protected $formId;
	
	public function __construct($options = array()) {
		$path 	  			= &$options['path'];
		$this->formId		= isset($options['formId']) ? (int) $options['formId'] : 0;
		
		$this->db = JFactory::getDbo();
		
		// Check if the form's xml exists
		if (!file_exists($path)) {
			throw new Exception(sprintf('The file %s does not exist!', $path));
		}
		
		if (!is_readable($path)) {
			throw new Exception(sprintf('File %s is not readable!', $path));
		}
		
		// Attempt to load the XML data
		libxml_use_internal_errors(true);
		
		$this->xml = simplexml_load_file($path);
		
		if ($this->xml === false) {
			$errors = array();
			foreach (libxml_get_errors() as $error) {
				$errors[] = 'Message: '.$error->message.'; Line: '.$error->line.'; Column: '.$error->column;
			}
			throw new Exception(sprintf('Error while parsing XML: %s<br/>', implode('<br />', $errors)));
		}
	}
	
	public function restore() {		
		foreach ($this->xml->children() as $submission) {
			$data = array(
				$this->db->qn('FormId').'='.$this->db->q($this->formId)
			);
		
			foreach ($submission as $property => $value) {
				// Skip form ID for now
				if ($property == 'values') {
					continue;
				}
				
				$data[] = $this->db->qn($property).'='.$this->db->q((string) $value);
			}
			
			$query = $this->db->getQuery(true);
			$query	->insert('#__rsform_submissions')
					->set($data);
			$this->db->setQuery($query)->execute();
			
			$submissionId = $this->db->insertid();
			
			// insert submission values
			if (isset($submission->values)) {
				foreach ($submission->values->children() as $value) {
					
					$query = $this->db->getQuery(true);
					$query	->insert('#__rsform_submission_values')
							->set(array(
									$this->db->qn('FormId')			.'='.$this->db->q($this->formId),
									$this->db->qn('SubmissionId')	.'='.$this->db->q($submissionId),
									$this->db->qn('FieldName')		.'='.$this->db->q((string) $value->fieldname),
									$this->db->qn('FieldValue')		.'='.$this->db->q((string) $value->fieldvalue)
							));
					$this->db->setQuery($query)->execute();
				}
			}
		}
	}
	
}