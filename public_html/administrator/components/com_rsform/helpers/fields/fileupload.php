<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldFileUpload extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		
		$caption	 = $this->getProperty('CAPTION', '');
		$html = '<td>'.$caption.'</td><td><input type="file" /></td>';
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$name			= $this->getName();
		$id				= $this->getId();
		$attr			= $this->getAttributes();
		$type 			= 'file';
		$additional 	= '';
		
		// MAX_FILE_SIZE is no longer used, didn't provide anything useful
		
		// Start building the HTML input
		$html = '<input';
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type
		$html .= ' type="'.$this->escape($type).'"';
		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
				 ' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';
		
		return $html;
	}
	
	// @desc All upload fields should have a 'rsform-upload-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-upload-box';
		
		// Check for invalid here so that we can add 'rsform-error'
		if ($this->invalid) {
			$attr['class'] .= ' rsform-error';
		}
		
		return $attr;
	}

	// process the upload file after form validation
	public function processBeforeStore($submissionId, &$post, &$files) {
		if (!isset($files[$this->name]))
		{
			return false;
		}

		$actualFile = $files[$this->name];
		if ($actualFile['error'] != UPLOAD_ERR_OK)
		{
			return false;
		}

		$prefixProperty = $this->getProperty('PREFIX', '');
		$destination    = $this->getProperty('DESTINATION', '');

		// Prefix
		$prefix = uniqid('') . '-';
		if (strlen(trim($prefixProperty)) > 0)
		{
			$prefix = RSFormProHelper::isCode($prefixProperty);
		}

		// Path
		$realpath = realpath($destination . DIRECTORY_SEPARATOR);
		if (substr($realpath, -1) != DIRECTORY_SEPARATOR)
		{
			$realpath .= DIRECTORY_SEPARATOR;
		}

		// Filename
		$file = $realpath . $prefix . $actualFile['name'];

		jimport('joomla.filesystem.file');

		// Upload File
		if (JFile::upload($actualFile['tmp_name'], $file, false, (bool) RSFormProHelper::getConfig('allow_unsafe')))
		{
			//Trigger Event - onBeforeStoreSubmissions
			JFactory::getApplication()->triggerEvent('rsfp_f_onAfterFileUpload', array(array('formId' => $this->formId, 'fieldname' => $this->name, 'file' => $file, 'name' => $prefix . $actualFile['name'])));

			$db = JFactory::getDbo();
			// Add to db (submission value)
			$query = $db->getQuery(true)
				->insert($db->qn('#__rsform_submission_values'))
				->set($db->qn('SubmissionId') . ' = ' . $db->q($submissionId))
				->set($db->qn('FormId') . ' = ' . $db->q($this->formId))
				->set($db->qn('FieldName') . ' = ' . $db->q($this->name))
				->set($db->qn('FieldValue') . ' = ' . $db->q($file));

			$db->setQuery($query)
				->execute();
		}
	}
}