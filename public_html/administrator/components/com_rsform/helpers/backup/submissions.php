<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/helper.php';

class RSFormProBackupSubmissions
{
	// JDatabase instance
	protected $db;
	
	// Holds the form ID.
	protected $id;
	
	// Limit start for SQL query.
	protected $limitstart;
	
	// Limit for SQL query.
	protected $limit;
	
	// Position of 512-bytes TAR dummy header.
	protected $header = 0;
	
	// The path to the backup archive.
	protected $path;
	
	// The XML Writer object
	protected $xml;
	
	public function __construct($options = array()) {
		require_once dirname(__FILE__).'/xml.php';
		
		// Setup options
		$this->path 		= $options['path'];
		$this->id 	 		= $options['form'];
		$this->limitstart 	= $options['start'];
		$this->header		= $options['header'];
		$this->limit		= !empty($options['limit']) ? $options['limit'] : 100;
		
		// Initialize classes
		$this->db	= JFactory::getDbo();
		$this->xml	= new RSFormProBackupXML;
	}
	
	public function store() {
		$archive = new RSFormProTar($this->path);
		$db 	 = &$this->db;
		$done	 = false;
		
		// Set the data XML file number
		if ($this->limitstart >= $this->limit) {
			$xmlNumber = round($this->limitstart / $this->limit);
		} else {
			$xmlNumber = 0;
		}
		// Grab the submissions
		$query = $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_submissions'))
			  ->where($db->qn('FormId').'='.$db->q($this->id));
		
		$db->setQuery($query, $this->limitstart, $this->limit);
		if ($submissions = $db->loadObjectList('SubmissionId')) {
			$query->clear()
				  ->select('*')
				  ->from($db->qn('#__rsform_submission_values'))
				  ->where($db->qn('SubmissionId').' IN ('.RSFormProBackupHelper::qi(array_keys($submissions)).')');
			
			$db->setQuery($query);			
			$values = $db->loadObjectList();
			
			// Record the position
			$this->header = $archive->tell();
			
			// Add the 512-bytes dummy header
			$archive->addEmptyHeader();

			// Add the XML header & opening tag.
			$this->xml->addHeader();
			$this->xml->add('submissions');
			
			foreach ($values as $value) {
				if (empty($submissions[$value->SubmissionId]->values)) {
					$submissions[$value->SubmissionId]->values = array();
				}
				
				$submissions[$value->SubmissionId]->values[$value->FieldName] = $value->FieldValue;
			}
			
			foreach ($submissions as $submission) {
				$values = isset($submission->values) ? $submission->values : array();
				
				// No need for these.
				unset($submission->values, $submission->SubmissionId, $submission->FormId);
				
				$this->xml->add('submission');
				foreach ($submission as $property => $value) {
					$this->xml->add($property, $value);
				}
				
				// Add values
				$this->xml->add('values');
				foreach ($values as $property => $value) {
					$this->xml->add('value');
						$this->xml->add('fieldname',  	$property);
						$this->xml->add('fieldvalue', 	$value);
					$this->xml->add('/value');
				}
				$this->xml->add('/values');
				
				$this->xml->add('/submission');
			}
			
			// We've finished, no more submissions, add the closing tag.
			$this->xml->add('/submissions');
			
			// Add data to archive.
			$archive->add((string) $this->xml);
			
			// Compute size of current file in archive.
			$size = $archive->tell() - $this->header - 512;
			
			// Add missing padding.
			$archive->addPadding($size);
			
			// Go to header (so we can add missing header data).
			$archive->seek($this->header);
			
			// Add missing header data.
			$archive->addHeader($size, RSFormProBackupHelper::getHash($this->id).'-data-'.$xmlNumber.'.xml');
		} else {
			// Flag so that we can move on to the next form
			$done = true;
		}
		
		$archive->close();
		
		return (object) array(
			'done' 		=> $done,
			'header' 	=> $this->header // Position of empty header (so we'll know where to overwrite it)
		);
	}
}